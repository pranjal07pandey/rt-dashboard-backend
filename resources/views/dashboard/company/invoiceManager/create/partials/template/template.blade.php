<div id="step-1">
    <div id="form-step-0" role="form" data-toggle="validator">
        <div class="form-group">
            <strong>Invoice Template</strong>
            <select id="framework" class="form-control"  required  name="template" >
                @if(@$invoiceTemplates)
                    @foreach ($invoiceTemplates as $row)
                        <option value="{!! $row->invoiceInfo->id !!}" id="email">{!! $row->invoiceInfo->title !!}</option>
                    @endforeach
                @endif
            </select>
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
