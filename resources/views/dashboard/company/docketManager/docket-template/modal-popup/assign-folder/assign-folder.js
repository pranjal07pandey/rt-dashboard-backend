$(document).ready(function() {
    $('#assignFolderModal').on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).data('id');
        var  title = $(e.relatedTarget).data('name');
        $(".assignFolderName").text(title);
        $('#templateId').val(id);
    });
    $(document).on('click','#assignFolderModal .submit',function () {
        var templateId = $('#templateId').val();
        var type = 1;
        var folderId = $('#assignFolderId').val();
        var name = $('.assignFolderName').text();
        var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
        $(assignTempalteErrorMessage).css('display','none');
        $.ajax({
            type:"Post",
            url : base_url+'/dashboard/company/folder/assignTemplateFolder',
            data:{'folderId':folderId,'type':type,'templateId':templateId,'name':name},
            success: function (response) {
                if (response.status == true){
                    $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
                    $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
                    $('#assignFolderModal').modal('hide');
                }else if(response.status == false){
                    $(assignTempalteErrorMessage).css('display','block');
                    $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                }
            }
        });
    });
});