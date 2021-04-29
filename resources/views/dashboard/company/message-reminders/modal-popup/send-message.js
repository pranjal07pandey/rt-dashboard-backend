$(document).ready(function() {
    try {
        $(document).on('click','#newMessageModal .submit',function () {
            var message = $('#singleMessages').val();
            var employeeId = $('#chatUserId').val();
            if(message.length==0){
                $('#newMessageModal .flash-message').fadeIn();
                $('#newMessageModal .flash-message .message').html('Please enter your message');
            }else {
                $('#newMessageModal .flash-message').fadeOut();
                $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
            }
            $.ajax({
                type: "post",
                data: {'employeeId':employeeId, 'isGroup':0, 'message': message},
                url:  base_url+'/dashboard/company/messages/create-group',
                success: function(response){
                    if (response.status == true) {
                        console.log(response);
                        $('#newMessageModal').modal('hide');
                        $( ".messageUserList li a" ).removeClass('active');
                        $('.single_chat'+response.messageGroupID).remove();
                        $('.messageUserList').prepend(response.messageGroupHtml);
                        $('.single_chat'+response.messageGroupID).addClass('active');
                        $.ajax({
                            type: "post",
                            data:{'id':response.messageGroupID},
                            url: base_url + '/dashboard/company/messages/chatView',
                            success: function(response){
                                if (response.status == true){
                                    $(".viewChat").html(response.html);
                                    $('#myModalNewMessage').modal('hide');
                                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                                }else if (response.status == false){
                                    alert(response.message);
                                }
                            }
                        });
                    }else if (response.status == false){
                        $('#newGroupModal .submit').html('Send');
                        $('#newGroupModal .flash-message').fadeIn();
                        $('#newGroupModal .flash-message .message').html(response.message);
                    }
                }
            });
        });
    }catch(e) {}
});