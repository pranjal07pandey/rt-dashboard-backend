(function($) {
  $.fn.rtTree = function(options) {
      console.log("test");

      var settings = $.extend({
      ajaxURL : null,
      ajaxCompletion : null
      }, options);


      return this.each(function() {
          $(this).on("click", 'a',function(e){
              e.preventDefault();

              console.log($(this).hasClass("active"));
              if($(this).hasClass("active")){
                  $(this).removeClass("active");
                  $(this).siblings("ul").slideToggle();
              }else{
                  $(".rtTree a").removeClass("active");
                  $(this).addClass("active");
                  $(this).siblings("ul").slideDown();
              }
              console.log(options.ajaxURL);
              settings.ajaxCompletion.call(this,options.ajaxURL);
          });

          $("#newFolder").on("click", function(e){
              e.preventDefault();
              console.log("test");
              $(".rtTree a.active").siblings("ul").append(' <li><a href="#" id="23">First</a></li>');
          });
      });
  }
  }(jQuery));