<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>Opps Something Went Wrong</title>
</head>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900" rel="stylesheet">
<body>
<div class="col1 logo-top-header center">
    <img src="https://www.recordtimeapp.com.au/backend/assets/beta/images/logoWhiteStrok.png">
</div>
<div class="col2 center">
    <div class="col1">
        <p class="emoji">:(</p>
    </div>
    <p class="text-bar">Sorry, Something Went Wrong</p>
    <p>we are working on it and we'll get it fixed as soon as we can.</p>
    <a href="{{ url()->previous() }}"  class="btn btn-homepage ">Go Back</a>
    <div class="col1 center">
        <p><small style="font-size: 11px;">© 2015 - 2018 Record TIME Pty Ltd. All rights reserved. ABN: 99 604 582 649 | Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA | Support: 0421 955 630</small></p>
    </div>
</div>

<style type="text/css">
    body{
        margin: 0px ;
        padding: 0px;
        background: #E5E7E9;
    }
    *{
        font-family: 'Raleway', sans-serif;
    }
    .center{
        align-content: center;
        text-align: center;

    }
    .col1{
        width:100%;
    }
    .col2{
        width: 60%;
        margin: 50px auto;
        padding: 40px;
        background: #fff;
        height:400px;

    }
    .logo-top-header img{
        height: 60px;
        display: block;
        margin: 0px auto;
    }
    .logo-top-header{
        background: #042F53;
        padding: 40px 0px;
    }
    .text-bar{
        font-size: 40px;
        font-weight: bold;
        margin: 0px;
        padding: 0px 80px;
        clear: both;
    }
    .emoji{
        background: #fe821e;
        padding: 41px;
        font-size: 50px;
        border-radius: 50%;
        color: #fff;
        display: block;
        margin: 0px auto 20px;
        width: 60px;
        height: 60px;
    }
    .btn-homepage{
        width: 200px;
        height: 27px;
        padding: 13px 10px 10px;
        font-size: 19px;
        background:#042F53;
        display: block;
        margin: 40px auto 20px;
        border: 1px solid #ccc;
        border-color: rgb(216, 216, 216) rgb(209, 209, 209) rgb(186, 186, 186);
        cursor: pointer;
        text-decoration: none;
        color: #fff;
        text-transform: uppercase;
    }

    .btn-homepage:hover{
        text-decoration: none;
    }
</style>
</body>
</html>
