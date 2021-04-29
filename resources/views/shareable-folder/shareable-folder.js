$(document).ready(function(){


    $("ul.folderRtTree").folderRtTree({
        clickListItem :base_url+'/folder/list',
        viewFolderData:base_url+'/folder/viewFolderData',
        ajaxCompletion : function test(response){
            var items = [];
            $.each(response.data, function() {
                items.push(  $(".folderRtTree a.active").siblings("ul").append('<li><a href="#" id="'+this.id+'" >'+this.name+' <span style="    position: absolute;right: 4px;">'+this.totalItems+'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'+this.id+'" data-title="'+this.name+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>' ));
            });

            window.location.hash = "&folderId="+$(".folderRtTree a.active").attr('id');
            $(".viewFolder").html(response).show();
            var timers = null;
            $('#searchFolderInputs').keydown(function(){
                clearTimeout(timers);
                timers = setTimeout(doStuffs, 1000)
            });
            dearch();

        }
    });

 //    var data = $(".folderRtTree a.active").attr('id');
 // window.location.hash = "&folderId="+data;


    //folder edit button action
    $(document).on('click','.folderRtTree .editBtn',function () {
        $('.editBtn>div').remove();
        var div = '<div class="folder-div"><ul class="divList" style="margin: 0;"> <li><button style="border: none;width: 100%;text-align: left; border-radius: 0 16px 16px 0;padding: 4px 12px;" class="downloadFolderPdf" data-FolderId="'+$(this).data('id')+'"  data-title="'+$(this).data('title')+'">Download Pdf</button></li></ul></div>';
        $(this).append(div);
    });
    $(document).on('mouseenter','.folderRtTree li a',function () {
        $(this).siblings('.editBtn').addClass('editBtnHover');
    });
    $(document).on('mouseleave','.folderRtTree li a',function () {
        $(this).siblings('.editBtn').removeClass('editBtnHover');
    });
    $(document).on('mouseleave','.folderRtTree .editBtn',function () {
        if($(this).siblings("a").hasClass("active")){}
        else{
            $(this).siblings("a").css("background","");
        }
    });
    $(document).on('mouseenter','.folderRtTree .editBtn',function () {
        if($(this).siblings("a").hasClass("active")){}
        else{ $(this).siblings("a").css("background","#f7f7f7"); }
    });
    window.onload = function(){
        var hideMe = document.getElementById('folder-div');
        document.onclick = function(e){
            if($(e.target).hasClass("folder-div") || $(e.target).hasClass("editBtn")){
            }else{
                $('.editBtn>div').remove();
            }
        };
    };

    $(document).on('click', '.folderEdit' ,function () {
        $('#updateFolderModal').modal('show');
        var editIds = $(this).attr('data-editId');
        var editNames = $(this).attr('data-editName');
        $("#updateFolderModal #editNameFolder").val(editNames);
        $("#updateFolderModal #editIdFolder").val(editIds);
    });

    $(document).click(function(evt){
        if(evt.target.className != 'editBtn'){
            $.map($(".editBtn"), function (el) {
                if($(el).children('.folder-div').hasClass('folder-div')){
                    $(el).children('.folder-div').remove()
                }else{

                }
            });
        }

    });





    $(document).on('click','.folderRtTree .folderRemove',function () {
        $('#removeFolderModal').modal('show');
        $('.editBtn>div').remove();
        var deleteId = $(this).attr('data-removeId');
        var folderTitle = $(this).attr('data-title');
        $(".deleteMessage").html("Are you sure you want to remove " +"<i class='material-icons' style='font-size: 16px;color: #eece4a;'> "+"folder"+ "</i> <b>"+folderTitle+ "</b>?");
        $("#removeFolderid").val(deleteId);
    });

    $("#assignTemplateName").chained("#assignTemplateType");

    $(document).on('click', '.folderRtTree .assignTemplate' ,function () {
        $('#assignTemplateModal').modal('show');
        var folderId = $(this).attr('data-removeId');
        var folderTitle = $(this).attr('data-title');
        $("#assignTemplateModal .assignFolderName").text(folderTitle);
        $("#assignTemplateId").val(folderId);
        $('.assignTempalteErrorMessage').css('display','none');
    });

    $(document).on('click', '.folderRtTree .downloadFolderPdf' ,function () {
        var folderId = $(this).attr('data-FolderId');
        var url = $(this).attr('href');
        $.ajax({
            type: 'post',
            url : base_url+'/folder/downloadPdf',
            data : {'folderId':folderId},
            success: function (response) {
                window.open(base_url+'/zipFile/'+response.messages, '_blank');
            }
        });

    });


    // $(".filterempolyeess").chained(".filtercompanys");

    //--per page item selection--//
    $(document).on('change', '.rtDataTableHeaderMenu .selectPaginateFolder' ,function () {
        doStuffs();
    });

    function doStuffs(){
        var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL') + '?search=';
        var folderID = $('.mainFolderId').val();
        const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();

        $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
        if ($('.rtDataTableHeaderMenu #searchFolderInputs').val().length > 0){
            $.ajax({
                type: "GET",
                data: {'folderId': folderID, items: paginate},
                url: url + $('#searchFolderInputs').val(),
                success: function (response) {
                    if (response == "") {
                    } else {
                        $(".searchViewItems").html(response).show();
                    }
                }
            });
        }
        else {
            $.ajax({
                type: "GET",
                data: {data: "all", 'folderId': folderID, items: paginate},
                url: url,
                success: function (response) {
                    if (response == "") {}
                    else { $(".searchViewItems").html(response).show(); }
                }
            });
        }
    }

    $(document).on('click','#searchFolder',function(e){
        e.preventDefault();
        $('#searchFolderModel').modal('show');
    });

    $(document).on('click','.rtDataTable #folderPagination ul li a', function (e) {
        e.preventDefault();

        //
        var page = $(this).text();

        // if(typeof(url) != "undefined"){
        //     var page = url.split('page=')[1];
        // }
        // const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
        const paginate =  10;
        var search  =  "";
        var id = $('.mainFolderId').val();
        var url = $(this).attr('href');
        $.ajax({
            type: 'post',
            url : base_url+'/folder/viewFolderData?items='+paginate+'&page='+page,
            data : {'page': page, 'folderId':id},
            success: function (response) {

                $(".viewFolder").html(response).show();
                window.location.hash = "&folderId="+id+"&search="+search+"&items="+paginate+'&page='+page;


                dearch();
            }
        });
    });

    $(document).on('click','#MyModalFolderFilters',function(e){
        e.preventDefault();
        $('#MyModalFolderFilter').modal('show');
        $( ".dateInput" ).datepicker({ dateFormat: 'dd-mm-yy'});
    });


    if(typeof(location.hash.split('#')[1]) !== "undefined"){
        var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];
        if(typeof(location.hash.split('#')[1].split('&')[2])!== "undefined"){
            var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
        }else{
            var searchValue = "";
        }

        if(typeof(location.hash.split('#')[1].split('&')[3])!== "undefined"){
            var items = location.hash.split('#')[1].split('&')[3].split('=')[1];
            var page = location.hash.split('#')[1].split('&')[4].split('=')[1];
        }else{
            var items = "";
            var page = "";

        }

        if(items != "" && page != ""){
            var searchValueitems =items
            var searchValuepage =page
        }else{
            var searchValueitems =10
            var searchValuepage =1
        }

        if(searchValue == ""){
            var searchValueSend = "";
        }else{
            var searchValueSend = searchValue;
        }
        console.log(searchValueSend);
        console.log(searchValue);
        $.ajax({
            type: "post",
            url: base_url+'/folder/viewFolderReload?search=' + searchValueSend+ '&'+'folderId='+ folderId,
            data : {'page': searchValuepage, 'items':searchValueitems},
            success: function (response) {
                $.ajax({
                    type: 'post',
                    url : base_url+'/folder/searchFolderById',
                    data : {'id': folderId},
                    success: function (response) {
                        $(".boxContent").html(response.detail).show();
                        $("ul.folderRtTree").folderRtTree({
                            clickListItem :base_url+'/folder/list',
                            viewFolderData:base_url+'/folder/viewFolderData',
                            ajaxCompletion : function test(response){
                                var items = [];
                                $.each(response.data, function() {
                                    items.push(  $(".folderRtTree a.active").siblings("ul").append('<li><a href="#" id="'+this.id+'">'+this.name+' <span style="    position: absolute;right: 4px;">'+this.totalItems+'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'+this.id+'" data-title="'+this.name+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>' ));
                                });
                                window.location.hash = "&folderId="+$(".folderRtTree a.active").attr('id');
                                $(".viewFolder").html(response).show();
                            }
                        });
                        if($('.boxContent .folderRtTree .active').attr('id')  == folderId ){
                            $('.boxContent .folderRtTree li ul').css('display','none');
                            if($('.boxContent .folderRtTree .active').parent('li').first().parent('ul').attr('class')=='rtTree'){
                                $('.boxContent .folderRtTree .active').parent('li').first().children('ul').css('display','');
                            }else{
                                jQuery.each( $('.boxContent .folderRtTree .active').parents('ul') , function( i, value) {
                                    $(value).css('display', '');
                                })
                                $('.boxContent .folderRtTree .active').parent('li').children('ul').css('display','')

                            }
                        }
                    }
                });

                $(".viewFolder").html(response).show();
                // $(".searchViewItems").html(response).show();
                $('.rtMenuSearch').val(searchValue)
            }
        });
        console.log("we");
    }

    $(document).on('click','.rtDataTable #reloadFolderPagination ul li a', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var page = url.split('page=')[1];
        // const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
        const paginate = 10;
        console.log(location.hash.split('#')[1]);

        var folderId = location.hash.split('#')[1].split('&')[1].split('=')[1];
        if(typeof(location.hash.split('#')[1].split('&')[2])!== "undefined"){
            var searchValue = location.hash.split('#')[1].split('&')[2].split('=')[1].replace("%20", " ");
        }else{
            var searchValue = "";

        }


        if(searchValue == ""){
            var searchValueSend = "";
        }else{
            var searchValueSend = searchValue;
        }

        console.log("data");


        $.ajax({
            type: 'post',
            url : base_url+'/folder/viewFolderReload?items='+paginate+'&page='+page,
            data : {folderId:folderId,search:searchValueSend},
            success: function (response) {
                $(".viewFolder").html(response).show();
                $('.rtMenuSearch').val(searchValue)
                window.location.hash = "&folderId="+folderId+"&search="+searchValueSend+"&items="+paginate+'&page='+page;
                dearch();
            }
        });
    });



    $(document).on('click','.rtDataTable #searchFolderPagination ul li a', function (e) {

        e.preventDefault();
        var url = $(this).attr('href');

        var page = url.split('page=')[1];

        const paginate = $('.rtDataTableHeaderMenu .selectPaginateFolder').val();
        var id = $('.mainFolderId').val();
        var search  =  $('.rtMenuSearch').val();
        $.ajax({
            type: 'get',
            url: base_url+'/dashboard/company/folder/searchFolderItems?search=' + search+ '&'+'folderId='+ id,
            data : {'page': page, 'items':paginate},
            success: function (response) {
                $(".searchViewItems").html(response).show();
                $('.rtMenuSearch').val(search)
                window.location.hash = "&folderId="+id+"&search="+search+"&items="+paginate+'&page='+page;

                dearch();
            }
        });
    });

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
                        url: base_url+'/dashboard/company/folder/searchFolderItems?search=' + $('#searchFolderInputs').val()+ '&'+'folderId='+ $('.mainFolderId').val(),
                        success: function(response){
                            if(response == ""){

                            }else{
                                window.location.hash = '&'+location.hash.split('#')[1].split('&')[1]+"&search="+$('#searchFolderInputs').val();
                                $(".searchViewItems").html(response).show();

                            }
                        }
                    });
                }else{
                    $.ajax({
                        type: "get",
                        data:{data:"all",'folderId':$('.mainFolderId').val()},
                        url: base_url+'/dashboard/company/folder/searchFolderItems?search=',
                        success: function(response){
                            if(response == ""){

                            }else{

                                window.location.hash = '&'+location.hash.split('#')[1].split('&')[1]+"&search=";
                                $(".searchViewItems").html(response).show();

                            }
                        }
                    });



                }
            }
        });

    }















});


