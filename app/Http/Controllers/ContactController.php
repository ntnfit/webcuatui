<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Models\ContactReason;
use App\Mail\ThankYouMail;
use Illuminate\Support\Facades\Mail;
use DB;
use Symfony\Component\Mime\Part\HtmlPart;
use Illuminate\Support\Facades\Validator;

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
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'contact_reason_id' => 'nullable|exists:contact_reasons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Nếu không có contact_reason_id, sử dụng ID mặc định hoặc tạo mới
        if (!$request->contact_reason_id) {
            $contactReason = ContactReason::firstOrCreate(
                ['name' => 'Website Contact'],
                ['name' => 'Website Contact']
            );
            $contact_reason_id = $contactReason->id;
        } else {
            $contact_reason_id = $request->contact_reason_id;
        }

        // Lưu thông tin liên hệ vào database
        $contact = Contacts::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'company' => $request->company,
            'message' => $request->message,
            'contact_reason_id' => $contact_reason_id,
        ]);

        // Gửi email thông báo
        $this->sendContactNotification($contact);

        return response()->json([
            'status' => 'success',
            'message' => 'Tin nhắn đã được gửi thành công!',
            'data' => $contact
        ]);
    }

    /**
     * Gửi email thông báo khi có liên hệ mới
     */
    private function sendContactNotification(Contacts $contact)
    {
        $to = 'ntnguyen0310@gmail.com'; // Email admin
        $subject = 'Liên hệ mới từ website';
        
        $data = [
            'contact' => $contact,
            'subject' => $subject
        ];

        // Gửi email sử dụng class Mail của Laravel
        Mail::send('emails.contact-notification', $data, function ($message) use ($to, $subject, $contact) {
            $message->to($to)
                ->subject($subject)
                ->replyTo($contact->email, $contact->full_name);
        });

        // Gửi email phản hồi tự động cho người dùng
        $userSubject = 'Cảm ơn bạn đã liên hệ';
        $userData = [
            'contact' => $contact,
            'subject' => $userSubject
        ];

        Mail::send('emails.contact-autoreply', $userData, function ($message) use ($contact, $userSubject) {
            $message->to($contact->email, $contact->full_name)
                ->subject($userSubject);
        });
    }
}
