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
 
  <title>ことばおしえる</title>
	
</head>
<body>





<!-- Head[Start] -->
<header>
<nav class="navbar navbar-default">
	<a class="navbar-brand" href="logout.php">部屋から出る</a>
	<a class="navbar-brand" href="home.php">homeにもどる</a>
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

    <div class="col-xs-4  col-sm-4 " >
		<form id="word_input" class="form-horizontal" method="post" enctype="multipart/form-data" onsubmit="word_input();return false;" style="display: none" >
			<div class="jumbotron">
			  <div class="form-group">
				  <input type="text" class="form-control " name="word" id="word" placeholder="ことばを入力" required>
			  </div>
			  <div class="form-group">
				  <button type="submit" class="btn btn-warning ">けってい</button>
			  </div>
		  </div>
		</form>
	</div>

		
	<div class="col-xs-4 col-sm-5 " >

			<div class="toro_area"><p id="toro_comment">どのことばをおしえてくれるニャ？</div>
			<img class="img-rounded" src="img/toro_room_img3.png" alt="トロ画像" class="first_scene-img" id="toro_img">

	</div>

	<div id="input_select_div" class="col-xs-4 col-sm-3" >

		  <button id="input_btn1" type="button" class="btn btn-warning btn-lg select btn-block" >ひと</button>
		  <button id="input_btn2" type="button" class="btn btn-warning btn-lg select btn-block" >ばしょ</button>
		  <button id="input_btn3" type="button" class="btn btn-warning btn-lg select btn-block" >あそび</button>
		  <button id="input_btn4" type="button" class="btn btn-warning btn-lg select btn-block" style="display: none">選択肢4</button>
		  <button id="input_btn5" type="button" class="btn btn-warning btn-lg select btn-block" style="display: none">選択肢5</button>
		  <button id="input_btn6" type="button" class="btn btn-warning btn-lg select btn-block" style="display: none">スキ</button>
		  <button id="input_btn7" type="button" class="btn btn-warning btn-lg select btn-block" style="display: none">キライ</button>
		  <button id="input_btn8" type="button" class="btn btn-warning btn-lg select btn-block" style="display: none">バイバイ</button>
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
var type,type_view;
var word,kind,feel;

	
//『ひと』『ばしょ』『あそび』ボタン押下1
		$("#input_btn1").on("click",function(){	
			//ことばの種類(type)取得
            type = "person";
			type_view = "ひと";
			console.log("type:",type);
			input_remove1();
			
		});

//『ばしょ』ボタン押下
		$("#input_btn2").on("click",function(){	
			//ことばの種類(type)取得
            type = "place";
			type_view = "ばしょ";
			console.log("type:",type);
			input_remove1();
		});	

//『あそび』ボタン押下
		$("#input_btn3").on("click",function(){	
			//ことばの種類(type)取得
            type ="play";
			type_view = "あそび";
			console.log("type:",type);
			input_remove1();
		});
	
	
function input_remove1(){
	        $("#input_btn1,#input_btn2,#input_btn3").remove();
			$("#toro_comment").html('『'+type_view+'』のなまえを教えてニャ'+'<span class="glyphicon glyphicon-heart"></span>');
			$("#toro_img").attr("src",'img/toro_room_img1.png');

			$("#word_input").slideDown(500);	
}
	
	
//なまえ入力後、『けってい』ボタン押下（ことば取得）
function word_input() {
			//ことばの種類(type)取得
			$("#word_input").slideUp(500);
			word = $("#word").val();
			console.log("word:",word);
			$("#toro_img").attr("src",'img/toro_room_img7.png');
			$("#input_btn4 ,#input_btn5").show();
	
			if(type == 'person'){
			$("#toro_comment").html(word+'って男のひと？女のひと？');
				$("#input_btn4").html('男');
				$("#input_btn5").html('女');
			}else if(type == 'place'){
			$("#toro_comment").html(word+'って遠い？近い？');
				$("#input_btn4").html('遠い');
				$("#input_btn5").html('近い');
			}else if(type == 'play'){
			$("#toro_comment").html(word+'ってひとりでする？<br>みんなでする？');
				$("#input_btn4").html('ひとりで');
				$("#input_btn5").html('みんなで');
			}
	
}
		
//『input_btn4』or『input_btn5』ボタン押下(kind取得)
		$("#input_btn4").on("click",function(){	
			//ことばの種類(type)取得
            kind = $("#input_btn4").html();
			console.log("kind:",kind);
			input_remove2();
		});
		$("#input_btn5").on("click",function(){	
			//ことばの種類(type)取得
            kind = $("#input_btn5").html();
			console.log("kind:",kind);
			input_remove2();
		});
	
function input_remove2(){
	        $("#input_btn4 ,#input_btn5 ").remove();
			$("#input_btn6 ,#input_btn7").show();

			$("#toro_comment").html('<?=$_SESSION["player_name"]?>は<br>'+word+'のことスキ？キライ？');
			$("#toro_img").attr("src",'img/toro_room_img1.png');
}
	
//『input_btn6』or『input_btn7』ボタン押下(feel取得)
		$("#input_btn6").on("click",function(){	
			//ことばの種類(type)取得
            feel = $("#input_btn6").html();
			console.log("feel:",feel);
			input_remove3();
		});
		$("#input_btn7").on("click",function(){	
			//ことばの種類(type)取得
            feel = $("#input_btn7").html();
			console.log("feel:",feel);
			input_remove3();
		});
	

function input_remove3(){
	        $("#input_btn6 ,#input_btn7 ").remove();
			$("#input_btn8").show();
			$("#toro_comment").html(word+'のことがちょっと分かったニャ!<br>がんばって覚えないとニャ'+'<span class="glyphicon glyphicon-heart"></span>'+'<br>バイバイニャ～');
			$("#toro_img").attr("src",'img/toro_room_img4.png');
}

	
//『input_btn8』ボタン押下(最終確認後)
		$("#input_btn8").on("click",function(){
			
//jsonでの値の受け渡しに失敗。
//原因が不明。デバックできなかった。★要質問			
//		$.ajax({
//			type: 'POST',
//			dataType: 'json',
//			url: 'lesson_act.php',
//			data: {
//					'type': type,
//					'word': word,
//					'kind': kind,
//					'feel': feel
//				  },
//			success: function(data){
//				console.log(data);
//				$("#console").html(data);
//			}
//		});
			
	var lesson = word+"/"+type+"/"+kind+"/"+feel;
		
    $.ajax({
        type: "POST",
        url: "lesson_act.php",
        data: { lesson:lesson },
        datatype: "html",
        success: function(data){
				console.log("ことばをおしえる成功",data);
				location.href = "home.php";
				
		}
		
		});
	
	});	
	
</script>

</body>
</html>
