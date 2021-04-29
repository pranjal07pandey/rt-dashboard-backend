$(document).ready(function(){
    //export dockets
    $(document).on("change", ".rtDataTable th .checkbox", function () {
        if ($(this).is(":checked")){ $(".rtDataTable td .checkbox").prop('checked', true); }
        else{ $(".rtDataTable td .checkbox").prop('checked', false); }
    });

    $(document).on('click','.rtDataTableHeaderMenu #exportcsv',function () {
        if($('.rtDataTable .selectitem:checked').serialize()==""){ alert("Please Select Docket");}
        else{
            var url =  base_url+'/dashboard/company/docketBookManager/docket/exportAllDocket' +"?"+$('.rtDataTable .selectitem:checked').serialize();
            window.open(url,"_blank");
        }
    });

    $(document).on('click','.rtDataTableHeaderMenu #exportpdf',function () {
        if ($('.rtDataTable .selectitem:checked').serialize()==""){ alert("Please Select Docket");}
        else {
            var url =  base_url+'/dashboard/company/docketBookManager/docket/downloadZip' +"?"+$('.rtDataTable .selectitem:checked').serialize();
            window.open(url ,"_blank");

        }
    });

    //--per page item selection--//
    $(document).on('change', '.rtDataTableHeaderMenu .selectPaginate' ,function () {
        doStuff();
    });

    //searching
    var timer = null;
    $('.rtDataTableHeaderMenu #searchInput').keydown(function(){ clearTimeout(timer); timer = setTimeout(doStuff, 1000) });

    function doStuff(){
        var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL')+'?search=';
        $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
        const paginate = $('.rtDataTableHeaderMenu .selectPaginate').val();

        if($('.rtDataTableHeaderMenu #searchInput').val().length>0) {
            $.ajax({
                type: "GET",
                data: {items: paginate},
                url: url + $('#searchInput').val(),
                success: function (response) {
                    if (response == "") {
                    } else {
                        $(".datatable").html(response).show();
                    }
                }
            });
        }else{
            $.ajax({
                type: "GET",
                data:{data:"all",items:paginate},
                url: url,
                success: function(response){
                    if(response == ""){}
                    else{ $(".datatable").html(response).show(); }
                }
            });
        }
    }
});
