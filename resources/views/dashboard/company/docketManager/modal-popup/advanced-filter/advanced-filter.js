$(document).on('change','.selectDocketTemplate',function () {
    console.log($(this).find(":selected").val())
    var docketTemplateId = $(this).find(":selected").val();
    $.ajax({
        type:"post",
        url : base_url+'/dashboard/company/docketBookManager/dockets/docketfieldName',
        data: {docketTemplateId:docketTemplateId},
        success:function (response) {
                $('.docketFieldNameSelect').html(response);

        }
    })

})