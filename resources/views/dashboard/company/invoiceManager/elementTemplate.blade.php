@if($item->invoice_field_category_id==1)
    <tr class="docketField" fieldId="{{ $item->id }}">
        <td colspan="2" style="border-top:0px;min-width: 708px;">
            <div class="shortTextDiv " id="shortTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList">
            <span>
                <a style="position: relative;" href="#" id="shortText" class="editable" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                    <div class="form-group">
                        <input id="title" type="text" disabled class="form-control" name="title" placeholder="Short Text" value="{{ old('title') }}" required autofocus>
                    </div>
                    {{--<button  data-toggle="modal" data-target="#deleteInvoiceField" data-id="{{ $item->id }}"  class="btn btn-raised btn-xs btn-danger">X</button>--}}
                    <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteInvoiceComponentField" fieldId="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
                </div>
            </div><!--shortText field-->
        </td>
    </tr>

@endif

@if($item->invoice_field_category_id==2)
    <tr class="docketField" fieldId="{{ $item->id }}">
        <td colspan="2" style="border-top:0px;min-width: 708px;">
        <div class="longTextDiv " id="longTextDiv" fieldId="{{ $item->id }}">
            <div class="horizontalList">
                <span>
                    <a style="position: relative;" href="#" id="longText" class="editable" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                <div class="form-group" style="width: calc(100% - 65px);margin-top: 10px;">
                    <input disabled style="height: 70px; background: #eee;cursor: pointer;" id="description"  type="text" class="form-control" name="description" placeholder="" value="{{ old('description') }}" required autofocus>
                </div>
                <button type="button" style="margin-top: 50px;" id="removeLongText" class="btn btn-raised btn-xs btn-danger deleteInvoiceComponentField" fieldId="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            </div>
        </div>
        </td>
    </tr>
@endif
