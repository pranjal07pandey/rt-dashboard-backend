$(document).on('click','#createNewFolder .submit',function () {
    $('#createNewFolder .dashboardFlashsuccess').css('display','none');
    var root_id = $('#createNewFolder #folderSelect option:selected').val();
    var folder_name = $('#createNewFolder #folderNewName').val();

    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/newFolderCreate',
        data:{'rootId':root_id,name:folder_name},
        success: function (response) {
            if (response.status == true) {
                $('.directoryEmpty').css('display','none');
                if(root_id==0){
                    $(".rtTree").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">'+ response.totalItem +'</span></a><ul></ul> <div  class="editBtn" id="editBtnId" data-id="'+response.newFolderId+'" data-title="'+response.newFolderName+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
                }else{
                    var activeLink = ".rtTree a[id='" + root_id + "']";
                    $(".rtTree a.active").siblings("ul").append(' <li><a href="#" id="' + response.newFolderId + '">' + response.newFolderName + '<span style="    position: absolute;right: 4px;">'+ response.totalItem +'</span></a><ul></ul><div  class="editBtn" id="editBtnId" data-id="'+response.newFolderId+'" data-title="'+response.newFolderName+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div></li>');
                }
                $('#createNewFolder').modal('hide');
                $("#folderNewName").val("");
            }else if(response.name){
                var wrappermessage = ".messagesucess";
                $(wrappermessage).html(response["name"]);
                $('.dashboardFlashsuccess').css('display','block');
            }
            else{
                var wrappermessage = ".messagesucess";
                $(wrappermessage).html(response.message);
                $('.dashboardFlashsuccess').css('display','block');
            }
        }
    });
});