<div class="slider">
    <?php $i=1 ?>
    @foreach( unserialize($themes->screenshot) as  $rowData)
    <input type="radio" name="slide_switch" id="id<?php echo $i ?>" <?php if($i==1) { ?> checked<?php } ?>/>
    <label for="id<?php echo $i ?>">
        <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" width="100" height="67"/>
    </label>
    <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" width="639" height="320px"/>
            <?php $i++ ?>
    @endforeach
</div>
<style>
    /*Time for the CSS*/
    * {margin: 0; padding: 0;}
    body {background: #ccc;}

    .slider{
        width: 100%; /*Same as width of the large image*/
        position: relative;
        /*Instead of height we will use padding*/
        padding-top: 320px; /*That helps bring the labels down*/

        margin: 100px auto;

        /*Lets add a shadow*/
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.75);
        margin-top: 20px;
    }


    /*Last thing remaining is to add transitions*/
    .slider>img{
        position: absolute;
        left: 0; top: 0;
        transition: all 0.5s;
    }

    .slider input[name='slide_switch'] {
        display: none;
    }

    .slider label {
        /*Lets add some spacing for the thumbnails*/
        margin: 18px 0 0 18px;
        border: 3px solid #999;

        float: left;
        cursor: pointer;
        transition: all 0.5s;

        /*Default style = low opacity*/
        opacity: 0.6;
    }

    .slider label img{
        display: block;
    }

    /*Time to add the click effects*/
    .slider input[name='slide_switch']:checked+label {
        border-color: #666;
        opacity: 1;
    }
    /*Clicking any thumbnail now should change its opacity(style)*/
    /*Time to work on the main images*/
    .slider input[name='slide_switch'] ~ img {
        opacity: 0;
        transform: scale(1.1);
    }
    /*That hides all main images at a 110% size
    On click the images will be displayed at normal size to complete the effect
    */
    .slider input[name='slide_switch']:checked+label+img {
        opacity: 1;
        transform: scale(1);
    }
    /*Clicking on any thumbnail now should activate the image related to it*/

    /*We are done :)*/
</style>


