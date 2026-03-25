<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ახალი შეტყობინება</title>
</head>
<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden;">

                <!-- HEADER -->
                <tr>
                    <td style="background:#0B3C5D; padding:20px; text-align:center;">
                        <h2 style="color:#ffffff; margin:0;">
                            📩 ახალი შეტყობინება
                        </h2>
                    </td>
                </tr>

                <!-- CONTENT -->
                <tr>
                    <td style="padding:25px;">

                        <p style="margin:0 0 10px;">
                            <strong>სახელი:</strong><br>
                            {{ $contact->name }}
                        </p>

                        <p style="margin:0 0 10px;">
                            <strong>ტელეფონი:</strong><br>
                            <a href="tel:{{ $contact->phone }}" style="color:#00C2A8; text-decoration:none;">
                                {{ $contact->phone }}
                            </a>
                        </p>

                        @if($contact->message)
                            <p style="margin:20px 0 5px;">
                                <strong>მესიჯი:</strong>
                            </p>

                            <div style="background:#f1f5f9; padding:15px; border-radius:8px; color:#333;">
                                {{ $contact->message }}
                            </div>
                        @endif

                        <!-- CTA -->
                        <div style="margin-top:25px; text-align:center;">
                            <a href="tel:{{ $contact->phone }}"
                               style="display:inline-block; background:#00C2A8; color:white; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold;">
                                📞 დარეკვა
                            </a>
                        </div>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#777;">
                        გაგზავნილია საიტიდან • {{ config('app.name') }}
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
