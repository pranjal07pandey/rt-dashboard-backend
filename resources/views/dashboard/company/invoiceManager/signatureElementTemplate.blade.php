@if($item->invoice_field_category_id==9)
    <tr class="docketField" fieldId="{{ $item->id }}">
        <td colspan="2" style="border-top:0px; min-width: 708px;">
            <div class="shortTextDiv " id="shortTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList">
            <span>
                <a href="#" id="shortText" class="editable" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                    <div class="form-group">
                        <input id="title" type="text" disabled class="form-control" name="title" placeholder="Signature" value="{{ old('title') }}" required autofocus>
                    </div>
                    <button type="button" id="removeSignature" class="btn btn-raised btn-xs btn-danger deleteInvoiceComponentField" fieldId="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
                </div>
            </div><!--shortText field-->
        </td>
    </tr>
@endif