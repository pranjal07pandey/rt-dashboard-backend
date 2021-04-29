$(document).on("click", '.message-form-wrapper .submit', function(event) {
    var id =  $('.message-form-wrapper #groupId').val();
    var  message= $('.message-form-wrapper #chatMessage').val();
    if(message.length==0){
      alert('Please write your message first.');
    }
    $.ajax({
        type: "post",
        data:{'id':id, 'message':message },
        url: base_url + '/dashboard/company/messages',
        success: function(response){
            if (response.status == true){
                $( ".messageList" ).last().append(response.html);
                var groupLi = $('.single_chat'+response.groupId).parent('li')[0].outerHTML;
                $('.single_chat'+response.groupId).remove();
                $('.messageUserList').prepend(groupLi);
                $('.single_chat'+response.groupId).addClass('active');
                $('.message-form-wrapper #chatMessage').val('');
                $('.messages').scrollTop($('.messages')[0].scrollHeight);

                $('.viewChat .seenUser'+response.senderUserId).hide();
                $('.viewChat .seenUser'+response.senderUserId).last().fadeIn();
            }else if (response.status == false){
                alert(response.message);
            }
        }
    });
});

$(document).on("keyup", '.message-form-wrapper #chatMessage', function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        $(".message-form-wrapper .submit" ).trigger( "click" );
    }
});
