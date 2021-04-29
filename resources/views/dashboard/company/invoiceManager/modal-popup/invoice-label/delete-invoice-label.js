$(document).ready(function() {
    $('#deleteInvoiceLabelModal').on('show.bs.modal', function(e) {
        $('#deleteInvoiceLabelModal .flash-message').css('display','none');
        $('#deleteInvoiceLabelModal .submit').html('Yes');

        $('#deleteInvoiceLabelModal .invoice-label-id').val($(e.relatedTarget).attr('data-id'));
        $('#deleteInvoiceLabelModal .type').val($(e.relatedTarget).attr('data-type'));
    });

    $(document).on('click','#deleteInvoiceLabelModal .submit',function () {
        var id      =   $('#deleteInvoiceLabelModal .invoice-label-id').val();
        var type    =   $('#deleteInvoiceLabelModal .type').val();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
            type: "Post",
            url: base_url + '/dashboard/company/invoiceManager/invoices/labels/delete',
            data: {'type': type, 'id': id},
            success: function (response) {
                if (response.status == true) {
                    $('.invoice-label-' + response.id).remove();
                    $('#deleteInvoiceLabelModal').modal('hide');
                } else if (response.status == false) {
                    $('#deleteInvoiceLabelModal .submit').html('Save');
                    $('#deleteInvoiceLabelModal .flash-message').fadeIn();
                    $('#deleteInvoiceLabelModal .flash-message .message').html(response.message);
                }
            }
        });
    });
});