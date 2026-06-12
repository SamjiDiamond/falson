<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{env('APP_NAME')}} - Transaction Alert</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f8fafc" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%; border-collapse: collapse; border-radius: 16px; overflow: hidden; background-color: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <!-- HEADER -->
                    <tr>
                        <td align="center" bgcolor="#0f172a" style="padding: 32px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                            <img src="{{env('APP_LOGO')}}" alt="{{env('APP_NAME')}} Logo" width="80" height="80" style="border-radius: 16px; display: block; margin-bottom: 16px; object-fit: contain;" />
                            <div style="color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: 0.5px; font-family: sans-serif;">{{env('APP_NAME')}}</div>
                            <div style="color: #94a3b8; font-size: 12px; margin-top: 4px; font-family: sans-serif;">Transaction Alert &bull; {{date('D, d M Y - h:i a')}}</div>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding: 32px 32px 16px 32px;">
                            <div style="font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 8px; font-family: sans-serif;">
                                Dear {{$data['user_name']}},
                            </div>
                            <div style="font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; font-family: sans-serif;">
                                A payment was successfully completed on your account. Please see below the details of the transaction.
                            </div>

                            <!-- TRANSACTION DETAILS CARD -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f1f5f9" style="border-radius: 12px; padding: 24px; margin-bottom: 24px; border-collapse: separate;">
                                <tr>
                                    <td align="center" style="padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                                        <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 1px; display: block; margin-bottom: 4px; font-family: sans-serif;">Amount</span>
                                        <span style="font-size: 28px; color: #10b981; font-weight: 800; font-family: sans-serif;">₦{{number_format($data['amount'])}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 16px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; font-family: sans-serif;">Description</td>
                                                <td style="padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; font-family: sans-serif;">{{$data['description']}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; font-family: sans-serif;">Reference Number</td>
                                                <td style="padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; font-family: monospace;">{{$data['ref']}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; font-family: sans-serif;">Initial Balance</td>
                                                <td style="padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; font-family: sans-serif;">₦{{number_format($data['i_wallet'])}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 500; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 8px; font-family: sans-serif;">Wallet Balance</td>
                                                <td style="padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 700; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 8px; font-family: sans-serif;">₦{{number_format($data['f_wallet'])}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- NOTE/ALERT -->
                            @if(!empty($email_note))
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fffbeb" style="border-left: 4px solid #f59e0b; border-radius: 6px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <div style="font-size: 12px; font-weight: 700; color: #b45309; margin-bottom: 4px; font-family: sans-serif;">Important Note</div>
                                        <div style="font-size: 12px; color: #b45309; line-height: 1.5; font-family: sans-serif;">{{$email_note}}</div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- CONTACT SUPPORT -->
                            <div style="font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 16px; font-family: sans-serif;">
                                If you have any questions or did not authorize this transaction, please contact support immediately at
                                <a href="mailto:{{$support_email}}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">{{$support_email}}</a>.
                            </div>

                            <div style="font-size: 14px; color: #334155; font-weight: 600; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 16px; font-family: sans-serif;">
                                Thanks for choosing us,<br/>
                                <span style="color: #10b981; font-weight: 700; font-size: 16px;">{{env('APP_NAME')}} Team</span>
                            </div>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" bgcolor="#f1f5f9" style="padding: 32px; border-top: 1px solid #e2e8f0;">
                            <div style="font-size: 11px; color: #94a3b8; line-height: 1.6; margin-bottom: 16px; font-family: sans-serif;">
                                This mail was sent with ❤ from {{env('APP_NAME')}} to <span style="font-weight: 600;">{{$email}}</span>.
                            </div>

                            <!-- SOCIAL MEDIA BADGES -->
                            <table border="0" cellspacing="0" cellpadding="0" align="center">
                                <tr>
                                    <td align="center">
                                        <a href="https://www.facebook.com/share/CeEB9FfEcJ321fN2/?mibextid=WC7FNe" target="_blank" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #1877f2; color: #ffffff; font-family: sans-serif;">Facebook</a>
                                        <a href="https://www.tiktok.com/@planetf_ng?_t=8m8V7Bp4oLu&_r=1" target="_blank" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #010101; color: #ffffff; font-family: sans-serif;">TikTok</a>
                                        <a href="https://x.com/planetf_ng1?s=11&t=3ZNS95UUIBAkS5ZKl-NLGQ" target="_blank" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #0f172a; color: #ffffff; font-family: sans-serif;">X</a>
                                        <a href="https://wa.me/+2348031230068" target="_blank" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #25d366; color: #ffffff; font-family: sans-serif;">WhatsApp</a>
                                        <a href="https://whatsapp.com/channel/0029VaFQ40b6BIEk0xhylQ0I" target="_blank" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #00a884; color: #ffffff; font-family: sans-serif;">Channel</a>
                                        <a href="mailto:{{$support_email}}" style="display: inline-block; padding: 6px 12px; margin: 4px; font-size: 11px; font-weight: 700; text-decoration: none; border-radius: 20px; background-color: #ea4335; color: #ffffff; font-family: sans-serif;">Email</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
