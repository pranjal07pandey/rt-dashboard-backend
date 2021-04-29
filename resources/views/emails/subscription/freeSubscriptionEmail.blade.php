<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Subscription Notification</title>
    <style type="text/css">
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
                <table class="main" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;padding: 0px 20px;">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper" style="font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px 20px 0px;">
                            <center><img src="{{ asset('assets/beta/images/logoWhite.jpg') }}" width="100px"></center><br/>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;"><br/>
                            <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">Hi {{ $email }},</p>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">
                                <tr>
                                    <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">Welcome to Record Time!</p>
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">
                                            You have selected a Free User Plan. You will now have limited access to the following features:
                                        </p>
                                        <ul>
                                            <li>Send 5 Dockets a month</li>
                                            <li>Receive Unlimited Dockets</li>
                                            <li>Unlimited Docket Templates</li>
                                            <li>Unlimited Invoice Templates</li>
                                            <li>Send 1 Invoice a Month</li>
                                            <li>Receive Unlimited Invoices</li>
                                        </ul>
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">
                                            Please upgrade your plan to send unlimited dockets and invoices every month.
                                        </p>
                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;">
                                            <a href="{{ url('redirect/'.\Illuminate\Support\Facades\Crypt::encryptString(url('dashboard/company/profile/subscription/upgrade'))) }}" target="_blank" style="text-decoration:none;background: #003f67;color: #fff;padding: 10px 20px;border-radius: 5px;">Click here to upgrade your Plan</a>
                                        </p>

                                        <p style="font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;margin-bottom:15px;margin-top:35px;">
                                            Regards,<br/>
                                            The Record Time Team
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- START FOOTER -->
                <div class="footer" style="clear:both;padding-top:10px;text-align:center;width:100%;">
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;">
                        <tr>
                            <td class="content-block" style="font-family:sans-serif;font-size:14px;vertical-align:top;color:#999999;font-size:12px;text-align:center;">
                                <span class="apple-link" style="display:block;margin-bottom: 10px;font-weight:bold;color:#999999;font-size:12px;text-align:center;">Download the Record Time App</span>
                                <a href="https://itunes.apple.com/au/app/record-time/id971035780?mt=8" target="_blank" title="Apple Store Download Link">
                                    <img src="{{ asset('assets/beta/images/iOSBadge.png') }}" alt="Download Record Time for iPhone Devices" style="max-width: 130px;">
                                </a>

                                <a href="https://play.google.com/store/apps/details?id=com.webtecsolutions.recordtimeapp" target="_blank"  title="Google Play Store Download Link">
                                    <img src="{{ asset('assets/beta/images/androidBadge.png') }}" alt="Download Record Time for Android Devices" style="max-width: 130px;">
                                </a>
                                <br/><br/>
                                <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">copyright 2018. Record Time PTY LTD. ABN: 99 604 582 649</span><br/>
                                <center>
                                    <a href="https://recordtime.com.au/terms-of-use" target="_blank">Terms of Use </a>
                                    <a href="https://recordtime.com.au/license-agreement" target="_blank">Licence Agreement</a></li>
                                </center><br/>
                                <span class="apple-link" style="color:#999999;font-size:12px;text-align:center;">We have sent you this email because you have signed up for a Record Time account.</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- END FOOTER -->
                <!-- END CENTERED WHITE CONTAINER -->
            </div>
        </td>
        <td style="font-family:sans-serif;font-size:14px;vertical-align:top;">&nbsp;</td>
    </tr>
</table>
</body>
</html>