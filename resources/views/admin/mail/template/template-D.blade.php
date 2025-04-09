<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>대한환경공학회 </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0;">
<table style="width:100%; max-width:650px; margin:0 auto; padding:0; border:1px solid #e2e2e2; border-collapse:collapse; border-spacing:0; text-align:center;
        font-family:'맑은 고딕',Arial,sans-serif; line-height:1.4; font-size:15px; font-weight:400; letter-spacing:-0.5px; word-break:keep-all; box-sizing:border-box; ">
    <tbody>
    <tr>
        <td style="padding:0;">
            <img src="{{ asset('assets/image/mail/mail_header_4.png') }}" alt="" style="display:block; margin:0 auto;" />
        </td>
    </tr>

    <tr>
        <td style="padding:15px 20px; font-size:24px; font-weight:400; background-color:#0096da; color:#fff; letter-spacing:-1.5px; ">
            {{ $mail->subject }}
        </td>
    </tr>

    <tr>
        <td style="padding:50px 50px 0; color:#4d4d4d; font-size:17px; font-weight:400; text-align: left;">
            {!! $mail->contents !!}
        </td>
    </tr>

    @include('admin.mail.template.common-template')

    <tr>
        <td style="padding:0; background-color: #3a3e49;">
            <img src="{{ asset('assets/image/mail/mail_footer.png') }}" alt="" style="display:block; margin:0 auto;" />
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>