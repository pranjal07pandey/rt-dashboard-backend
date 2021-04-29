$(document).ready(function(){
    try{
        const slimSelect =  new SlimSelect({
            select: '#invoiceLabelModal .slim-select',
            addToBody: false,
            placeholder: 'Select Label'
        });

        $('#invoiceLabelModal').on('show.bs.modal', function(e) {
            $('#invoiceLabelModal .flash-message').css('display','none');
            $('#invoiceLabelModal .submit').html('Save');
            slimSelect.set([]);
            $('#invoiceLabelModal .form-group').removeClass('has-error');
            $('#invoiceLabelModal .invoice-company-id').html($(e.relatedTarget).attr('data-formatted-id'));
            $('#invoiceLabelModal #invoice-id').val($(e.relatedTarget).attr('data-id'));
            $('#invoiceLabelModal #invoice-type').val($(e.relatedTarget).attr('data-type'));
        });
        $(document).on('click', '#invoiceLabelModal .flash-message .close', function(){
            $('#invoiceLabelModal .flash-message').fadeOut();
        });
        $(document).on('click','#invoiceLabelModal .submit',function () {
            if(slimSelect.selected().length==0){
                $('#invoiceLabelModal .flash-message').fadeIn();
                $('#invoiceLabelModal .flash-message .message').html('Please select Invoice Label');
            }else {
                $('#invoiceLabelModal .flash-message').fadeOut();
                var id = $('#invoiceLabelModal #invoice-id').val();
                var type = $('#invoiceLabelModal #invoice-type').val();
                var value = slimSelect.selected();
                $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');

                $.ajax({
                    type: "Post",
                    url: base_url+'/dashboard/company/invoiceManager/invoices/labels/assign',
                    data: {'type':type,'id':id,'value':value},
                    success: function(response){
                        if (response.status == true){
                            $('.invoice-label-container #'+response.id+" ul").append(response.html);
                            $('#invoiceLabelModal').modal('hide');
                        }else if (response.status == false){
                            $('#invoiceLabelModal .submit').html('Save');
                            $('#invoiceLabelModal .flash-message').fadeIn();
                            $('#invoiceLabelModal .flash-message .message').html(response.message);
                        }
                    }
                });
            }
        });
    } catch(e) {}
});