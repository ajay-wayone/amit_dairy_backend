<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #4a90e2;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-body {
            padding: 30px;
            color: #333333;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4a90e2;
            text-align: center;
            margin: 20px 0;
        }

        .email-footer {
            background-color: #f4f6f9;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Welcome to Amit Dairy!</h2>
        </div>
        <div class="email-body">
            <p>Hello,</p>
            <p>Use the OTP below to complete your registration or login process:</p>
            <div class="otp-code">
                {{ $otp }}
            </div>
            <p>This OTP is valid for <strong>5 minutes</strong>.</p>
            <p>Please do not share this code with anyone for security reasons.</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} Amit Dairy. All rights reserved.
        </div>
    </div>
</body>

</html>
