 <!--modified by dileep-->
<div class="sidebarBox">
    <div class="boxHeader">
        <strong class="pull-left">Folders</strong>
        <div class="pull-right">
            <a href="#"><i class="material-icons">search</i></a>
            <a href="#" id="newFolder"><i class="material-icons">create_new_folder</i></a>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="boxContent">

        <ul class="rtTree">
            <li>
                <a href="#" id="1">Root 1</a>
                <ul style="display:none;">
                    <li><a href="#" id="23">A</a>
                        <ul style="display:none;">
                            <li><a href="#" id="23">First</a></li>
                            <li><a href="#" id="23">Second</a></li>
                        </ul>
                    </li>
                    <li><a href="#" id="23">B</a></li>
                </ul>
            </li>
            <li> <a href="#" id="1">Root 2</a></li>
        </ul>
        <div class="directoryEmpty"><small>Press   <i class="material-icons">create_new_folder</i> to add new folder</small></div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/css/rtTree.css') }}"/>
<script src="{{  asset('assets/dashboard/js/rtTree.js') }}"></script>
<script>
    $(document).ready(function(){
        $("ul.rtTree").rtTree({
        ajaxURL : "https://blog.teamtreehouse.com/writing-your-own-jquery-plugins",
            newItemId : "#newFolder",
            newItemURL : "https://blog.teamtreehouse.com/writing-your-own-jquery-plugins",
        ajaxCompletion : function test(response){ 
        //   console.log(response);
        }
        });

    });
</script>
<!--modified by dileep-->