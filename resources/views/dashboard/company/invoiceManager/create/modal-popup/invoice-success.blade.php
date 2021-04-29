<div class="modal fade invoiceSuccess" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="dashboardFlash">
                    <h4><i class="fa fa-check-circle" aria-hidden="true" style="color: green;margin-right: 10px;"></i> Invoice sent successfully.</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button style="text-transform: capitalize;" type="button" class="btn btn-secondary" onclick="location.href ='{{ route('invoices.allInvoices') }}'">Close</button>
            </div>
        </div>
    </div>
</div>