<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Email Docket</title>
    <style type="text/css">
        p{
            line-height: 1.5em;
        }
        .preview {
            background: #fafafa;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .preview > div{
            padding: 15px;
        }
        .preview > div strong{
            display: block;
            margin-bottom: 5px;
        }
        /* -------------------------------------
            INLINED WITH https://putsmail.com/inliner
        ------------------------------------- */
        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important; }
            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important; }
            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important; }
            table[class=body] .content {
                padding: 0 !important; }
            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important; }
            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important; }
            table[class=body] .btn table {
                width: 100% !important; }
            table[class=body] .btn a {
                width: 100% !important; }
            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important; }}
        /* -------------------------------------
            PRESERVE THESE STYLES IN THE HEAD
        ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%; }
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%; }
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important; }
            .btn-primary table td:hover {
                background-color: #34495e !important; }
            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important; } }
    </style>
</head>
<body class="" style="background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;">
<table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background-color:#f6f6f6;width:100%;">
    <tr>
        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>
        <td class="container" style="font-family:sans-serif;font-size:14px;vertical-align:top;display:block;max-width:580px;padding:10px;width:580px;Margin:0 auto !important;">
            <div class="content" style="box-sizing:border-box;display:block;Margin:0 auto;max-width:580px;padding:10px;">
                <!-- START CENTERED WHITE CONTAINER -->
                <table class="main" style="border:1px solid #ddd; padding-bottom: 15px;padding-top: 15px;border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;"></td>
                        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;"><br/>
                            <strong>Dear {{ $recipient->receiver_full_name }},</strong><br/><br/>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">
                                <tr>
                                    <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0 0px 15px;">
                                            {{ $emailDocket->sender_name }}, from {{ $emailDocket->company_name }}, ABN: {{ $emailDocket->abn }},  has sent you a docket. Here are some details:
                                        </p>
                                        <div class="preview">
                                            <div>
                                                <strong>{{ $emailDocket->template_title }}</strong>
                                                Date: {{ \Carbon\Carbon::parse($emailDocket->created_at)->format('d M Y') }}<br/>
                                                Docket ID : {{ $emailDocket->formatted_id }}<br>
                                            </div>
                                        </div>
                                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%;">
                                            <tbody>
                                            <tr>
                                                <td align="center" style="font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;">
                                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto;">
                                                        <tbody>
                                                        <tr>
                                                            <td style="text-align: center;font-family:sans-serif;font-size:14px;vertical-align:top;background-color:#ffffff;border-radius:5px;text-align:center;background-color:#3498db;">
                                                                <a href="{{ url('docket/emailed',array($emailDocket->encryptedID(),$recipient->encryptedID())) }}" target="_blank" style="text-decoration:underline;background-color:#ffffff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:10px 25px;text-decoration:none;text-transform:capitalize;background-color:#3498db;border-color:#3498db;color:#ffffff;">
                                                                    View Docket
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0 0px 15px;">
                                            If you’re having trouble clicking the "View Docket" button, copy and paste the URL below into your web browser:
                                            <span style="display: block;font-size: 10px;word-break: break-all;">
                                                {{ url('docket/emailed',array($emailDocket->encryptedID(),$recipient->encryptedID())) }}
                                            </span>
                                        </p>
                                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%;">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <p style="margin-top: 0px;">Sincerely,<br/>
                                                        <strong>Record Time</strong></p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;"></td>
                    </tr>
                    <!-- END MAIN CONTENT AREA -->
                </table>
                <div class="footer" style="margin-top: 15px;clear:both;padding-top:10px;text-align:center;width:100%;">
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">
                        <tr>
                            <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">
                                <span style="display:block; font-weight:bold;color:#999999;font-size:12px;text-align:center;">
                                    Docket created with Record Time
                                </span>

                                <br/>
                                <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">
                                    &copy; Record Time PTY LTD. ABN: 99 604 582 649<br/>
                                    Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630</span><br/>
                                    <a href="https://recordtimeapp.com.au/" target="_blank">unsubscribe</a>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- END FOOTER -->
            </div>
        </td>
        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>
    </tr>
</table>
</body>
</html>
