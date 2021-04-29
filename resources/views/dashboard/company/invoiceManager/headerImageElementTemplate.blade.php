@if($item->invoice_field_category_id==12)
    <tr class="docketField" fieldId="{{ $item->id }}">
        <td colspan="2" style="border-top:0px;min-width: 708px;">
            <div class="headerTextDiv " id="headerTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList">
                <span>
                    <a style="position: relative;" href="#" id="headerText" class="editable" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                    <div class="form-group" style="width: calc(100% - 47px);margin-top: 10px;">
                        <input disabled style=" cursor: pointer;" id="description"  type="text" class="form-control" name="description" placeholder="Header" value="{{ old('description') }}" required autofocus>
                    </div>
                    <button type="button"  id="removeLongText" class="btn btn-raised btn-xs btn-danger deleteInvoiceComponentField" fieldId="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
                </div>
            </div>
        </td>
    </tr>
@endif
@if($item->invoice_field_category_id==5)
    <tr class="docketField" fieldId="{{ $item->id }}">
        <td colspan="2" style="border-top:0px;min-width: 708px;">
            <div class="imageTextDiv " id="imageTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList">
                <span>
                    <a style="position: relative;" href="#" id="imageText" class="editable" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                    <div class="form-group" style="width: calc(100% - 47px);margin-top: 10px;">
                        <input disabled style="cursor: pointer;" id="description"  type="text" class="form-control" name="description" placeholder="Image" value="{{ old('description') }}" required autofocus>
                    </div>
                    <button type="button"  id="removeLongText" class="btn btn-raised btn-xs btn-danger deleteInvoiceComponentField" fieldId="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
                </div>
            </div>
        </td>
    </tr>
@endif