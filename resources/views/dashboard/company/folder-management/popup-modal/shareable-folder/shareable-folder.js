$(document).on('click','.shareableFolder', function (e) {
    $('.loadspin').css('display','block');
    $('.divList').remove();
    $('.errorMessageShareable').css('display','none');
    $('#shareableFolderModal').modal('show');
    var id = $(this).attr('data-editid')
    $('.shareableFolderId').val(id);
    $.ajax({
        type:"post",
        url: base_url+'/dashboard/company/folder/viewShareableData',
        data:{folder_id:id},
        success:function (response) {
            $('.shareableContain').html(response);
            $('.loadspin').css('display','none');
        }
    })



})


$(document).on('click','.submitUserShareable',function () {
    $('.loadspin').css('display','block');
    $(this).html('<span class="spinner" style="padding: 0 10px 0px 0px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span> Add');

    $('.errorMessageShareable').css('display','none');
    var folderId = $('.shareableFolderId').val();
    var email = $('.shareableEmail').val();
    var password = $('.sharePassword').val();
    $.ajax({
        type:"POST",
        url: base_url+'/dashboard/company/folder/saveShareableUsers',
        data: {folder_id:folderId,email:email,password:password},
        success: function (response) {
            if(response['status'] == false){
                $('.submitUserShareable').text('Add');
                $('.errorMessageShareable').css('display','block');
                $('.errorMessageShareable').text(response['message'])
            }else{
                $('.shareableContain').html(response);
                $('.submitUserShareable').text('Add');
            }
            $('.loadspin').css('display','none');
        }
    })
});

$(document).on('change','.sharefolderSelect',function () {
    $('.loadspin').css('display','block');
    $('.errorMessageShareable').css('display','none');
    var folderId = $('.shareableFolderId').val();
    var value = $(this).val();
    $.ajax({
        type:"POST",
        url:  base_url+'/dashboard/company/folder/updateShareableType',
        data: {folder_id:folderId,value:value},
        success: function (response) {
            if(response['status'] == false){
                $('.errorMessageShareable').css('display','block');
                $('.errorMessageShareable').text(response['message'])
            }else{

            }
            $('.loadspin').css('display','none');
        }
    })
});


$(document).on('click','.deleteShareableUsers', function (e) {
    $('#deleteShareableUsersModal').modal('show');
    var id = $(this).attr('data-shareableuserId')
    $('.shareableUserId').val(id);
})

$(document).on('click','.deleteShareableUser', function () {
    $('.loadspin').css('display','block');
    var shareableUserId =  $('.shareableUserId').val();
    $.ajax({
        type: "post",
        url: base_url+'/dashboard/company/folder/deleteShareableUser',
        data:{id:shareableUserId},
        success: function (response) {
            if(response['status'] == false){
                $('.errorMessageShareable').css('display','block');
                $('.errorMessageShareable').text(response['message'])
            }else{
                $('.shareableContain').html(response);
                $('#deleteShareableUsersModal').modal('hide');
            }
            $('.loadspin').css('display','none');
        }
    })
})

$(document).on('click','.editShareableUsers', function () {
    $('.errorMessageShareableUser').css('display','none');
    $('#editShareableUserModal').modal('show');
    var id = $(this).attr('data-shareableuserId')
    var email = $(this).attr('data-shareableuserEmail')
    $('.editshareableEmail').val(email)
    $('.updateShareableUserId').val(id)
})


$(document).on('click','.updateShareableUser',function () {
    $('.loadspin').css('display','block');
     var shareableUsersId  = $('.updateShareableUserId').val();
     var password  = $('.editshareablePassword').val();
        $.ajax({
            type: "post",
            url: base_url+'/dashboard/company/folder/updateShareableUser',
            data:{id:shareableUsersId,password:password},
            success: function (response) {
                if(response['status'] == false){
                    $('.errorMessageShareableUser').css('display','block');
                    $('.errorMessageShareableUser').text(response['message'])
                }else{
                    $('.shareableContain').html(response);
                    $('#editShareableUserModal').modal('hide');
                }
                $('.loadspin').css('display','none');
            }
        })

})


$(document).ready(function() {
    var clipboard = new ClipboardJS('.copyurl');
})

