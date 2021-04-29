$(document).ready(function() {
    $('#updateEmailClientModal').on('show.bs.modal', function(e) {
        var id = $(e.relatedTarget).data('id');
        var email = $(e.relatedTarget).data('email');
        var fullname = $(e.relatedTarget).data('fullname');
        var companyname = $(e.relatedTarget).data('companyname');
        var companyaddress = $(e.relatedTarget).data('companyaddress');

        $("#updateEmailClientModal #id").val(id);
        $("#updateEmailClientModal #email").val(email);
        $("#updateEmailClientModal #fullname").val(fullname);
        $("#updateEmailClientModal #companyname").val(companyname);
        $("#updateEmailClientModal #companyaddress").val(companyaddress)
    });
});