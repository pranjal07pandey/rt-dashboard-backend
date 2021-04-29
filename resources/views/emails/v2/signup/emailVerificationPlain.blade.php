Hi {{ $user->email }},

Thanks for signing up to Record Time.  Please click the link below to verify your email address.

Click here to verify, {{ url('/email_verification/'.$user->email_verification) }}

Sincerely,
Record Time

If you’re having trouble clicking the "Click here to verify" button, copy and paste the URL below into your web browser: {{ url('/email_verification/'.$user->email_verification) }}

&copy; Record Time PTY LTD. ABN: 99 604 582 649<
Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630
We have sent you this email because you have signed up for a Record Time account