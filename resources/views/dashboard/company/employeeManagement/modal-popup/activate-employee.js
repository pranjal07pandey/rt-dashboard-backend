$(document).ready(function() {
    $('#activateEmployeeModal').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).data('id');
        $("#activateEmployeeModal #empolyeeid").val(id);
    });
});