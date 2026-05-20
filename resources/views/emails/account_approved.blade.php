<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved</title>
</head>
<body style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f5f3ef; color: #111111; margin: 0; padding: 40px 20px; -webkit-font-smoothing: antialiased;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" max-width="600" style="max-width: 600px; background-color: #ffffff; border: 1px solid #ddd8d0; margin: 0 auto; padding: 40px 30px;">
        <tr>
            <td style="text-align: center; padding-bottom: 30px;">
                <h2 style="font-size: 20px; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; margin: 0; color: #111111;">
                    PureFit Apparel
                </h2>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 20px;">
                <h1 style="font-size: 28px; font-weight: 400; line-height: 1.2; margin: 0; color: #111111;">
                    Welcome to PureFit, {{ $user->name }}.
                </h1>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 25px; font-size: 15px; line-height: 1.6; color: #555555;">
                We are pleased to inform you that your request for a partner account has been approved by our administrators. To complete your activation and verify your email address, please use the following verification code:
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding: 25px 0; background-color: #fdfdfd; border: 1px dashed #ddd8d0; margin-bottom: 25px;">
                <span style="font-family: monospace; font-size: 32px; font-weight: 700; letter-spacing: 0.2em; color: #111111;">
                    {{ $code }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 25px; padding-bottom: 30px; font-size: 15px; line-height: 1.6; color: #555555;">
                You can enter this code on the verification page to activate your account. If the verification page is not open, you can click the button below to verify:
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-bottom: 30px;">
                <a href="{{ route('verify.show', ['email' => $user->email]) }}" style="display: inline-block; background-color: #111111; color: #ffffff; font-size: 11px; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; text-decoration: none; padding: 15px 35px; border-radius: 0; transition: background-color 0.2s;">
                    Verify Account
                </a>
            </td>
        </tr>
        <tr>
            <td style="border-t: 1px solid #ddd8d0; padding-top: 30px; font-size: 12px; line-height: 1.5; color: #888888; text-align: center;">
                If you did not request this, you can ignore this email safely.<br>
                &copy; {{ date('Y') }} PureFit Apparel. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
