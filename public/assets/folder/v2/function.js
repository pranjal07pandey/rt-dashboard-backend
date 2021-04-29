function  dearch() {
    $(document).ready(function() {
        var timers = null;
        $('#searchFolderInputs').keydown(function(){
            clearTimeout(timers);
            timers = setTimeout(doStuffs, 1000)
        });

        function doStuffs() {
            $(".datatable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
            if($('#searchFolderInputs').val().length>0){
                $.ajax({
                    type: "get",
                    url: base_url+'/dashboard/company/folder/searchFolderItems?search=' + $('#searchFolderInputs').val()+ '&'+'folderId='+ $('#removeItemFolderId').val(),
                    success: function(response){
                        if(response == ""){

                        }else{
                            $(".searchViewItems").html(response).show();

                        }
                    }
                });
            }else{
                $.ajax({
                    type: "get",
                    data:{data:"all",'folderId':$('#removeItemFolderId').val()},
                    url: base_url+'/dashboard/company/folder/searchFolderItems?search=',
                    success: function(response){
                        if(response == ""){

                        }else{
                            $(".searchViewItems").html(response).show();

                        }
                    }
                });



            }
        }
    });

}


//refactored
$(document).on('click','#moveFolder',function () {
    if ($('.selectitem:checked').serialize()==""){
        alert("Please Select Docket");
    }else {

        // console.log($(this).attr('type'));
//                var idss = $('.selectitem:checked').serialize();
//                $("#docketSeralizeValue").val(idss);
        $('#moveFolderModel').modal('show', function() {});
        $('.re-mover').val($(this).attr('type'));

        $("#folderLabel").html("");
        $(".spinerSubDocket").css("display", "block");
        $.ajax({
            type: "GET",
            url: base_url+'/dashboard/company/folder/getFolderStru',
            success: function(response){
                $(".spinerSubDocket").css("display", "none");
                $("#folderLabel").html(response);

            }
        });



    }
});

//refactored
$(document).on('click','#submitFolderItems',function(){
    var docketData = { 'docketId[]' : [],'emailDocketId[]':[], 'folderId':'','invoiceId[]':[],'emailInvoiceId[]':[]};
    var emailDocketData={'emailDocketId[]':[]};
    var typeValue =$('.re-mover').val();
    alert(typeValue);

    var e = document.getElementById("folderFramework");
    var folder_id = e.options[e.selectedIndex].value;
    var removeItemFolderId = $('#removeItemFolderId').val();

    $('.forDocket:checked').each(function() {
        docketData['docketId[]'].push($(this).val());
    });
    $('.forEmailDocket:checked').each(function() {
        docketData['emailDocketId[]'].push($(this).val());
    });
    $('.forInvoice:checked').each(function() {
        docketData['invoiceId[]'].push($(this).val());
    });
    $('.forEmailInvoice:checked').each(function() {
        docketData['emailInvoiceId[]'].push($(this).val());
    });
    docketData['folderId'] = folder_id;
    if (typeValue == 1){
        if ($('#folder_status').val()==1){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                   var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');
                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/docketBookManager/docket/allDockets?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        }else if($('#folder_status').val()==2){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');
                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/invoiceManager/allInvoice?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        }else if ($('#folder_status').val() == 3){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                  var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');
                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/docketBookManager/docket?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        }else if ($('#folder_status').val() == 4){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/docketBookManager/docket/received?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        else if ($('#folder_status').val() == 5){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/docketBookManager/docket/emailed?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        else if ($('#folder_status').val() == 6){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/invoiceManager/invoices?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        else if ($('#folder_status').val() == 7){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/invoiceManager/receivedInvoices?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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
        else if ($('#folder_status').val() == 8){
            $.ajax({
                type:"Post",
                url: base_url+'/dashboard/company/folder/saveFolderItems',
                data: docketData,
                success: function (response) {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                    $('#moveFolderModel').modal('hide');
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: base_url+'/dashboard/company/invoiceManager/emailedInvoices?search=',
                        success: function(response){
                            if(response == ""){

                            }else{
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




    }else if (typeValue == 2){

        $.ajax({
            type:"Post",
            url: base_url+'/dashboard/company/folder/saveFolderItems',
            data: docketData,
            success: function (response) {
                var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');
                $('#moveFolderModel').modal('hide');
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

    }



});

$(document).on('click','.jstree-anchor',function () {
    var id  = this.id;
    var paginate = $(this).val();
    $(".menuli").removeClass("active");
    $.ajax({
        type:"Post",
        url: base_url+'/dashboard/company/folder/viewFolderData',
        data:{'id':id,'items':paginate},
        success: function (response) {
            $(".viewFolder").html(response).show();

        }

    });

});

//refactored
$(document).ready(function(){
    $("ul.rtTree").rtTree({
        ajaxURL : "https://blog.teamtreehouse.com/writing-your-own-jquery-plugins",
        clickListItem : base_url+'/dashboard/company/folder',
        viewFolderData:base_url+'/dashboard/company/folder/viewFolderData',
        addFolder:base_url+'/dashboard/company/folder/newFolderCreate',
        createFolderSelect: base_url+'/dashboard/company/folder/createFolderSelect',
        ajaxCompletion : function test(response){
            var items = [];
            $.each(response.data, function() {
                items.push(  $(".rtTree a.active").siblings("ul").append('<li><a href="#" id="'+this.id+'" >'+this.name+' <span style="    position: absolute;right: 4px;">'+this.totalItems+'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'+this.id+'" data-title="'+this.name+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>' ));
            });

            $(".viewFolder").html(response).show();





            dearch();
        }




    });
    $(".filterempolyeess").chained(".filtercompanys");

});


$(document).ready(function () {
    $(".rtTree li::before").on('click', function(){
        alert("test");
//                    alert($(this).sibling('a').attr("id"));
    });

    $('.submitFolderItem').on('click',function () {
        $('.dashboardFlashsuccess').css('display','none');
        var root_id = $('#folderSelect option:selected').val();
        var folder_name = $('#folderNewName').val();
        $.ajax({
            type:"Post",
            url:base_url+'/dashboard/company/folder/newFolderCreate',
            data:{'rootId':root_id,name:folder_name},
            success: function (response) {
                if (response.status == true) {
                    $('.directoryEmpty').css('display','none');
                    if(root_id==0){
//                               $(".rtTree a").removeClass("active");
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
});

$(".rtTree").on("click",'.editBtn', function(){
    $('.editBtn>div').remove();
    console.log(this);
    var div = '<div class="folder-div"><ul class="divList" style="margin: 0;"><li><button style="border: none; width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderEdit" data-editId="'+$(this).data('id')+'" data-editName="'+$(this).data('title')+'">Edit</button></li><li><button style="border: none;    width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderRemove" data-removeId="'+$(this).data('id')+'"  data-title="'+$(this).data('title')+'">Remove Folder</button></li>        <li><button style="border: none;width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="assignTemplate" data-removeId="'+$(this).data('id')+'"  data-title="'+$(this).data('title')+'">Assign Template</button></li>       </ul></div>';
    $(this).append(div);

});



$("#assignTemplateName").chained("#assignTemplateType");

$(document).on('click', '.assignTemplate' ,function () {
    $('#assignTemplateModal').modal('show');
    var folderId = $(this).attr('data-removeId');
    var folderTitle = $(this).attr('data-title');
    $(".assignFolderName").text(folderTitle);
    $("#assignTemplateId").val(folderId);
    $('.assignTempalteErrorMessage').css('display','none');

});

$(document).on('click','#submitAssignFolder',function () {
    var assignTempalteErrorMessage = ".assignTempalteErrorMessage";
    $(assignTempalteErrorMessage).css('display','none');

    var id = $('#assignTemplateId').val();
    var type = $('#assignTemplateType').val();
    var name = $('#assignTemplateName').val();
    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/assignTemplateFolder',
        data:{'id':id,'type':type,'name':name},
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






$(document).on('click', '.folderEdit' ,function () {
    $('#updateFolderData').modal('show');
    var editIds = $(this).attr('data-editId');
    var editNames = $(this).attr('data-editName');
    $("#editNameFolder").val(editNames);
    $("#editIdFolder").val(editIds);
});



$(document).on('click','#submitDeleteFolder',function () {
    var folderId = $('#removeFolderid').val();
    $.ajax({
        type:"Post",
        url:base_url+'/dashboard/company/folder/removeFolder',
        data:{'id':folderId},
        success: function (response) {
            if (response.status == true){
                if (response.foldercount == 0){
                    $('.directoryEmpty').css('display','block');

                }
                //                    $('.editBtn>div').remove();
                $(".rtTree li a[id="+folderId+"]").parent('li').remove();
                $('#removeFolder').modal('hide');
            }


        }
    });

});

$(document).on('click','#UpdateFolder',function () {
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


            }
            if ( $(".rtTree li a[id="+response.id+"]").hasClass('active')){
                $('.rtTabHeader ul li h4').html(response.title+'<span style="position: absolute;right: 4px;">'+response.totalItems+'</span>');
            }
            $('#updateFolderData').modal('hide');
        }
    });

});




$(document).on('click','#removeItemsFolder',function(){

    if ($('.selectitem:checked').serialize()==""){
        alert("Please Select Docket");
    }else {
        var docketData = {'docketId[]': [], 'emailDocketId[]': [], 'removeItemFolderId': '','invoiceId[]':[],'emailInvoiceId[]':[]};
//                var emailDocketData = {'emailDocketId[]': []};
//                var typeValue = $('#inputValue').val();
//                var e = document.getElementById("folderFramework");
        var folder_id = $('#removeItemFolderId').val();
        $('.forDocket:checked').each(function () {
            docketData['docketId[]'].push($(this).val());
        });
        $('.forEmailDocket:checked').each(function () {
            docketData['emailDocketId[]'].push($(this).val());
        });
        $('.forInvoice:checked').each(function() {
            docketData['invoiceId[]'].push($(this).val());
        });
        $('.forEmailInvoice:checked').each(function() {
            docketData['emailInvoiceId[]'].push($(this).val());
        });
        docketData['folderId'] = folder_id;

        $('.loadspin').css('display','block');
        var id = $('#removeItemFolderId').val();

        $.ajax({
            type: "Post",
            url: base_url+'/dashboard/company/folder/removeItemsFolder',
            data: docketData,
            success: function(response){
                if (response.data == 0){
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('');

                } else {
                    var test = $('.boxContent .rtTree li #'+response.id + ' span').text('('+response.data + ')');

                }
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

$(document).on('click','#searchFolder',function(e){
    e.preventDefault();
    $('#searchFolderModel').modal('show');
});



$(document).on('click','#submitSearchFolderName',function () {
    var value = $('#searchFolderName').val();
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/searchFolder',
        data: {"name":value},
        success: function(response){
            if(response.status == true){
                $(".boxContent").html(response.detail).show();
                $('#searchFolderModel').modal('hide');
                $("ul.rtTree").rtTree({
                    ajaxURL : "https://blog.teamtreehouse.com/writing-your-own-jquery-plugins",
                    clickListItem :base_url+'/dashboard/company/folder',
                    viewFolderData:base_url+'/dashboard/company/folder/viewFolderData',
                    addFolder:base_url+'/dashboard/company/folder/newFolderCreate',
                    createFolderSelect:  base_url+'/dashboard/company/folder/createFolderSelect',
                    ajaxCompletion : function test(response){
                        var items = [];
                        $.each(response.data, function() {
                            items.push(  $(".rtTree a.active").siblings("ul").append('<li><a href="#" id="'+this.id+'">'+this.name+' <span style="    position: absolute;right: 4px;">'+this.totalItems+'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'+this.id+'" data-title="'+this.name+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>' ));
                        });

                        $(".viewFolder").html(response).show();

                        dearch();
                    }
                });

                $(".rtTree").on("click",'.editBtn', function(){
                    $('.editBtn>div').remove();
                    console.log(this);
                    var div = '<div class="folder-div"><ul class="divList" style="margin: 0;"><li><button style="border: none; width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderEdit" data-editId="'+$(this).data('id')+'" data-editName="'+$(this).data('title')+'">Edit</button></li><li><button style="border: none;    width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="folderRemove" data-removeId="'+$(this).data('id')+'"  data-title="'+$(this).data('title')+'">Remove Folder</button></li> <li><button style="border: none;    width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="assignTemplate" data-removeId="'+$(this).data('id')+'"  data-title="'+$(this).data('title')+'">Assign Template</button></li></ul></div>';
                    $(this).append(div);

                });

                $(document).on('click','#UpdateFolder',function () {
                    var folderId = $('#editIdFolder').val();
                    var folderName = $('#editNameFolder').val();
                    $.ajax({
                        type:"Post",
                        url:base_url+'/dashboard/company/folder/updateFolder',
                        data:{'id':folderId,'title':folderName},
                        success: function (response) {
                            if (response.status == true){
                                $(".rtTree li a[id="+response.id+"]").html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
                            }
                            if ( $(".rtTree li a[id="+response.id+"]").hasClass('active')){
                                $('.rtTabHeader ul li h4').html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
                            }
                            $('#updateFolderData').modal('hide');
                        }
                    });

                });

                $(".rtTree").on('mouseenter', '.editBtn', function() {

                    if($(this).siblings("a").hasClass("active")){

                    }else{
                        $(this).siblings("a").css("background","#f7f7f7");
                    }


                });

                $(".rtTree").on('mouseleave', '.editBtn', function() {

                    if($(this).siblings("a").hasClass("active")){

                    }else{
                        $(this).siblings("a").css("background","");
                    }


                });



            }

        }
    });
});


$(document).on('click','#MyModalFolderFilters',function(e){
    e.preventDefault();
    $('#MyModalFolderFilter').modal('show');
    $( ".dateInput" ).datepicker({ dateFormat: 'dd-mm-yy'});
});

$(document).on('change','#folderSelect',function(){
    var type = $(this).find(":checked").val();
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/showFolderAdvanceFilter',
        data: {'type':type},
        success: function(response){
            $("#folderContentFilter").html(response).show();
            $(".filterempolyeess").chained(".filtercompanys");
            $( ".dateInput" ).datepicker({ dateFormat: 'dd-mm-yy'});
        }
    });

});
// $('radio_tags').on('change', function() {
//     alert( $(this).find(":checked").val() );
// });

// $(document).on('click','.submitData',function () {
//    var type = $('.filterType').val();
//     $.ajax({
//         type: "Post",
//         url: base_url+'/dashboard/company/folder/showFolderAdvanceFilter',
//         data: {'type':type},
//         success: function(response){
//             $("#folderContentFilter").html(response).show();
//             $("#filterempolyees").chained("#filtercompany");
//         }
//     });
//
// });

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

    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/advanceSearch/AdvanceFilter',
        data: {'type':type,'company':company,'employee':employee,'TemplateId':itemName,'id':itemId,'date':itemDateCat,'from':itemDateFrom,'to':itemDateto,'invoiceable':invoiceable,'email':emailFilter,'folder_id':folder_id},
        success: function(response){
            $("#folderAdvanceFilterView").html(response).show()
            $("#folderAdvanceFilterFooterView").css('display','none');
            $('#MyModalFolderFilter').modal('hide');

        }
    });


});


$(document).ready(function() {
    var type  = $('#folderSelect').find('option:selected').val();
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/showFolderAdvanceFilter',
        data: {'type':type},
        success: function(response){
            $("#folderContentFilter").html(response).show();
            $(".filterempolyeess").chained(".filtercompanys");
        }
    });


});

$(document).ready(function() {
    $('#folderLabeling').on('show.bs.modal', function(e) {
        $('.dashboardFlashdanger').css('display','none');
        $(".multiselect-selected-text").remove();
        var id = $(e.relatedTarget).data('id');
        var type =$(e.relatedTarget).data('type');
        var prefix =$(e.relatedTarget).data('prefix');

        var company_id = $(e.relatedTarget).data('companyid');
        $(".submitFolderLabel").html('Save')

        if(type == 1){
            var  name = 'Docket';
        }else if (type == 2){
            var  name = 'Email Docket';
        }
        // else if (type == 3){
        //     var  name = 'Invoice';
        // }else if (type == 4){
        //     var  name = 'Email Invoice';
        // }
        $(".folderItemsNamefilter").text(' Assign '+ name+' Label');

        $("#itemsIdForLabel").val(id);
        $("#itemsIdForCompanyId").val(company_id);
        $("#itemsIdForLabelType").val(type);
        $(".itemIdForCompanyIdPrefix").html(prefix);


        $('#folderFramework').multiselect('destroy');
        $('#folderFramework').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth:'100%',
            nonSelectedText: 'Select '+ name +' Label',
        });

    });
});

$(document).on('click','.submitFolderLabel',function () {

    var id =  $('#itemsIdForLabel').val();
    var type =$('#itemsIdForLabelType').val();
    var value  = $('#folderFramework').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/folderLabelSave',
        data: {'type':type,'id':id,'value':value},
        success: function(response){
            if (response.status == true){
                $('div > .'+response.id).append(response.labelData);
                $('#folderLabeling').modal('hide');
            }else if (response.status == false){
                $('.submitFolderLabel').html('Save');
                var wrappermessagedanger = ".messagedanger";
                $(wrappermessagedanger).text(response.message);
                $('.dashboardFlashdanger').css('display','block');

            }


        }
    });

});



$(document).ready(function() {
    $('#invoicefolderLabeling').on('show.bs.modal', function(e) {
        $('.dashboardFlashdanger').css('display','none');
        $(".multiselect-selected-text").remove();
        var id = $(e.relatedTarget).data('id');
        var type =$(e.relatedTarget).data('type');
        var company_id =$(e.relatedTarget).data('companyid');
        $(".submitInvoiceFolderLabel").html('Save')
        console.log(type);
        if(type == 3){
            var  name = 'Invoice';
        }else if (type == 4){
            var  name = 'Email Invoice';
        }
        $(".folderItemsNamefilter").text(' Assign '+ name+' Label');
        $("#itemsInvoiceIdForLabel").val(id);
        $("#itemsInvoiceIdForLabelType").val(type);
        $("#itemsInvoiceIdIdForCompanyId").val(company_id);



        $('#folderInvoiceFramework').multiselect('destroy');
        $('#folderInvoiceFramework').multiselect({
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth:'100%',
            nonSelectedText: 'Select '+ name +' Label',
        });

    });
});

$(document).on('click','.submitInvoiceFolderLabel',function () {

    var id =  $('#itemsInvoiceIdForLabel').val();
    var type =$('#itemsInvoiceIdForLabelType').val();
    var value  = $('#folderInvoiceFramework').val();


    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/folderInvoiceLabelSave',
        data: {'type':type,'id':id,'value':value},
        success: function(response){
            if (response.status == true){
                $('div > .'+response.id).append(response.labelData);
                $('#invoicefolderLabeling').modal('hide');
            }else if (response.status == false){
                $('.submitInvoiceFolderLabel').html('Save');
                var wrappermessagedanger = ".messagedanger";
                $(wrappermessagedanger).text(response.message);
                $('.dashboardFlashdanger').css('display','block');

            }


        }
    });

});


$(document).ready(function() {
    $('#deleteLabel').on('show.bs.modal', function(e) {
        $('.dashboardFlashdangerDelete').css('display','none');
        var id = $(e.relatedTarget).data('id');
        var type = $(e.relatedTarget).data('type');
        $("#deleteAssignLabels").html('Yes');
        $("#delete_label").val(id);
        $("#delete_label_type").val(type);

        if(type == 1){
            var  name = 'Docket';
        }else if (type == 2){
            var  name = 'Email Docket';
        } else if(type == 3){
            var  name = 'Invoice';
        }else if (type == 4){
            var  name = 'Email Invoice';
        }
        $(".headerTextChange").text('Delete '+ name+' Label');

    });



});


$(document).on('click','#deleteAssignLabels',function () {
    var id =  $('#delete_label').val();
    var type =$('#delete_label_type').val();
    $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
    $.ajax({
        type: "Post",
        url: base_url+'/dashboard/company/folder/deleteAssignLable',
        data: {'type':type,'id':id},
        success: function(response){
            if(response.status == true){
                $('.'+response.id).remove();
                $('#deleteLabel').modal('hide');

            }else if (response.status == false){
                var wrappermessagedanger = ".messagedangerdelete";
                $(wrappermessagedanger).text(response.message);
                $('.dashboardFlashdangerDelete').css('display','block');


            }

        }
    });

});


$(document).on('click','#openCancelModal',function () {
    $('#cancelRtItemsModal').modal('show');
    var id = $(this).data('id');
    var type = $(this).data('type');
    $('#canceltype').val(type);
    $('#cancelid').val(id);
});

$(document).on('click','#cancelItems',function () {
    var id = $('#cancelid').val();
    var type = $('#canceltype').val();
    $.ajax({
        type: 'post',
        url : base_url+'/dashboard/company/folder/cancelRtItems',
        data: {'type':type, 'id':id},
        success: function (response) {
            if (response.status == true){
                jQuery.each( $('.datatable>tbody>tr>td>label .forDocket') , function( i, value) {
                    if (value.value == response.id) {
                        $(value).parents("tr").css({'background':'#f3f3f3','font-style':'italic'});
                        $(value).parents('tr').children().last("td").children().last("a").hide();
                        $(value).parents('tr').children().last("td").children().eq(2).hide();
                        $(value).parents('tr').children().last("td").children().eq(1).hide();
                        $(value).parents('tr').children().first("td").children().children().prop('disabled',true)
                        $(value).parents('tr').children().eq(5).children().hide();
                        console.log($(value).parents('tr').children().eq(5).append('<span class="label label-danger">Cancelled</span>'));


                    }
                });
                $('#cancelRtItemsModal').modal('hide');
            }
        }
    });


});


$(document).on('click','#folderPagination ul li a', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var page = url.split('page=')[1];

    var id = $('#removeItemFolderId').val();
    $.ajax({
        type: 'post',
        url : base_url+'/dashboard/company/folder/viewFolderData?items='+5+'&page='+page,
        data : {'page': page, 'folderId':id},
        success: function (response) {
            $(".viewFolder").html(response).show();
            dearch();
        }
    });
});
