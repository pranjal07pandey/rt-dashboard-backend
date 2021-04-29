Hi {{ $user->first_name." ".$user->last_name }},</strong><br/><br/>

You are receiving this email because we received a password reset request for your account.

Reset Password, {{ url('password/reset/'.$token) }}

If you did not request a password reset, no further action is required.

Sincerely,
Record Time

If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: {{ url('password/reset/'.$token) }}

&copy; Record Time PTY LTD. ABN: 99 604 582 649<br/>
Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630