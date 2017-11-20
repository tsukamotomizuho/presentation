<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
ssidChk();//セッションチェック関数

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">

<!--bootstrap3-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <link href="css/toro.css" rel="stylesheet">

<!--bootstrap3-->
 
  <title>自己紹介</title>
	
</head>
<body>





<!-- Head[Start] -->
<header>
<nav class="navbar navbar-default">
	<a class="navbar-brand" href="logout.php">部屋から出る</a>
</nav>

</header>
<!-- Head[End] -->


<!-- Main[Start] -->


<div class="play_disp">
<!--class="container" 中央ぞろえ-->
  <div class="row">
	<div class="room_name">
		<h1><span class="label label-success"><?=$_SESSION["player_name"]?>の部屋</span></h1>
	</div>
</div>

<div class="container">
  <div class="row">
<!--    <div class="col-xs-3 col-sm-offset-1 col-sm-3 " >-->
    <div class="col-xs-4  col-sm-4 " >
		<form class="form-horizontal" method="post" action="setting_act.php" enctype="multipart/form-data"  onsubmit="return check()" style="display: none" >
			<div class="jumbotron">
			  <div class="form-group">
				  <input type="text" class="form-control " name="player_name" id="player_name" placeholder="ことばを入力" required>
			  </div>
			  <div class="form-group">
				  <button type="submit" class="btn btn-warning ">けってい</button>
			  </div>
		  </div>
		</form>
	</div>

		
	<div class="col-xs-4 col-sm-5 " >

			<div class="toro_area"><p id="toro_comment">はじめまして・・・<br>ネコのトロです・・・<br>ニンゲンになるのがユメなのニャ<span class="glyphicon glyphicon-heart"></span></p></div>
			<img class="img-rounded" src="img/toro_room_img1.png" alt="トロ画像" class="first_scene-img" id="toro_img">

	</div>

	<div class="col-xs-4 col-sm-3" >
		<div class="btn-group-vertical btn-block">
		  <button id="input_btn1" type="button" class="btn btn-warning btn-lg select" >すすむ</button>
<!--
		  <button id="input_btn2" type="button" class="btn btn-warning btn-lg select" style="display: none">選択肢2</button>
		  <button id="input_btn3" type="button" class="btn btn-warning btn-lg select" style="display: none">選択肢3</button>
-->
		</div>
	</div>
	<div class="col-xs-1 " >
	</div>	
  </div>



</div>

</div>


<!-- Main[End] -->

<!--bootstrap3-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--bootstrap3-->



<script>

	
//『つぎへ』ボタン押下1
$("#input_btn1").on("click",function(){
	var toro_comment ="ここで<?=$_SESSION["player_name"]?>といっしょに暮せば！<br>人間になれそうな気がするニャ";
	toro_comment += '<span class="glyphicon glyphicon-heart"></span>';
	toro_comment += "<br>よろしくなのニャ";
	toro_comment += '<span class="glyphicon glyphicon-heart"></span>';
	$("#toro_comment").html(toro_comment);
	$("#input_btn1").html("よろしく");
	$("#toro_img").attr("src",'img/toro_room_img4.png');
	
	//『つぎへ』ボタン押下2
		$("#input_btn1").on("click",function(){	
			location.href = "home.php";
		});
	
});
	

	
	
//function confirm() {
//    if (window.confirm('これでよろしいですか？')) {
//        return true;
//    }
//    return false;
//}
		
	
</script>

</body>
</html>
