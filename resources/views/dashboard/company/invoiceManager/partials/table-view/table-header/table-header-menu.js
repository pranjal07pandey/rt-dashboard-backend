$(document).ready(function() {
    $(document).on('click','.rtDataTableHeaderMenu #exportcsvInvoice',function () {
        if ($('.selectitem:checked').serialize() == "") { alert("Please Select Invoice"); }
        else {
            var url =  base_url+'/dashboard/company/invoiceManager/exportInvoice?'+$('.rtDataTable .selectitem:checked').serialize();
            window.open(url,"_blank");
        }
    });

    $(document).on('click','.rtDataTableHeaderMenu #exportpdfInvoice',function () {
        if ($('.selectitem:checked').serialize()==""){ alert("Please Select Invoice"); }
        else {
            var url =  base_url+'/dashboard/company/invoiceManager/makePdfInvoice?'+$('.rtDataTable .selectitem:checked').serialize();
            window.open(url,"_blank");
        }
    });

    $(document).on('change', '.selectPaginateInvoice' ,function () { searchInvoice(); });

    var timer = null;
    $('.rtDataTableHeaderMenu #searchInputInvoice').keydown(function(){ clearTimeout(timer); timer = setTimeout(searchInvoice, 1000) });

    function searchInvoice() {
        var url = $('.rtDataTableHeaderMenu').attr('dataCurrentURL')+'?search=';
        $(".rtDataTable").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');

        const paginate = $('.rtDataTableHeaderMenu .selectPaginate').val();

        if($('#searchInputInvoice').val().length>0){
            $.ajax({
                type: "GET",
                data:{items:paginate},
                url: url + $('#searchInputInvoice').val(),
                success: function(response){
                    if(response == ""){}
                    else{ $(".datatable").html(response).show(); }
                }
            });
        }
        else {
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