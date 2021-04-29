Dear {{ $recipient['email'] }},

{{ $emailDocket->sender_name }}, from {{ $emailDocket->company_name }}, ABN: {{ $emailDocket->abn }},  has sent you a docket. Here are some details:

{{ $emailDocket->template_title }}
Date: {{ \Carbon\Carbon::parse($emailDocket->created_at)->format('d M Y') }}
Docket ID : {{ $emailDocket->formatted_id }}

View Docket, {{ url('docket/emailed',array($emailDocket->encryptedID())) }}
If you’re having trouble clicking the "View Docket" button, copy and paste the URL below into your web browser:
{{ url('docket/emailed',array($emailDocket->encryptedID())) }}

Sincerely,
Record Time

Docket created with Record Time
&copy; Record Time PTY LTD. ABN: 99 604 582 649
Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630
unsubscribe, https://recordtimeapp.com.au
