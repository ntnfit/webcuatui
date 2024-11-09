<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Jobs\SendThankYouEmail;
class ContactController extends Controller
{
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
         SendThankYouEmail::dispatch($request->email);
        return redirect()->back()->with('success', 'Thông tin của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ lại sớm nhất!');
    }
}
