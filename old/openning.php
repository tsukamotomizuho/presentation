<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>オープニング画面</title>


    <link href="css/bootstrap.min.css" rel="stylesheet">

	<!--CSS -->
	<style type="text/css">
		body{
			background: #ffffcc;
			text-align: center;
			color: #a50;
		}

		img{
			margin: auto;
		}

		.select{
			width:300px;
		}

	</style>
	<!--CSS -->
</head>

	


<body>

	<img src="img/openning_img2.png" class="img-responsive img-rounded" alt="オープニング画像" >

    <h3>再現してみました</h3>
    <p>※トロに言葉を教えて遊ぶゲームです※</p>


	<div class="btn-group-vertical select">
	  <button id="new_game" type="button" class="btn btn-warning btn-lg" >はじめから</button>
	  <button id="load_game" type="button" class="btn btn-warning btn-lg"  disabled>お部屋に行く</button>
	</div>







<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
	
//はじめからを選択した場合
$("#new_game").on("click",function(){
	location.href = "setting.php";
   });  

//お部屋に行くを選択した場合
$("#load_game").on("click",function(){
	location.href = "login.php";
   });


</script>

</body>


</html>