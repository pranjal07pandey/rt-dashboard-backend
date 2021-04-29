Dear {{ $emailInvoice->receiver_full_name }},

{{ $emailInvoice->sender_name }}, from {{ $emailInvoice->company_name }}, ABN: {{ $emailInvoice->abn }},  has sent you an invoice. Here are some details:

{{ $emailInvoice->template_title }}
Date: {{ $emailInvoice->formattedCreatedDate() }}
Invoice ID : {{ $emailInvoice->formatted_id }}

View Invoice, {{ url('invoice/emailed',array($emailInvoice->encryptedID(),$recipient->encryptedID())) }}
If you’re having trouble clicking the "View Invoice" button, copy and paste the URL below into your web browser:
{{ url('invoice/emailed',array($emailInvoice->encryptedID(),$recipient->encryptedID())) }}

Sincerely,
Record Time

Docket created with Record Time
&copy; Record Time PTY LTD. ABN: 99 604 582 649
Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 | M: 0421 955 630
unsubscribe, https://recordtimeapp.com.au