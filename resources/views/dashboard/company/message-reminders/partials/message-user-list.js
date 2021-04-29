$(document).ready(function () {
    $(document).on('click','.clickToChat',function () {
        $( ".messageUserList li a" ).removeClass('active');
        var id =  $(this).attr('idAtt');
        $.ajax({
            type: "post",
            data:{'id':id},
            url: base_url + '/dashboard/company/messages/chatView',
            success: function(response){
                if (response.status == true){
                    $(".viewChat").html(response.html);
                    $('.single_chat'+id).addClass('active');
                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                }else if (response.status == false){
                    alert(response.message);
                }
            }
        });
    });
});