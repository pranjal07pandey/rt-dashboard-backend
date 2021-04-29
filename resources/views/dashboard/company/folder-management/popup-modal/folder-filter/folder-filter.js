$('#MyModalFolderFilter').on('show.bs.modal', function(e) {
    var type = $("#folderSelect").find(":checked").val();
    $(".spinnerCheck").css('display','block');
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/showFolderAdvanceFilter',
        data: {'type':type},
        success: function(response){
            $("#folderContentFilter").html(response).show();
            $(".filterempolyeess").chained(".filtercompanys");
            $( ".dateInput" ).datepicker({ dateFormat: 'dd-mm-yy'});
            $(".spinnerCheck").css('display','none');
        }
    });
});

$(document).on('change','#folderSelect',function(){
    var type = $(this).find(":checked").val();
    $(".spinnerCheck").css('display','block');
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/showFolderAdvanceFilter',
        data: {'type':type},
        success: function(response){
            $("#folderContentFilter").html(response).show();
            $(".filterempolyeess").chained(".filtercompanys");
            $( ".dateInput" ).datepicker({ dateFormat: 'dd-mm-yy'});
            $(".spinnerCheck").css('display','none');
        }
    });
});


$(document).on('click','.submitData',function () {
    var type = $('.filterType').val();
    var company = $('#filtercompany').val();
    var employee = $('#filterempolyees').val();
    var itemName = $('#itemName').val();
    var itemId = $('#itemId').val();
    var itemDateCat = $('#itemDateCat').val();
    var itemDateFrom = $('.itemDateFrom').val();
    var itemDateto = $('.itemDateto').val();
    var invoiceable = $(".invoiceableFilter").attr("checked") ? 1 : null;
    var emailFilter = $('#emailFilter').val();
    var folder_id = $('#removeItemFolderId').val();
    var data = $('.folderFilter .docketFieldNameSelect div div input');
    var docketFieldValue = [];
    $(data).each(function(index) {
        if($(this).val() == ""){
            docketFieldValue[index] = $(this).attr('name').split("[")[1].split(']')[0]+"-"+"null";
        }else{
            docketFieldValue[index] = $(this).attr('name').split("[")[1].split(']')[0]+"-"+$(this).val();
        }
    });
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/advanceSearch/AdvanceFilter',
        data: {'type':type,'company':company,'employee':employee,'TemplateId':itemName,'id':itemId,'date':itemDateCat,'from':itemDateFrom,'to':itemDateto,'invoiceable':invoiceable,'email':emailFilter,'folder_id':folder_id,'docketFieldValue':docketFieldValue},
        success: function(response){
            $("#folderAdvanceFilterView").html(response).show()
            $("#folderAdvanceFilterFooterView").css('display','none');
            $('#MyModalFolderFilter').modal('hide');

        }
    });


});
