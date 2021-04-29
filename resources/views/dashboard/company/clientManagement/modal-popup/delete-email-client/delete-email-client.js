$(document).ready(function() {
    $('#deleteEmailClientModal').on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).data('id');
        $("#deleteEmailClientModal #deleteEmailClientid").val(id);
    });
});