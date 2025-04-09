<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Mail\ThankYouMail;
use Illuminate\Support\Facades\Mail;
use DB;
use Symfony\Component\Mime\Part\HtmlPart;
class ContactController extends Controller
{
    public function sendemail()
    {

        $emails = \DB::table('contacts')->pluck('email')->toArray();
        $batches = array_chunk($emails, 90);
        $logFile = storage_path('logs/email_log.txt'); // Đường dẫn file log

        foreach ($batches as $batch) {
            try {
                // Render nội dung email
                $databody = view('mail.emailudemy')->render();
                $data = [
                    'subject' => '🚀🚀 Khai giảng khóa học ERP từ SAP',
                ];

                Mail::send([], [], function ($message) use ($batch, $data, $databody) {
                    $message->to('tienle.sap@gmail.com')
                        ->bcc($batch)
                        ->subject($data['subject'])
                        ->html($databody);
                });

                // Ghi log email gửi thành công
                file_put_contents($logFile, "Success (BCC): " . implode(", ", $batch) . "\n", FILE_APPEND);
            } catch (\Exception $e) {
                // Nếu gửi BCC lỗi, thử gửi từng email với TO
                file_put_contents($logFile, "Error (BCC): " . $e->getMessage() . "\n", FILE_APPEND);
                foreach ($batch as $email) {
                    try {
                        Mail::send([], [], function ($message) use ($email, $data, $databody) {
                            $message->to($email)
                                ->subject($data['subject'])
                                ->html($databody);
                        });
                        file_put_contents($logFile, "Success (TO): " . $email . "\n", FILE_APPEND);
                    } catch (\Exception $e) {
                        file_put_contents($logFile, "Error (TO): " . $email . " - " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            }
        }

        return 'Emails have been sent with logging.';
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);
        // Lưu dữ liệu vào database
        Contacts::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'topic' => $request->topic,
        ]);

        // Send email
        Mail::queue(new ThankYouMail($request->email));
        return redirect()->back()->with('success', 'Thông tin của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ lại sớm nhất!');
    }

}
