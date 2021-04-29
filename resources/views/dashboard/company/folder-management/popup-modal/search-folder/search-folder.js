$(document).on("keyup", '#searchFolderModal #searchFolderName', function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        $("#searchFolderModal .submit" ).trigger( "click" );
    }
});

$(document).on('click','#searchFolderModal .submit',function () {
    var inputVal = $('#searchFolderModal #searchFolderName').val().trim();
    if(inputVal.length==0){ alert("Please enter folder name");}
    else{

        $.ajax({
            type: "Post",
            url: base_url+'/dashboard/company/folder/searchFolder',
            data: {"name":inputVal},
            success: function(response) {
                if(response.status == true) {

                    $(".boxContent").html(response.detail).show();
                    $('#searchFolderModal').modal('hide');
                    $("ul.rtTree").rtTree({
                        clickListItem :base_url+'/dashboard/company/folder',
                        viewFolderData:base_url+'/dashboard/company/folder/viewFolderData',
                        addFolder:base_url+'/dashboard/company/folder/newFolderCreate',
                        createFolderSelect:  base_url+'/dashboard/company/folder/createFolderSelect',
                        ajaxCompletion : function test(response){
                            var items = [];
                            $.each(response.data, function() {
                                items.push(  $(".rtTree a.active").siblings("ul").append('<li><a href="#" id="'+this.id+'">'+this.name+' <span style="    position: absolute;right: 4px;">'+this.totalItems+'</span></a><ul></ul>  <div  class="editBtn" id="editBtnId" data-id="'+this.id+'" data-title="'+this.name+'" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div> </li>' ));
                            });

                            $(".viewFolder").html(response).show();
                        }
                    });
                }
            }
        });
    }


    //             $(document).on('click','#UpdateFolder',function () {
    //                 var folderId = $('#editIdFolder').val();
    //                 var folderName = $('#editNameFolder').val();
    //                 $.ajax({
    //                     type:"Post",
    //                     url:base_url+'/dashboard/company/folder/updateFolder',
    //                     data:{'id':folderId,'title':folderName},
    //                     success: function (response) {
    //                         if (response.status == true){
    //                             $(".rtTree li a[id="+response.id+"]").html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
    //                         }
    //                         if ( $(".rtTree li a[id="+response.id+"]").hasClass('active')){
    //                             $('.rtTabHeader ul li h4').html(response.title+'<span style="    position: absolute;right: 4px;">'+response.totalItems+'</span>');
    //                         }
    //                         $('#updateFolderData').modal('hide');
    //                     }
    //                 });
    //
    //             });
    //
    //
    //
    //
    //         }
    //
    //     }
    // });
});
