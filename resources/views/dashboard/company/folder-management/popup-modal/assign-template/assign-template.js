$(document).on('click','#assignTemplateModal .submit',function () {
    var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
    $(assignTempalteErrorMessage).css('display','none');

    var id = $('#assignTemplateId').val();
    var type = $('#assignTemplateType').val();
    var name = $('#assignTemplateName').val();
    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/assignTemplateFolder',
        data:{'folderId':id,'type':type,'templateId':name},
        success: function (response) {
            if (response.status == true){
                $('#assignTemplateModal').modal('hide');
            }else if(response.status == false){
                $(assignTempalteErrorMessage).css('display','block');
                $(assignTempalteErrorMessage).html('<i class="fa fa-exclamation-circle"></i> '+response.message);
            }
        }
    });
});