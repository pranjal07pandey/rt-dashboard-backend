$(document).on('click','#updateFolderModal .submit',function () {
    var folderId = $('#editIdFolder').val();
    var folderName = $('#editNameFolder').val();
    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/updateFolder',
        data:{'id':folderId,'title':folderName},
        success: function (response) {
            if (response.status == true){
                $(".rtTree li a[id="+response.id+"]").html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
                $(".rtTree li .editBtn").data('title',response.title);
            }else{
                alert("Invalid action.");
            }
            if ( $(".rtTree li a[id="+response.id+"]").hasClass('active')){
                $('.rtTabHeader ul li h4').html(response.title+'<span style="position: absolute;right: 4px;">'+response.totalItems+'</span>');
            }
            $('#updateFolderModal').modal('hide');
        }
    });
});