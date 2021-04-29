(function($) {
  $.fn.rtTree = function(options) {
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
                  $(".rtTree a").removeClass("active");
                  $(this).addClass("active");
                  $(this).siblings("ul").slideDown();
                  // console.log($(this).attr('id'));
                  var paginate = $(this).val();
                  var ids =$(this).attr('id');

                  $(".rtTree a.active").siblings("ul").empty();
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

          $("#newFolder").on("click", function(e){
              e.preventDefault();
               if (  $(".rtTree a.active").attr('id') != undefined ) {
                  var root_id = $(".rtTree a.active").attr('id');
               }else{
                   var root_id = 0;
               }
              $('#rootId').val(root_id);
              $('#createNewFolder').modal('show');
              $('.spinerSubDocket').css('display','block');

              $.ajax({
                  type: "GET",
                  url:options.createFolderSelect,
                  success: function(response){
                      $('.spinerSubDocket').css('display','none');
                      $("#folderCreateSelect").html(response);
                      if(root_id== 0) {

                          $("#folderSelect option[value="+root_id+"]").attr('selected', 'selected');
                          $('#folderSelect .dislableNested:contains("Please select a parent...")').text('').attr('selected', 'selected');
                          $("#nastedLabel").prop("checked", false);
                          $('#folderSelect').on('change', function() {
                              if ($("#folderSelect option:selected").text() == "Please select a parent...") {
                                  $('.submitFolderItem').prop("disabled", true);
                              }else{
                                  if ($("#folderSelect option:selected").val()==0){
                                      $("#nastedLabel").prop("checked", false);
                                      $('#folderSelect .dislableNested:contains("Please select a parent...")').text('Please select a parent...').attr('selected', false)
                                      $('.submitFolderItem').prop("disabled", false);
                                  }else{
                                      $("#nastedLabel").prop("checked", true);
                                      $('#folderSelect .dislableNested:contains("Please select a parent...")').text('Please select a parent...').attr('selected', false)
                                      $('.submitFolderItem').prop("disabled", false);
                                  }


                              }

                          });

                          $('#nastedLabel').change(function() {
                              if($(this).is(":checked")) {
                                  $('#folderSelect .dislableNested:contains("")').text('Please select a parent...').attr('selected', 'selected');
                                  $('.submitFolderItem').prop("disabled",true);
                                  $(".dislableNested option[value=0]").attr('selected', 'selected');
                                  $("#folderSelect").val(0);
                              }else{
                                  $('#folderSelect .dislableNested:contains("Please select a parent...")').text('').attr('selected', 'selected');
                                  $(".dislableNested option[value=0]").attr('selected', 'selected');
                                  $("#folderSelect").val(0);
                                  $('.submitFolderItem').prop("disabled",false);
                              }
                          });
                      }else{
                          $("#folderSelect option[value="+root_id+"]").attr('selected', 'selected');
                          $('#folderSelect').on('change', function() {
                              if ($("#folderSelect option:selected").text() == "Please select a parent...") {
                                  $('.submitFolderItem').prop("disabled", true);
                              }else{
                                  $("#nastedLabel").prop("checked", true);
                                  $('#folderSelect .dislableNested:contains("Please select a parent...")').text('Please select a parent...').attr('selected', false)
                                  $('.submitFolderItem').prop("disabled", false);
                              }

                          });
                          $('#nastedLabel').change(function() {
                              if($(this).is(":checked")) {
                                  $('#folderSelect .dislableNested:contains("")').text('Please select a parent...').attr('selected', 'selected');
                                  $('.submitFolderItem').prop("disabled",true);
                                  $("#folderSelect option[value=0]").attr('selected', 'selected');
                                  $("#folderSelect").val(0);
                              }else{

                                  $("#folderSelect option[value=0]").attr('selected', 'selected');
                                  $("#folderSelect").val(0);
                                  $('#folderSelect .dislableNested:contains("Please select a parent...")').text('').attr('selected', 'selected');
                                  $('.submitFolderItem').prop("disabled",false);
                              }
                          });
                      }


                  }
              });

          });






      });
  }
  }(jQuery));
