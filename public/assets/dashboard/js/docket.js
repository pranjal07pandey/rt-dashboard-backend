$(document).ready(function(){
    var titleDiv    =   ' <div class="col-md-12 shortTextDiv" id="shortTextDiv">'+$('#shortTextDiv').html()+'</div>';
    var hoursDiv    =   '<div class="col-md-12" id="hoursDiv"><div class="row"><div class="col-md-6"><div class="horizontalList"><span>Hours</span><div class="form-group" style="min-width: 150px;width:150px;"><input id="hours" type="text" class="form-control" name="hours" placeholder="Hours"  required autofocus></div></div></div><div class="col-md-6"><div class="horizontalList"><span>To</span><div class="form-group" style="min-width: 150px;width:150px;"><input id="to" type="text" class="form-control" name="to" placeholder="To"  required autofocus></div></div></div></div></div>';
    var imagesDiv   =   '<div class="col-md-12 imageDiv" id="imagesDiv">'+$('#imagesDiv').html()+'</div>'
    var rateDiv     =   ' <div class="col-md-12" id="rateDiv"><div class="horizontalList"><span>Hourly Rate</span><div class="form-group"><input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="Hourly Rate"  required autofocus></div></div></div>'
    var descriptionDiv  =   '<div class="col-md-12 longTextDiv" id="longTextDiv">'+$('#longTextDiv').html()+'</div>';
    var locationDiv =   '<div class="col-md-12" id="locationDiv">'+$('#locationDiv').html()+'</div>';

    var numDiv  =   '<div class="col-md-12 numDiv" id="numDiv">'+$('#numDiv').html()+'</div>';
    function editableFunction(){
        $('.editable').editable({
            type: 'text',
            title: 'Enter username',
            success: function(response) {
                console.log(response);
            }
        });
    }
    //title checkbox js
    $('#shortTextCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#shortTextCheckbox').attr('data','unchecked');
            $.when($(".shortTextDiv").fadeOut()).done(function() {
                $(".shortTextDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(titleDiv)).done(function() {
                $("#shortTextDiv").hide();
                $("#shortTextDiv").fadeIn();
            });
            $(this).attr('data','checked');
        }

    });

    $(document).on('click', '#shortTextAdd', function(){
        if($('#shortTextAdd').length>0){
            // $('#shortTextAdd ').attr('id',"removeShortText");
            // $(this).attr('class','btn btn-raised btn-xs btn-danger');
            // $('#removeShortText').html('x');
            $.when($('#sortable > div:first-child').before(titleDiv)).done(function() {
                $("#shortTextDiv").hide();
                $("#shortTextDiv").fadeIn();
                editableFunction();
            });
        }
    });

    $(document).on('click', '#removeShortText', function(){
        $.when($(this).parents('#shortTextDiv').fadeOut()).done(function() {
            $(this).parents('#shortTextDiv').remove();
        });
    });

    //num checkbox js
    $('#numCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#numCheckbox').attr('data','unchecked');
            $.when($(".numDiv").fadeOut()).done(function() {
                $(".numDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(numDiv)).done(function() {
                $("#numDiv").hide();
                $("#numDiv").fadeIn();
            });
            $(this).attr('data','checked');
        }

    });

    $(document).on('click', '#numAdd', function(){
        if($('#numAdd').length>0){

            $.when($('#sortable > div:first-child').before(numDiv)).done(function() {
                $("#numDiv").hide();
                $("#numDiv").fadeIn();
                editableFunction();
            });
        }
    });

    $(document).on('click', '#removeNum', function(){
        $.when($(this).parents('#numDiv').fadeOut()).done(function() {
            $(this).parents('#numDiv').remove();
        });
    });

    //hours checkbox js
    $('#hoursCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#hoursCheckbox').attr('data','unchecked');
            $.when($("#hoursDiv").fadeOut()).done(function() {
                $("#hoursDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(hoursDiv)).done(function() {
                $("#hoursDiv").hide();
                $("#hoursDiv").fadeIn();
            });
            $(this).attr('data','checked');
        }
    });

    //hours checkbox js
    $('#locationCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#locationCheckbox').attr('data','unchecked');
            $.when($(".locationDiv").fadeOut()).done(function() {
                $(".locationDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(locationDiv)).done(function() {
                $("#locationDiv").hide();
                $("#locationDiv").fadeIn();
                $.material.init();
            });
            $(this).attr('data','checked');
        }
    });

    $(document).on('click', '#locationAdd', function(){
        if($('#locationAdd').length>0){

            $.when($('#sortable > div:first-child').before(locationDiv)).done(function() {
                $("#locationDiv").hide();
                $("#locationDiv").fadeIn();
                // $.material.init();
                editableFunction();
            });
        }
    });

    $(document).on('click', '#removeLocation', function(){
        $.when($(this).parents('#locationDiv').fadeOut()).done(function() {
            $(this).parents('#locationDiv').remove();
        });
    });



    //hours checkbox js
    $('#imagesCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#imagesCheckbox').attr('data','unchecked');
            $.when($(".imageDiv").fadeOut()).done(function() {
                $(".imageDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(imagesDiv)).done(function() {
                $("#imagesDiv").hide();
                $("#imagesDiv").fadeIn();
                $.material.init();
            });
            $(this).attr('data','checked');
        }
    });

    $(document).on('click', '#imageAdd', function(){
        if($('#imageAdd').length>0){
            $.when($('#sortable > div:first-child').before(imagesDiv)).done(function() {
                $("#imagesDiv").hide();
                $("#imagesDiv").fadeIn();
                $.material.init();
                editableFunction();
            });
        }
    });

    $(document).on('click', '#removeImage', function(){
        $.when($(this).parents('#imagesDiv').fadeOut()).done(function() {
            $(this).parents('#imagesDiv').remove();
        });
    });

    //descriptionCheckbox checkbox js
    $('#longTextCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#longTextCheckbox').attr('data','unchecked');
            $.when($(".longTextDiv").fadeOut()).done(function() {
                $(".longTextDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(descriptionDiv)).done(function() {
                $("#longTextDiv").hide();
                $("#longTextDiv").fadeIn();
            });
            $(this).attr('data','checked');
        }
    });

    $(document).on('click', '#longTextAdd', function(){
        if($('#longTextAdd').length>0){
            // $('#longTextAdd ').attr('id',"removeLongText");
            // $(this).attr('class','btn btn-raised btn-xs btn-danger');
            // $('#removeLongText').html('x');
            $.when($('#sortable > div:first-child').before(descriptionDiv)).done(function() {
                $("#longTextDiv").hide();
                $("#longTextDiv").fadeIn();
                editableFunction();
            });
        }
    });

    $(document).on('click', '#removeLongText', function(){
        $.when($(this).parents('#longTextDiv').fadeOut()).done(function() {
            $(this).parents('#longTextDiv').remove();
        });
    });

    //descriptionCheckbox checkbox js
    $('#rateCheckbox').click(function(){
        if($(this).attr('data') == 'checked'){
            $('#rateCheckbox').attr('data','unchecked');
            $.when($("#rateDiv").fadeOut()).done(function() {
                $("#rateDiv").remove();
            });
        } else {
            $.when($('#sortable > div:first-child').before(rateDiv)).done(function() {
                $("#rateDiv").hide();
                $("#rateDiv").fadeIn();
            });
            $(this).attr('data','checked');
        }
    });
});