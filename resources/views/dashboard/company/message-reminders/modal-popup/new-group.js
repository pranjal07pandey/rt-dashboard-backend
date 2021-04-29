$(document).ready(function() {
    try {
        const slimSelectNewGroupModal = new SlimSelect({
            select: '#newGroupModal .slim-select',
            addToBody: false,
            placeholder: 'Select Employee'
        });

        $('#newGroupModal').on('show.bs.modal', function (e) {
            $('#newGroupModal .flash-message').css('display','none');
            $('#newGroupModal .submit').html('Create');
            slimSelectNewGroupModal.set([]);
            $('#newGroupModal .form-group').removeClass('has-error');
        });

        $(document).on('click', '#newGroupModal .flash-message .close', function(){
            $('#newGroupModal .flash-message').fadeOut();
        });

        $(document).on('click','#newGroupModal .submit',function () {
            var title = $('#newGroupModal #groupChatTitle').val();
            var employeeId =  slimSelectNewGroupModal.selected();;

            if(title.length==0){
                $('#newGroupModal .flash-message').fadeIn();
                $('#newGroupModal .flash-message .message').html('Please enter group title');
            }else if(employeeId.length==0){
                $('#newGroupModal .flash-message').fadeIn();
                $('#newGroupModal .flash-message .message').html('Please select Employee');
            }else{
                $('#newGroupModal .flash-message').fadeOut();
                $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');

                $.ajax({
                    type: "post",
                    data: {'employeeId':employeeId,'title':title, 'isGroup':1},
                    url:  base_url+'/dashboard/company/messages/create-group',
                    success: function(response){
                        if (response.status == true) {
                            $('#newGroupModal').modal('hide');
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
                                    }else if (response.status == false){
                                        alert(response.message);
                                    }
                                }
                            });
                        }else if (response.status == false){
                            $('#newGroupModal .submit').html('Create');
                            $('#newGroupModal .flash-message').fadeIn();
                            $('#newGroupModal .flash-message .message').html(response.message);
                        }
                    }
                });
            }
        })
    }catch(e) {}
});