$(document).on('click','.rtDataTableHeaderMenu #moveFolder',function () {
    if ($('.rtDataTable .selectitem:checked').serialize()==""){ alert("Please select the item you want to move.");}
    else {
        $('#moveFolderItemModal').modal('show', function() {});
        $('#moveFolderItemModal .re-mover').val($(this).attr('type'));

        $("#moveFolderItemModal #folderLabel").html("");
        $("#moveFolderItemModal .spinerSubDocket").css("display", "block");
        $.ajax({
            type: "GET",
            url: base_url+'/dashboard/company/folder/getFolderStru',
            success: function(response){
                $("#moveFolderItemModal .spinerSubDocket").css("display", "none");
                $("#moveFolderItemModal #folderLabel").html(response);
            }
        });
    }
});

$(document).on('click','#moveFolderItemModal .submit',function(){
    var typeValue =$('.re-mover').val();


    if (typeValue == 1){
        //1-alldockets, 2-allinvoice,3 sentdockets, 4 received dockets, 5 emailed dockets, 6 sent invoice, 7 receivedInvoice, 8 emailedInvoice
        if ($('#folder_status').val()==1){ moveItems('dockets', 'all'); }
        else if($('#folder_status').val()==2){ moveItems('invoices', 'all'); }
        else if ($('#folder_status').val() == 3){ moveItems('dockets', 'sent'); }
        else if ($('#folder_status').val() == 4){ moveItems('dockets', 'received'); }
        else if ($('#folder_status').val() == 5){ moveItems('dockets', 'emailed'); }
        else if ($('#folder_status').val() == 6){ moveItems('invoices', 'sent'); }
        else if ($('#folder_status').val() == 7){ moveItems('invoices', 'received'); }
        else if ($('#folder_status').val() == 8){ moveItems('invoices', 'emailed'); }
    }
    else if (typeValue == 2){
        moveItems('folder', 'all');
    }
});

function moveItems(type, section){
    let docketData = {
        'docketId[]': [],
        'emailDocketId[]': [],
        'folderId': '',
        'invoiceId[]': [],
        'emailInvoiceId[]': []
    };
    let folder_id = $("#moveFolderItemModal #folderFramework").val();
    $('.rtDataTable .forDocket:checked').each(function() { docketData['docketId[]'].push($(this).val()); });
    $('.rtDataTable .forEmailDocket:checked').each(function() { docketData['emailDocketId[]'].push($(this).val()); });

    $('.rtDataTable .forInvoice:checked').each(function() { docketData['invoiceId[]'].push($(this).val()); });
    $('.rtDataTable .forEmailInvoice:checked').each(function() { docketData['emailInvoiceId[]'].push($(this).val()); });
    docketData['folderId'] = folder_id;

    if(type == "folder"){
        var removeItemFolderId = $('#removeItemFolderId').val();
        $.ajax({
            type:"Post",
            url: base_url+'/dashboard/company/folder/saveFolderItems',
            data: docketData,
            success: function (response) {
                $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');
                $('#moveFolderItemModal').modal('hide');
                $.ajax({
                    type:"Post",
                    url:base_url+'/dashboard/company/folder/viewFolderData',
                    cache: false,
                    data:{'folderId':removeItemFolderId,'items':10},
                    success: function (response) {
                        $('.loadspin').css('display','none');
                        $(".viewFolder").html(response).show();
                    }
                });
            }
        });
    }else {
        $.ajax({
            type: "Post",
            url: base_url + '/dashboard/company/folder/saveFolderItems',
            data: docketData,
            success: function (response) {
                var test = $('.boxContent .rtTree li #' + response.id + ' span').text('(' + response.data + ')');
                $('#moveFolderItemModal').modal('hide');
                var manager = "docketBookManager";
                if (type == "invoices") {
                    manager = "invoiceManager";
                }
                $.ajax({
                    type: "GET",
                    data: {data: "all"},
                    url: base_url + '/dashboard/company/' + manager + '/' + type + '/' + section + '?search=',
                    success: function (response) {
                        if (response == "") {
                        } else {
                            $(".menuli").addClass("active");
                            $(".jstree-anchor").removeClass("jstree-clicked");
                            $(".jstree-node").removeClass("jstree-open");
                            $(".jstree-node").addClass("jstree-closed");
                            $(".datatable").html(response).show();
                        }
                    }
                });
            }
        });
    }
}