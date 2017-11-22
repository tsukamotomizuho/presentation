<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">

<!--bootstrap3-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link href="css/bootstrap.min.css" rel="stylesheet">

	<style type="text/css">
		
		div{padding: 10px;font-size:16px;}
		body{
			background: #ccffcc;
		}
		
		img{
			margin: auto;
		}
		
		p{
			color: #a50;
			background-color: #ffffe0;    
			padding: 10px;
			width: 200px;
			border: 3px solid #fc0;
			margin-top: 100px;
			border-radius: 10px;
			font-family:"HGP創英角ﾎﾟｯﾌﾟ体";
		}

	</style>
<!--bootstrap3-->
 
  <title>初期設定画面</title>
	
</head>
<body>





<!-- Head[Start] -->
<header>
<nav class="navbar navbar-default">
<!--  <div class="container-fluid">-->
	<a class="navbar-brand" href="openning.php">戻る</a>
<!--  </div>-->
</nav>

</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div class="col-sm-offset-1 col-sm-11">
	<h1><span class="label label-success">個人データ入力</span></h1>
</div>
<!--class="container" 中央ぞろえ-->

<div class="container">
  <div class="row">
    <div class="col-xs-6 " >

		<form class="form-horizontal" method="post" action="setting_act.php" enctype="multipart/form-data"  onsubmit="return check()" >
			<div class="jumbotron">
			  <div class="form-group">
				<label class="control-label col-sm-4" for="player_name">プレイヤー名:</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" name="player_name" id="player_name" placeholder="あなたの名前を入力" required>
				</div>
			  </div>
			  <div class="form-group">
				<label class="control-label col-sm-4" for="password">パスワード:</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" name="password" id="password" placeholder="パスワードを入力" required>
				</div>
			  </div>
			  <div class="form-group">
				<label class="control-label col-sm-4" for="gender">性別:</label>
				<div class="col-sm-8">
				  <label class="radio-inline"><input type="radio" name="gender"  value="男" required>男</label>
				  <label class="radio-inline"><input type="radio" name="gender"  value="女" required>女</label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="control-label col-sm-4" for="birthday">誕生日:</label>
				<div class="col-sm-8">
				  <input type="date" class="form-control" name="birthday" id="birthday" required>
				</div>
			  </div>
			  <div class="form-group">
				 <div class="col-sm-offset-4 col-sm-8">
				  <button type="submit" class="btn btn-warning  btn-lg">けってい</button>
				</div>
			  </div>
		  </div>
		</form>

	</div>

		
	<div class="col-xs-6 toro" >
	<div><p>あなたのことを<br>おしえてほしいニャ～</p></div>

		<img src="img/toro_img1.jpg" alt="トロ画像" class="first_scene-img" id="toro_img">
	</div>
  </div>
</div>




<!-- Main[End] -->

<!--bootstrap3-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--bootstrap3-->

<script>

	var check = function(){
   if (window.confirm('これでよろしいですか？')) {
        return true;
    }
    return false;
};

</script>

</body>
</html>
