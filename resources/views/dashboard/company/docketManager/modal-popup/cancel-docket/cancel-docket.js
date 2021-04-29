$(document).ready(function(){
    $('#cancelDocketModal').on('show.bs.modal', function(e) {
        $('#cancelDocketModal .flash-message').css('display','none');
        $('#cancelDocketModal .submit').html('Yes');

        $('#cancelDocketModal #cancelid').val($(e.relatedTarget).attr('data-id'));
        $('#cancelDocketModal #canceltype').val($(e.relatedTarget).attr('data-type'));
    });

    $(document).on('click','#cancelDocketModal .submit',function () {
        var id      =   $('#cancelDocketModal #cancelid').val();
        var type    =   $('#cancelDocketModal #canceltype').val();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
            type: "Post",
            url : base_url+'/dashboard/company/docketBookManager/dockets/cancelDocket',
            data: {'type': type, 'id': id},
            success: function (response) {
                if (response.status == true) {
                    var parentTr    =   $("#docketLabelIdentify" + response.id).parents("tr");

                    parentTr.addClass("cancelled");
                    parentTr.children('td').eq(4).html("");
                    parentTr.children('td').eq(4).append('<span class="label label-danger">Cancelled</span>');
                    parentTr.children().last('td').children().eq(1).hide();
                    parentTr.children().last('td').children().eq(2).hide();
                    parentTr.children().last('td').children().eq(3).hide();
                    $('#cancelDocketModal').modal('hide');
                } else if (response.status == false) {
                    $('#cancelDocketModal .submit').html('Yes');
                    $('#cancelDocketModal .flash-message').fadeIn();
                    $('#cancelDocketModal .flash-message .message').html(response.message);
                }
            }
        });
    });
});