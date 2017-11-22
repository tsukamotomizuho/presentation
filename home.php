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
   <link href="css/toro.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	
	<script src="js/bootstrap.min.js"></script>
<!--bootstrap3-->


<!--slick-->
	<link rel="stylesheet" href="slick-1.8.0/slick/slick.css">
	<link rel="stylesheet" href="slick-1.8.0/slick/slick-theme.css">
	<script src="slick-1.8.0/slick/slick.min.js"></script>
<!--slick-->
 

 
  <title>home</title>
	
</head>
<body>





<!-- Head[Start] -->
<header>
<nav class="navbar navbar-default">
	<h3>スライド作成画面</h3>
<!--		<a class="navbar-brand" href="logout.php">スライド作成画面</a>-->
</nav>

</header>
<!-- Head[End] -->


<!-- Main[Start] -->


<div class="play_disp">
<!--class="container" 中央ぞろえ-->
  <div class="row">
	<div class="room_name">
<!--		<h1><span class="label label-success"><?=$_SESSION["player_name"]?>の部屋</span></h1>-->
	</div>
  </div>

<div class="container">
  <div class="row">
	<div class="col-xs-4 col-sm-3 select_div" >
		<div>
	  		<div class="toro_area"><p>xxxさん</p></div>
	  		
		<!--<div class="alert alert-success">
		  <strong>xxxさん</strong> 
		</div>-->

		<img src="img/icon_sample.png" class="img-responsive img-rounded slide" alt="トロ画像" >
		</div>
		

	
<form method="post" action="insert.php" enctype="multipart/form-data">
	<label for="upfile" >
		<h2 id="btn"><span class="label label-warning upfile">①スライドUL</span></h2>
		<input type="file" id="upfile"  name="upfile[]" webkitdirectory style="display:none;" />
	</label>
	 <input type="submit" value="送信">
</form>

	  
		  <button id="input_btn2" type="button" class="btn btn-warning btn-block">②音声録音</button>
		  <button id="input_btn3" type="button" class="btn btn-warning btn-block">③再生</button>
<!--		</div>-->


		
	</div>
	<div class="col-xs-1 col-sm-1" >
	</div>	
   
		
	<div class="col-xs-7 col-sm-8" >
スライド　x/x枚目
	<div class="slide_area">
		<div class="slider">
			<div class="sample"><img src="img/slide_sample.png" class="img-responsive img-rounded slide sample" alt="サンプル画像" ></div>
			<div class="sample"><img src="img/slide_sample1.png" class="img-responsive img-rounded slide sample" alt="サンプル画像1" ></div>
			<div id="slide2"><img src="img/slide_sample2.png" class="img-responsive img-rounded slide" alt="サンプル画像2" ></div>
			<div id="slide3"><img  src="img/slide_sample3.png" class="img-responsive img-rounded slide" alt="サンプル画像3" ></div>
		</div>

	</div>
	
	</div>
  </div>
</div>

</div>




<!-- Main[End] -->




<script>

$('.slider').slick();

//アップロード回数
let slide_ul_num = 0;
	
//①ファイルUL押下
$('#upfile').change(function(){
	$('.slider').remove();//サンプル削除

	//前回ULしたスライド削除   
	if(slide_ul_num > 0){
		$('.slider'+slide_ul_num).remove();
	}
	
	slide_ul_num++;
	console.log(slide_ul_num);//同じファイルを連続ULするとカウントされない。
	
	//スライドデータ
	let slide_data ='';
	//スライドの枚数
	let slide_num = this.files.length -1;
	
	//スライド全体
	let slider_add = '';
	slider_add += '<div class="slider'+slide_ul_num+'">';	

	//ULされたスライドのhtmlを作成
	for (let i = 0; i < this.files.length; i++) {

		// 選択されたファイル情報を取得
		let file = this.files[i];

		// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
		let reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = function() {
		//$('#slide'+i+'>img').attr('src', reader.result );

		//何枚でもアップロードできるように変更
		//スライド追加
		slide_data += reader.result+"/";	
		let slide_add = '';		
		slide_add += '<div id="slide'+ i+'">';
		slide_add += '<img src="'+reader.result+'" class="img-responsive img-rounded slide" alt="ULスライド'+i+'枚目" >';
		slide_add += '</div>'; 
		slider_add += slide_add;

			//スライドがすべてULされたとき
			//sliderのdivタグを閉じる＆新しくslick関数を動作させる
			if(i == slide_num){
				slider_add += '</div>';
				$(".slide_area").append(slider_add);
				$('.slider'+slide_ul_num).slick();
//				console.log(slide_data);
//				slide_ul(slide_data);
				
		
			   }
		}
	}
	
});

//スライドUL処理(ajaxバージョン)
$("#search_btn").on("click",function(){
    $.ajax({
        type: "POST",
        url: "insert.php",
        data: { search:$("#search").val() },
        datatype: "html",
        success: function(data){
        $("#view_table").html(data);
        }
    });
});
	
	

//ボタン2押下
		$("#input_btn2").on("click",function(){	
			location.href = "talk.php";
		});	


//ボタン3押下
		$("#input_btn3").on("click",function(){	
			location.href = "note.php";
		});
	
//function confirm() {
//    if (window.confirm('これでよろしいですか？')) {
//        return true;
//    }
//    return false;
//}
		
function slide_ul(){

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
}

	
</script>

</body>
</html>
