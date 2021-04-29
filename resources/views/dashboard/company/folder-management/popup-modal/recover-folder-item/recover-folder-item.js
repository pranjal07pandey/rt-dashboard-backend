$('#recoverFolderItem').on('show.bs.modal', function(e) {
    var id = $(e.relatedTarget).data('id');
    var type = $(e.relatedTarget).data('type');
    $('#removeFolderId').val(id);
    $('#removeFolderType').val(type);

});

$(document).on('click','.submitRecoverFolderItem',function () {
    var id = $('#removeFolderId').val();
    var type = $('#removeFolderType').val();
    var folderId = $('#removeItemFolderId').val()
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/recoverFolderItem',
        data: {'type':type,'id':id,'folderId':folderId},
        success: function(response){

            $.map($('.selectitem'), function (el) {
                if($(el).val() == id){
                 $(el).parent().parent('tr').remove();
                }
            });
            $('#recoverFolderItem').modal('hide');
            var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
        }
    });

});










