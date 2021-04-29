$(document).ready(function() {
    $('#deleteDocketLabelModal').on('show.bs.modal', function(e) {
        $('#deleteDocketLabelModal .flash-message').css('display','none');
        $('#deleteDocketLabelModal .submit').html('Yes');

        $('#deleteDocketLabelModal .docket-label-id').val($(e.relatedTarget).attr('data-id'));
        $('#deleteDocketLabelModal .type').val($(e.relatedTarget).attr('data-type'));
    });

    $(document).on('click','#deleteDocketLabelModal .submit',function () {
        var id      =   $('#deleteDocketLabelModal .docket-label-id').val();
        var type    =   $('#deleteDocketLabelModal .type').val();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
            type: "Post",
            url: base_url + '/dashboard/company/docketBookManager/dockets/labels/delete',
            data: {'type': type, 'id': id},
            success: function (response) {
                if (response.status == true) {
                    $('.docket-label-' + response.id).remove();
                    $('#deleteDocketLabelModal').modal('hide');
                } else if (response.status == false) {
                    $('#deleteDocketLabelModal .submit').html('Save');
                    $('#deleteDocketLabelModal .flash-message').fadeIn();
                    $('#deleteDocketLabelModal .flash-message .message').html(response.message);
                }
            }
        });
    });
});