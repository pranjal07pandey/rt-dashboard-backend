<div class="modal fade rt-modal" id="invoiceLabelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Assign Invoice Label</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @if($company->invoiceLabels->count()==0)
                            <p><i class="fa fa-exclamation-circle"></i> Please create a invoice label before marking it.</p>
                        @else
                            @include('dashboard.company.docketManager.modal-popup.flash-message')
                            <div class="form-group" style="margin-top:0px;">
                                <label class="control-label" for="title">Invoice Id : </label>
                                <strong class="invoice-company-id"></strong>
                                <input type="hidden" id="invoice-id" name="invoice-id" class="form-control">
                                <input type="hidden" id="invoice-type" class="form-control" >
                            </div>
                            <div class="form-group">
                                <label class="control-label">Invoice Label</label>
                                <select class="form-control slim-select" multiple required name="invoice_label_id[]">
                                    @if($company->invoiceLabels)
                                        @foreach($company->invoiceLabels as $row)
                                            <option value="{!! $row['id'] !!}">{!! $row['title'] !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if($company->invoiceLabels->count()==0)
                    <a type="submit" href="{{ route('companyInvoiceLabel') }}" class="btn btn-primary">Ok</a>
                @else
                    <button class="btn btn-primary submit">Save</button>
                @endif
            </div>
        </div>
    </div>
</div>