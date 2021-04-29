$(document).ready(function(){
    $('#deleteSentDocket').on('show.bs.modal', function(e) {
        $('#deleteSentDocket .flash-message').css('display','none');
        $('#deleteSentDocket .submit').html('Yes');
        $('#deleteSentDocket #deleteDocketIds').val($(e.relatedTarget).attr('data-id'));
        $('#deleteSentDocket #deleteDocketTypes').val($(e.relatedTarget).attr('data-type'));
    });

    $(document).on('click','#deleteSentDocket .submit',function () {
        var id      =   $('#deleteSentDocket #deleteDocketIds').val();
        var type    =   $('#deleteSentDocket #deleteDocketTypes').val();
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
            type: "Post",
            url : base_url+'/dashboard/company/docketBookManager/dockets/submitDeleteDocket',
            data: {'type': type, 'id': id},
            success: function (response) {
                if (response.status == true) {
                    $.map($('.selectitem'), function (el) {
                        if($(el).val() == id){
                            console.log($(el).parent().parent('tr').remove());

                        }
                    });
                    if(response.type == "create"){
                        $(".rtTree").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">'+ response.totalItem +'</span></a><ul></ul> <div   data-id="'+response.newFolderId+'" data-title="'+response.newFolderName+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
                    }else if(response.type == "update"){
                        var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
                    }

                    $('#deleteSentDocket').modal('hide');
                } else if (response.status == false) {
                    $('#deleteSentDocket .submit').html('Yes');
                    $('#deleteSentDocket .flash-message').fadeIn();
                    $('#deleteSentDocket .flash-message .message').html(response.message);
                }
            }
        });
    });


});
