$(document).ready(function(){
    $('#unassignFolderModal').on('show.bs.modal', function(e) {
        var folderId = $(e.relatedTarget).data('folderid');
        var templateId =$(e.relatedTarget).data('id')
        var templateName =$(e.relatedTarget).data('name')
        $("#unassignFolderModal #unassignFolderId").val(folderId);
        $("#unassignFolderModal #unassignTemplateId").val(templateId);
        $("#unassignFolderModal #unassignTemplateName").val(templateName);
    });

    $(document).on('click','#unassignFolderModal .submit',function () {
        var folderId =  $("#unassignFolderId").val();
        var templateId =  $("#unassignTemplateId").val();
        var templateName =  $("#unassignTemplateName").val();
        var assignTempalteErrorMessage = ".unassignTempalteErrorMessage";
        $(assignTempalteErrorMessage).css('display','none');

        $.ajax({
            type:"Post",
            url : base_url+'/dashboard/company/folder/unassignTemplateFolder',
            data:{'folderId':folderId,'templateId':templateId,'templateName':templateName},
            success: function (response) {
                if (response.status == true){
                    $(response.buttonConfig[1]).replaceWith(response.buttonConfig[0]);
                    $(response.buttonConfig[2]).replaceWith(response.buttonConfig[3]);
                    $('#unassignFolderModal').modal('hide');
                }else if(response.status == false){
                    $(assignTempalteErrorMessage).css('display','block');
                    $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> '+response.message);
                }
            }
        });
    });
});
