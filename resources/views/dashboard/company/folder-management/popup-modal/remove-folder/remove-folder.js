$(document).on('click','.rtDataTableHeaderMenu #removeItemsFolder',function(){
    if ($('.rtDataTable .selectitem:checked').serialize()==""){ alert("Please select item that you want to remove."); }
    else {
        var docketData = {'docketId[]': [], 'emailDocketId[]': [], 'removeItemFolderId': '','invoiceId[]':[],'emailInvoiceId[]':[]};

        var folder_id = $('#removeItemFolderId').val();
        $('.rtDataTable .forDocket:checked').each(function (){ docketData['docketId[]'].push($(this).val()); });
        $('.rtDataTable .forEmailDocket:checked').each(function () { docketData['emailDocketId[]'].push($(this).val()); });
        $('.rtDataTable .forInvoice:checked').each(function() { docketData['invoiceId[]'].push($(this).val()); });
        $('.rtDataTable .forEmailInvoice:checked').each(function() { docketData['emailInvoiceId[]'].push($(this).val()); });
        docketData['folderId'] = folder_id;

        $('.loadspin').css('display','block');
        var id = $('#removeItemFolderId').val();

        $.ajax({
            type: "Post",
            url: base_url+'/dashboard/company/folder/removeItemsFolder',
            data: docketData,
            success: function(response){
                if (response.data == 0){ $('.boxContent .rtTree li #'+response.id + ' span').text(''); }
                else { $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')'); }
                $.ajax({
                    type:"Post",
                    url:base_url+'/dashboard/company/folder/viewFolderData',
                    cache: false,
                    data:{'folderId':id,'items':10},
                    success: function (response) {
                        $('.loadspin').css('display','none');
                        $(".viewFolder").html(response).show();
                    }
                });
            }
        });
    }
});

$(document).on('click','#removeFolderModal .submit',function (){
    var folderId = $('#removeFolderid').val();
    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/removeFolder',
        data:{'id':folderId},
        success: function (response) {
            if (response.status == true){
                if (response.foldercount == 0){ $('.directoryEmpty').css('display','block'); }
                $(".rtTree li a[id="+folderId+"]").parent('li').remove();
                $('#removeFolderModal').modal('hide');
            }
        }
    });
});