$(document).ready(function(){
    $('#framework').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth:'100%',
        includeSelectAllOption: true,
        nonSelectedText: '',
        filterPlaceholder: 'Search Template',
        onChange: function(element, checked) {
            if (element){
                lastSelected = element.val();
            }
            else {
                $("#framework").multiselect('select', lastSelected);
                $("#framework").multiselect('deselect', element.val());
            }
        }
    });
});