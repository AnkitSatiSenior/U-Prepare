<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Received</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #212529;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; width: 100%; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); text-align: left;">
                    
                    <tr>
                        <td style="padding: 30px 40px; border-bottom: 1px solid #e9ecef; text-align: center; background-color: #ffffff;">
                            <img src="https://www.u-prepare.com/assets/img/updated-logo.png" alt="U-Prepare Logo" style="max-height: 60px; width: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 24px; font-weight: 600; color: #212529;">
                                Hello {{ $feedback->name }},
                            </h2>
                            
                            <p style="margin-top: 0; margin-bottom: 24px; font-size: 16px; line-height: 1.6; color: #495057;">
                                Thank you for reaching out to <strong>U-Prepare</strong>. We have successfully received your <strong>{{ $feedback->type }}</strong>. Our support team is currently reviewing your request and will get back to you shortly.
                            </p>

                            <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; padding: 25px; margin-bottom: 30px;">
                                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px; font-weight: 700; color: #212529; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Your Submission Details
                                </h3>
                                
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 15px; line-height: 1.6; color: #495057;">
                                    <tr>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef; width: 80px; font-weight: 600;">Name:</td>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef;">{{ $feedback->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef; font-weight: 600;">Email:</td>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef;"><a href="mailto:{{ $feedback->email }}" style="color: #0d6efd; text-decoration: none;">{{ $feedback->email }}</a></td>
                                    </tr>
                                    @if($feedback->phone_number)
                                    <tr>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef; font-weight: 600;">Phone:</td>
                                        <td style="padding: 6px 0; border-bottom: 1px solid #e9ecef;">{{ $feedback->phone_number }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="padding: 6px 0; font-weight: 600;">Subject:</td>
                                        <td style="padding: 6px 0;">{{ $feedback->subject ?? 'General ' . ucfirst($feedback->type) }}</td>
                                    </tr>
                                </table>
                            </div>

                            <h4 style="margin-top: 0; margin-bottom: 10px; font-size: 15px; font-weight: 600; color: #495057;">Message:</h4>
                            <div style="background-color: #e9ecef; border-left: 4px solid #0d6efd; padding: 20px; border-radius: 4px; font-size: 15px; line-height: 1.6; color: #212529; font-style: italic;">
                                {!! nl2br(e($feedback->message)) !!}
                            </div>

                            <p style="margin-top: 30px; margin-bottom: 0; font-size: 16px; line-height: 1.6; color: #495057;">
                                If you have any immediate questions, feel free to reply directly to this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0; font-size: 14px; color: #6c757d; font-weight: 500;">
                                &copy; {{ date('Y') }} The U-Prepare Support Team. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>