$(document).ready(function(){
    $('#deleteRequestModal').on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).data('id');
        $("#deleteRequestModal #clientid").val(id);
    });
});
