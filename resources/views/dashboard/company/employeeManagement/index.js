$(document).ready(function() {
    $('#employeeListDatatable').on('change', 'tbody input.receiveDocketCopy', function () {
        $.ajax({
            type: "POST",
            url: base_url + '/dashboard/company/employeeManagement/employees/receiveDocketCopy',
            data: {"data": $(this).attr("data"), "status": (this.checked) ? 1 : 0},
            success: function (msg) {
                if(msg!=""){
                    alert(msg);
                }
            }
        });
    });

    $('#employeeListDatatable').DataTable({ "order": [[ 4, "asc" ]] });
});