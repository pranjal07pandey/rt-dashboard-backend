Dear {{ $recipient->first_name." ".$recipient->last_name }},

{{ $sentDocket->sender_name }}, from {{ $sentDocket->company_name }}, ABN: {{ $sentDocket->abn }},  has sent you a docket. Here are some details:

{{ $sentDocket->template_title }}
Date: {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d M Y') }}
Docket ID : {{ $sentDocket->formatted_id }}

View Docket, {{ url('docket',array($sentDocket->encryptedID(),$recipient->encryptedID())) }}
If you’re having trouble clicking the "View Docket" button, copy and paste the URL below into your web browser:
{{ url('docket',array($sentDocket->encryptedID(),$recipient->encryptedID())) }}

Sincerely,
Record Time

Docket created with Record Time
&copy; Record Time PTY LTD. ABN: 99 604 582 649
Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630
unsubscribe, https://recordtimeapp.com.au
