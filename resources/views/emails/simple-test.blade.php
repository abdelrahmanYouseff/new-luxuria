<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #1a1a1a;">Test Email from Luxuria UAE</h1>

        <p>Hello {{ $userName ?? 'User' }},</p>

        <p>This is a simple test email to verify that our email system is working correctly.</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>Test Details:</h3>
            <ul>
                <li>Sent at: {{ date('Y-m-d H:i:s') }}</li>
                <li>From: {{ config('mail.from.address') }}</li>
                <li>To: {{ $userEmail ?? 'test@example.com' }}</li>
            </ul>
        </div>

        <p>If you receive this email, it means our email system is working properly.</p>

        <p>Best regards,<br>
        <strong>Luxuria UAE Team</strong></p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <p style="font-size: 12px; color: #666;">
            This is a test email. Please ignore if you received it by mistake.
        </p>
    </div>
</body>
</html>
