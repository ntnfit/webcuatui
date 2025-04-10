<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #3b82f6;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #777;
            border-radius: 0 0 8px 8px;
        }
        h1 {
            color: #fff;
            margin: 0;
        }
        h2 {
            color: #3b82f6;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .message-box {
            background-color: #f0f7ff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3b82f6;
            margin: 20px 0;
        }
        .signature {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cảm Ơn Bạn Đã Liên Hệ</h1>
        </div>
        
        <div class="content">
            <p>Xin chào {{ $contact->full_name }},</p>
            
            <p>Cảm ơn bạn đã liên hệ với chúng tôi. Đây là email xác nhận chúng tôi đã nhận được tin nhắn của bạn.</p>
            
            <div class="message-box">
                <p><strong>Nội dung tin nhắn của bạn:</strong></p>
                <p>{{ $contact->message }}</p>
            </div>
            
            <p>Chúng tôi sẽ xem xét và phản hồi tin nhắn của bạn trong thời gian sớm nhất.</p>
            
            <p>Nếu bạn có bất kỳ câu hỏi hoặc thông tin bổ sung nào, vui lòng không ngần ngại liên hệ với chúng tôi qua email này.</p>
            
            <div class="signature">
                <p>Trân trọng,</p>
                <p>HarryDev Team</p>
                <p>Email: ntnguyen0310@gmail.com</p>
                <p>Điện thoại: 0981710031</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động. Vui lòng không trả lời trực tiếp email này.</p>
        </div>
    </div>
</body>
</html> 