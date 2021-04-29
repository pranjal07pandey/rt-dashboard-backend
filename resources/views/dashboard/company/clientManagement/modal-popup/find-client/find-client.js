$(document).ready(function(){
    $('#findClientModal .search-box .form-group input[type="text"]').on("keyup input", function () {
        var inputVal = $(this).val().trim();
        var resultDropdown = $(this).siblings(".searchResult");
        if (inputVal.length >=3) {
            $.get(base_url + '/dashboard/company/clientManagement/clients/search/' + inputVal).done(function (data) {
                resultDropdown.html(data);
            });
        } else {
            resultDropdown.empty();
        }
    });

    $(document).on('click', '#findClientModal .clientRequested' ,function () {
        var requestedId = $(this).attr('userId');
        var id = $(this).attr('id');
        $(this).html('<span class="spinner" style="padding: 0 37px 0px 37px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>');
        $.ajax({
            type: "post",
            data:{'id':requestedId},
            url: base_url + "/dashboard/company/clientManagement/clients/request",
            success: function(response){
                if (response.status == true){
                    if (id ==response.id ){
                        $("#"+id).removeClass("pull-right btn btn-success  btn-sm btn-raised clientRequested");
                        $("#"+id).addClass("pull-right btn btn-secondary btn-sm btn-raised changeText");
                        $(".changeText").html('<i class="fa fa-check"></i> '+'REQUESTED');
                    }
                }else{
                    alert(response.message);
                }
            }
        });
    })
});