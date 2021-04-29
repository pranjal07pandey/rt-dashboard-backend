(function($) {
    $.fn.folderRtTree = function(options) {
        // console.log("test");

        var settings = $.extend({
            ajaxURL : null,
            ajaxCompletion : null,
            newItemId : null,
            newItemURL: null,
            clickListItem: null,
            viewFolderData: null,
            newFolderCreate: null,
            createFolderSelect : null

        }, options);


        return this.each(function() {


            $(this).on("click", 'a',function(e){
                e.preventDefault();
                $('.menu ul li').removeClass('active');
                if($(this).hasClass("active")){
                    $(this).removeClass("active");
                    $(this).siblings("ul").slideToggle();
                }else{
                    $(".menuli").removeClass("active");
                    $(".folderRtTree a").removeClass("active");
                    $(this).addClass("active");
                    $(this).siblings("ul").slideDown();
                    var paginate = $(this).val();
                    var ids =$(this).attr('id');

                    $(".folderRtTree a.active").siblings("ul").empty();
                    $.ajax({
                        type:"Post",
                        url:options.clickListItem,
                        cache: false,
                        data:{'id':ids},
                        success: function (response) {
                            settings.ajaxCompletion.call(this,response);
                            $('.loadspin').css('display','block');
                            $.ajax({

                                type:"Post",
                                url:options.viewFolderData,
                                cache: false,
                                data:{'folderId':ids,'items':paginate},
                                success: function (response) {
                                    $('.loadspin').css('display','none');
                                    settings.ajaxCompletion.call(this,response);
                                }

                            });

                        }

                    });


                }
                // console.log(options.ajaxURL);

            });







        });
    }
}(jQuery));
