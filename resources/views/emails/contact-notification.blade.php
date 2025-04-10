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
            background-color: #007bff;
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
            color: #007bff;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            margin-right: 10px;
        }
        .message-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông Báo: Liên Hệ Mới</h1>
        </div>
        
        <div class="content">
            <h2>Thông tin liên hệ mới từ website</h2>
            
            <div class="info">
                <p><span class="label">Họ và tên:</span> {{ $contact->full_name }}</p>
                <p><span class="label">Email:</span> {{ $contact->email }}</p>
                
                @if($contact->phone_number)
                <p><span class="label">Số điện thoại:</span> {{ $contact->phone_number }}</p>
                @endif
                
                @if($contact->company)
                <p><span class="label">Công ty:</span> {{ $contact->company }}</p>
                @endif
                
                <p><span class="label">Thời gian:</span> {{ $contact->created_at->format('d/m/Y H:i:s') }}</p>
                
                @if($contact->contactReason)
                <p><span class="label">Lý do liên hệ:</span> {{ $contact->contactReason->name }}</p>
                @endif
            </div>
            
            <div class="message-box">
                <p><strong>Nội dung tin nhắn:</strong></p>
                <p>{{ $contact->message }}</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống website của bạn.</p>
        </div>
    </div>
</body>
</html> 