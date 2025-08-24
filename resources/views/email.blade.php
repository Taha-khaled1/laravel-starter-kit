<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 20px;
            color: #333333;
        }

        .email-body h2 {
            color: #007bff;
            font-size: 20px;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.6;
        }

        .otp-code {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .email-footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <h1>{{ $subject }}</h1>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <h2>Hello, {{ $username }}!</h2>
            <p>{{ $massge }}</p>
            <p>Your One-Time Password (OTP) is:</p>
            <div class="otp-code">{{ $code }}</div>
            <p>This code will expire in 10 minutes. Please do not share it with anyone.</p>
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <p>If you did not request this OTP, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>

</html>