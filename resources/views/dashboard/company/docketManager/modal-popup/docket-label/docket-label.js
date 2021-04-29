$(document).ready(function(){
    try{
        const slimSelect =  new SlimSelect({
            select: '#docketLabelModal .slim-select',
            addToBody: false,
            placeholder: 'Select Label'
        });
        $('#docketLabelModal').on('show.bs.modal', function(e) {
            $('#docketLabelModal .flash-message').css('display','none');
            $('#docketLabelModal .submit').html('Save');
            slimSelect.set([]);
            $('#docketLabelModal .form-group').removeClass('has-error');
            $('#docketLabelModal .docket-company-id').html($(e.relatedTarget).attr('data-formatted-id'));
            $('#docketLabelModal #docket-id').val($(e.relatedTarget).attr('data-id'));
            $('#docketLabelModal #docket-type').val($(e.relatedTarget).attr('data-type'));
        });
        $(document).on('click', '#docketLabelModal .flash-message .close', function(){
            $('#docketLabelModal .flash-message').fadeOut();
        });
        $(document).on('click','#docketLabelModal .submit',function () {
            if(slimSelect.selected().length==0){
                $('#docketLabelModal .flash-message').fadeIn();
                $('#docketLabelModal .flash-message .message').html('Please select Docket Label');
            }else {
                $('#docketLabelModal .flash-message').fadeOut();
                var id = $('#docketLabelModal #docket-id').val();
                var type = $('#docketLabelModal #docket-type').val();
                var value = slimSelect.selected();
                $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');

                $.ajax({
                    type: "Post",
                    url: base_url+'/dashboard/company/docketBookManager/dockets/labels/assign',
                    data: {'type':type,'id':id,'value':value},
                    success: function(response){
                        console.log(response);
                        if (response.status == true){
                            $('.docket-label-container #'+response.id+" ul").append(response.html);
                            $('#docketLabelModal').modal('hide');
                        }else if (response.status == false){
                            $('#docketLabelModal .submit').html('Save');
                            $('#docketLabelModal .flash-message').fadeIn();
                            $('#docketLabelModal .flash-message .message').html(response.message);
                        }
                    }
                });
            }
        });
    } catch(e) {}
});