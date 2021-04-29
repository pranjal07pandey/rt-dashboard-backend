<br/>
<table  class="table table-striped">
    <tr style="font-weight: bold;">
        <td colspan="2">
            Payment Details
        </td>
    </tr>
    <tr>
        <td>
            Bank Name
        </td>
        <td>
            {{ $sentInvoice->paymentDetails->bank_name }}
        </td>
    </tr>
    <tr>
        <td>
            Account Name
        </td>
        <td>
            {{ $sentInvoice->paymentDetails->account_name }}
        </td>
    </tr>
    <tr>
        <td>
            BSB Number
        </td>
        <td>
            {{ $sentInvoice->paymentDetails->bsb_number }}
        </td>
    </tr>
    <tr>
        <td>
            Account Number
        </td>
        <td>
            {{ $sentInvoice->paymentDetails->account_number }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <strong>{{ $sentInvoice->paymentDetails->instruction }}</strong>
        </td>
    </tr>
    @if($sentInvoice->paymentDetails->additional_information)
        <tr>
            <td colspan="2">
                <strong>{{ $sentInvoice->paymentDetails->additional_information }}</strong>
            </td>
        </tr>
        @endif
        </tbody>
</table>