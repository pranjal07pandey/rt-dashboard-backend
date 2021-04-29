$(document).ready(function () {
    $(".messageUserList li:first-child a").addClass('active');
    if($(".messageUserList li:first-child a").hasClass('active')) {
        var id = $(".messageUserList li:first-child a").attr('idatt');
        $.ajax({
            type: "post",
            data: {'id': id},
            url: base_url + '/dashboard/company/messages/chatView',
            success: function (response) {
                if (response.status == true){
                    $(".viewChat").html(response.html);
                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                    $('#myModalNewMessage').modal('hide');
                }else if (response.status == false){
                    alert(response.message);
                }
            }
        });
    }
});