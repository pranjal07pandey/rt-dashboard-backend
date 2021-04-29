$(document).ready(function(){
    $(".docketFieldIsHidden").on("click", function () {
        var docketFieldId = $(this).attr("data");
        var checked = 0;
        if ($(this).is(':checked')) {
            checked = 1;
        }
    
        $.ajax({
            type: "POST",
            url: appURL + "dashboard/company/docketBookManager/designDocket/docketFieldIsHidden",
            data: {"data": checked, "docketFieldId": docketFieldId},
            success: function (msg) {
                if (msg == "Invalid attempt!") {
                    alert(msg);
                }
            }
        });
    });
});

