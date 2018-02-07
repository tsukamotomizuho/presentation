<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

//セッション関数(ユーザ情報)
$_SESSION["user_name"] = '発表者';
////ユーザid⇒presen_mkでのみ使用するパラメータとする
//$_SESSION["user_id"] = '1' ;
//アイコン画像名
$_SESSION["user_icon"] = 'icon_sample.png' ;

//入力チェック(受信確認処理追加)　ソート処理①
if(
  !isset($_GET["slide_group"]) || $_GET["slide_group"]=="" ||
  !isset($_GET["slide_num"]) || 
  $_GET["slide_num"]=="" 
){
  exit('ParamError：プレゼンurlが間違っています');
}

//■再生プレゼン読み込み■
//1.GET受信
$view_slide_group = $_GET["slide_group"];//スライドグループ
$view_slide_num   = $_GET["slide_num"];//スライド数

//echo 'スライドグループ：$slide_group='.$view_slide_group;
//echo '/　スライド数：$slide_num='.$view_slide_num;

//2. DB接続
$pdo = db_con();
	
//３．SQLを作成(スライド取得)

$view_slide = '<div class="slider0">';//slider開始タグ
$view_slide_id   = "なし";
$view_slide_data ='';//スライド画像ファイル名
$view_slide_data_copy ='' ;//デバック用
$view_slide_name ='';//スライド名
$file_dir_path = "upload_slide/";  //画像ファイル保管先

//②スライドを一枚ずつ取得＆表示html作成	
	for($i=1; $i <= $view_slide_num; $i++){
		$stmt = $pdo->prepare("SELECT * FROM slide_table 
			WHERE 
			slide_group =".$view_slide_group." AND 
			slide_num =".$view_slide_num." AND 
			slide_now_num =".$i." 
			ORDER BY slide_id DESC LIMIT 1");
		$status = $stmt->execute();

//	echo '/　$stm='."SELECT * FROM slide_table 
//			WHERE 
//			slide_group =".$view_slide_group." AND 
//			slide_num =".$view_slide_num." AND 
//			slide_now_num =".$i." 
//			ORDER BY slide_id DESC LIMIT 1";

	//表示html作成
	if($status==false){
		queryError($stmt);
	}else{//正常
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

			$view_slide_data 	  = $r["slide_data"];
			$view_slide_data_copy .= $view_slide_data.'/';
			$view_slide_name      = $r["slide_name"];
			$view_slide_id        = $r["slide_id"];
			$view_slide_now_num   = $r["slide_now_num"];	
			$view_slide .= '<div class="db_slide">';
			$view_slide .= 	'<img src="'.$file_dir_path.$view_slide_data.'" class="img-responsive img-rounded slide slide_img_'.$view_slide_now_num.' " alt="dbスライド"></div>';
		}		
	}
  }
			$view_slide .= '</div>'; //slider終了タグ



//5．SQLを作成(音声取得)
//スライド番号ごとに取り出す
	$view_voice = '';//html表示用
	$view_voice_id   = "なし";
	$view_voice_data_copy =''; //デバック用
	$view_voice_time_copy    ='';
	$voice_file_dir_path = "upload_voice/";  //画像ファイル保管先

for($i=1; $i <= $view_slide_num; $i++){
		$sql = 'SELECT COUNT(*) FROM voice_table 
		WHERE 
		slide_group ='.$view_slide_group.' AND 
		slide_now_num ='.$i;
	
		$res = $pdo->prepare($sql);
		$status1 = $res->execute();
		
		$voice_table_sql = 'SELECT * FROM voice_table WHERE 
		slide_group ='.$view_slide_group.' AND 
		slide_now_num ='.$i.' 
		ORDER BY voice_id DESC LIMIT 1';
	
		$stmt = $pdo->prepare($voice_table_sql);
		$status2 = $stmt->execute();
		//実行後、エラーだったらfalseが返る
	

	//音声タグ作成
		$view_slide_now_num ='';
		$view_voice_data    ='';
	
 if ($status1) {

  //DBに該当する音声があるかどうかチェック
  /* SELECT 文にマッチする行数をチェック*/
  if ($res->fetchColumn() > 0) {

	if($status2==false){
			queryError($stmt);
	}else{//正常

	  
	  while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

				$view_voice_id      = $r["voice_id"];
				$view_voice_data    = $r["voice_data"];
				$view_voice_data_copy    .= $r["voice_data"].'/';
		  		//$slide_now_num = $iだから不要
				$view_voice_time    = $r["voice_time"];
				$view_voice_time_copy    .= $r["voice_time"].'/';

				$view_voice .= '<div id="voice_slide_now_num_'.$i.'" style="display: block;">';//開始タグ
//				$view_voice .= 'スライド'.$i.'枚目の音声';
				$view_voice .= '<audio id="voice_slide_now_num_'.$i.'_audio" controls="" controlslist="nodownload" src="'.$voice_file_dir_path.$view_voice_data.'" style="display:none;" ></audio>';
				$view_voice .= '</div>';//終了タグ
			}
		}  
    }
    /* 行がマッチしなかった場合、voice_dataに『/』を挿入 */
  else {
	  $view_voice_data_copy      .= $r["voice_data"].'/';
	  $view_voice_time_copy .=  '3000/';
    }
  }
}

//6．SQLを作成(アイコン取得)＆アイコンリスト作成
		$sql = 'SELECT COUNT(*) FROM icon_table 
		WHERE 
		slide_group ='.$view_slide_group;

		$res = $pdo->prepare($sql);
		$status1 = $res->execute();
		
		//sqlのselect実行文(最新の音声を取得)
//		$voice_table_sql = 'SELECT * FROM icon_table 
//		WHERE user_id ='.$_SESSION["user_id"].' AND 
//		slide_group ='.$view_slide_group.' 
//		ORDER BY icon_start_time ASC';
		$voice_table_sql = 'SELECT * FROM icon_table 
		WHERE 
		slide_group ='.$view_slide_group.' 
		ORDER BY icon_start_time ASC';

		$stmt = $pdo->prepare($voice_table_sql);
		$status2 = $stmt->execute();
		//実行後、エラーだったらfalseが返る
	
	//アイコンリスト
	$view_icon_list        = array();
	$view_icon_list_all    = array();
	$view_icon_id          = "なし";
	
 if ($status1) {
	 
	$view_icon_id   = "あり";
  //DBに該当するアイコンがあるかどうかチェック
  /* SELECT 文にマッチする行数をチェック*/
  if ($res->fetchColumn() > 0) {

	  
	if($status2==false){
			queryError($stmt);
	}else{//正常
		  while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

			$view_icon_list =  array('slide_now_num'=>(int)$r["slide_now_num"], 'icon_start_time'=>(int)$r["icon_start_time"], 'icon_data'=>$r["icon_data"]);

			array_push($view_icon_list_all, $view_icon_list);

			}  
    	}
  	}
  }



//■新着プレゼン5個読み込み■

$slide_group_other_list = array();
$view_slide_other = '新着プレゼンなし';

//7．SQLを作成(スライド取得)
//①スライド総数と最新のスライドグループ5個取り出し
//①-1該当プレゼンが5つあるかチェック

$sql = 'SELECT COUNT(*) FROM slide_table 
WHERE slide_now_num = 1';

$res = $pdo->prepare($sql);
$status1 = $res->execute();


 if ($status1) {
	 //プレゼン数
	$other_presen_num = $res->fetchColumn();

	//DBに該当する音声があるかどうかチェック
	/* SELECT 文にマッチする行数をチェック*/
	if ($other_presen_num == 0) {

		//新着プレゼン0の場合、処理なし

	}else{

		//新着プレゼンが存在する場合
		if($other_presen_num > 5 ){
			$other_presen_num = 5;
		}
		
		//②該当スライドを取得
		$sql2 = 'SELECT * FROM slide_table WHERE slide_now_num = 1 ORDER BY slide_group DESC LIMIT '.$other_presen_num;

		$stmt = $pdo->prepare($sql2);
		$status2 = $stmt->execute();
		//実行後、エラーだったらfalseが返る

		//最新の取得スライドからslide_groupとslide_numを取得
		if($status2 == false){
			queryError($stmt);
		}else{//正常
			while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
				$slide_group_other = $r["slide_group"];
				array_push($slide_group_other_list, $slide_group_other);
			}
		}



	$view_slide_other = '<table class="table table-bordered table-hover table-condensed"><tbody>';//過去プレゼンリスト開始タグ

	//スライド画像ファイル名
	$slide_img_other_top ='';
	//スライド名
	$slide_name_other ='';
	//スライド作成日
	$slide_name_date ='';
	//スライド枚数
	$slide_num_other='';

	//③各1枚目のスライドを取得＆表示html作成	
		for($i=0; $i < count($slide_group_other_list); $i++){

			//sqlのselect実行結果(件数)確認用
			$stmt = $pdo->prepare("SELECT * FROM slide_table WHERE slide_group =".$slide_group_other_list[$i]." AND slide_now_num = 1");

			$status = $stmt->execute();

		//表示html作成
		if($status==false){
			queryError($stmt);
		}else{//正常
			while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

				$slide_img_other_top  = $r["slide_data"];
				$slide_name_other     = $r["slide_name"];
				$slide_date_other      = $r["create_date"];
				$slide_num_other      = $r["slide_num"];

				$view_slide_other .= '<tr id="slide_group='.$slide_group_other_list[$i].'&slide_num='.$slide_num_other.'" class="other_presen active" onclick="getId_otherpresen(this);">';
				$view_slide_other .= '<td class="other_presenlist_img ">';
				$view_slide_other .= '<img class="img-responsive img-rounded " src="upload_slide/'.$slide_img_other_top .'" alt="トップスライド"></td>';
				$view_slide_other .='<td class="other_presenlist_title"><p class="other_presen_title">'.$slide_name_other.'</p><p class="other_presen_date">'.$slide_date_other.'</p></td></tr>';

			}
		}
	  }

	  $view_slide_other .= '</tbody></table>'; //過去プレゼン終了タグ
	}

}else{//$status1エラー
			queryError($res);
}

	  


?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
 
<link href="css/presentation.css" rel="stylesheet">

 
<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>


<!--rangeslider.js スライダーバーのライブラリ読み込み-->
<link rel="stylesheet" href="rangeslider.js-2.3.0/rangeslider.css">
<script src="rangeslider.js-2.3.0/rangeslider.js"></script>


<!--recorder.js 音声録音ライブラリ読み込み-->
 <script src="Recorderjs-master/dist/recorder.js"></script>




<!--bootstrap3-->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
<!--bootstrap3-->

<!--slick-->
<link rel="stylesheet" href="slick-1.8.0/slick/slick.css">
<link rel="stylesheet" href="slick-1.8.0/slick/slick-theme.css">
<script src="slick-1.8.0/slick/slick.min.js"></script>
<!--slick-->
 
  <title>真・プレゼン共有</title>
</head>

<body>

<!-- Head[Start] -->
<header>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
          <a class="navbar-brand" href="#">真・プレゼン共有</a>
	</div>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="https://real-presen.sakura.ne.jp/presen_mk.php">プレゼン新規作成</a></li>
      <li class="active"><a href="https://real-presen.sakura.ne.jp/presen_play.php?slide_group=39&slide_num=52">プレゼン視聴</a></li>
    </ul>
  </div>
</nav>

</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div class="play_disp">
<!--class="container" 中央ぞろえ-->

	<div class="room_name"></div>

<div class="container">
  <div class="row">
	<div class="col-xs-5 col-sm-4 select_div" >
		<div>

			<div class="icon_area">
				<img id="icon" src="img/icon_sample.png" class="img-responsive img-rounded icon" alt="アイコン画像">
			</div>

			<div class="icon_info">
			  <p><?=$_SESSION["user_name"]?>さん</p> 
			</div>

		</div>

  	  <div id="recordingslist"></div>
  	  
	  <button id="all_play" type="button" class="btn btn-success all_play" onclick="all_play_btn();"><span class="glyphicon glyphicon-play"></span>　プレゼン再生</button>
	  <button type="button" class="btn btn-success all_play_stop" onclick="all_play_btn();" style="display:none;"><span class="glyphicon glyphicon-pause"></span>　一時停止</button>
	  
	<!-- Trigger -->
	<button id="link_mk" type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-link"></span>　リンク生成</button>
	<!-- Modal -->
	<div id="myModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">リンク生成</h4>
		  </div>
		  <div class="modal-body">
			<div id="link_mk">
			  <div class="input-group link_mk_div" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="本視聴リンクがコピーできます。">
			  	<span class="input-group-addon">視聴リンク</span>
				<input id="play_link" type="text" class="form-control" placeholder="スライドが登録されていません" value="">
				<div class="input-group-btn">
				  <button class="btn btn-default" onclick="clipboadCopy_play_link()">
					<i class="glyphicon glyphicon-copy"></i>
				  </button>
				</div>
			  </div>
			</div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal" >閉じる</button>
		  </div>
		</div>
	  </div>
	  
	</div>


	<div>
	<h3>新着プレゼン</h3>
	<div id ="other_presen"></div>
<!--	<a href=""><h3>And more...</h3></a>-->
	</div>

	<!--tweetボタン-->
	<div id="twbtn"></div>

	
</div>
	
<!--	<div class="col-xs-1 col-sm-1" ></div>	-->
   
		
	<div class="col-xs-7 col-sm-8" >
		<div class="slide_area">
		
	<div class="sample_slide" >
		<form id="upfile_form" method="post" action="slide_insert.php" enctype="multipart/form-data">
			<label for="upfile">
				<div class="slider0">
					<div class="sample"><img src="img/upfile_area.jpg" class="img-responsive img-rounded slide sample" alt="スライドULエリア" ></div>
				</div>
				<input type="file" id="upfile"  class="btn btn-warning"  name="upfile[]" webkitdirectory style="display:none;" />
			</label>
		</form>
	</div>
			<div class="db_slide" ><?=$view_slide?></div>
		</div>
		<div class="slidebar_area">
			<input id="rangeslider" type="range" min="0" max="100" value="0" step="0.01"  data-rangeslider>
			<output style="display:none;"></output>
			<div id="time_area" style="margin-top:10px;">
			</div>
			<div class="slidebar_btn_area">
				<button id="slide_back" type="button" class="btn slide_btn slick-arrow"  onclick="slide_back();"><span class="glyphicon glyphicon-chevron-left" ></span></button>
				<div class="slidebar_counter">
					<div class="slidebar_timer"><div id='now_time'>0:00</div>/<div id='all_time'>0:00</div></div>
					<div class="slick-counter"><span class="current"></span> of <span class="total"></span></div>
				</div>
				<button id="slide_front" type="button" class="btn slide_btn slick-arrow" onclick="slide_front();"><span class="glyphicon glyphicon-chevron-right" ></span></button>
			</div>

		</div>
		<div class="slide_info">お題
			<div id="slide_name"></div>
		</div>
	</div>

  </div>
 </div>
</div>

<!-- Main[End] -->




<script>
	
//グローバル変数----------------------

//スライド用
	//現在のスライド番号
	let slide_now_num ='';
	//スライド総数
	let slide_num ='';
	//スライドid
	let slide_id ='';
	//スライドグループid
	let slide_group ='';
	//スライド名
	let slide_name = '';
	//アップロード回数
	let slide_ul_num = 0;
	//スライドデータ(DB登録用)
	let	slide_data_ul;
	

//音声データ用
	//音声データファイル名(DB取得用)
	let voice_data;
	//音声データファイル名リスト
	let voice_data_split;
	
	//音声データ時間の総和
	let voice_time_all = 0;
	//音声データ時間(DB取得用、文字列)
	let voice_time ='';
	//デフォルト音声時間(3s)
	let default_audio_time = 3000;
	//音声総時間(表示用)
	let voice_time_all_html;
	//音声時間リスト(配列：1~)
	let voice_time_split;
	

	
//初期処理---------------------------------------
$(function () {
	
	//過去プレゼン表示処理
	let view_slide_data_other ='<?=$view_slide_other?>';
   $('#other_presen').html(view_slide_data_other);
	
	//1.スライドDBデータ取得＆表示処理--------------
	//DBのスライド有無チェック
		slide_id ='<?=$view_slide_id?>';
//		console.log("DBのスライド有無チェック",slide_id);

	if(slide_id != 'なし'){
		//スライドデバッグ用
		slide_name ='『<?=$view_slide_name?>』';
		let view_slide_data ='<?=$view_slide_data_copy?>';
		let view_slide_num = '<?=$view_slide_num?>';
		slide_group = '<?=$view_slide_group?>';

	   $('.sample_slide').remove();
	   $('#slide_name').append(slide_name);

		slide_num = view_slide_num;
		link_mk();

	   }else{
		   //データ未登録時の処理★★要検討
			$('.db_slide').remove();
		   //サンプルスライド表示時はdbから取得したスライド表示タグを削除する。でないと、slider0が2つ存在することになり、スライドのカウントがおかしくなる。
	   }
	
	//2.スライダーバー(全体)設置関数---------------------- 
	//rangeslider.js-2.3.0 を使用
	$('input[type="range"]').rangeslider({
		polyfill: false,
		// Callback function スライダー起動時
		onInit: function() {
			$('output').html(0);
		},

		// Callback function　スライダー移動時
		onSlide: function(position, value) {

			//スライド全体での秒数
			let output = $('#rangeslider').val();
//			console.log('onSlide_all_slide：',output);
			$('output').html(output);
			console.log('スライド全体での秒数:',output);

			//スライド単体での秒数
			let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;

			//アイコン表示変更処理
			let icon_name = icon_disp(onSlideEnd_time).icon_name;
			$('#icon').attr("src",icon_dir_path+icon_name); 


		},

		// Callback function() スライダー停止時
		onSlideEnd: function(position, value) {

			console.log('icon_list：',icon_list);

			//スライド単体での秒数
			let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
			let onSlideEnd_slide_num  = onSlideEnd_output().onSlideEnd_slide_num;

			//★保留★スライダーが途中にあると、頭出しできない
			if(!all_play_flag){
				//スライド手動移動フラグ:false
				slick_manual_flag = false;
				$('.slider'+slide_ul_num).slick('slickGoTo', onSlideEnd_slide_num);
				console.log('スライダーで移動',onSlideEnd_slide_num +1);

				}
		}
	});
	
	//3.スライド起動関数---------------------- 
		slickjs();

	//4.音声DBデータ取得＆表示処理----------------------

	//1)音声DBデータを取得＆表示
	//DBの音声有無チェック処理
	let db_voice_chk = '<?=$view_voice_id?>' ;
	console.log("DBの音声有無チェック(voice_id)",db_voice_chk);

		if(db_voice_chk != 'なし'){
			//音声データファイル名取得
			voice_data ='<?=$view_voice_data_copy?>';
			voice_data_split = voice_data.split('/');
			console.log('取得した音声データ：',voice_data_split);
			//phpで作成したタグを挿入
			$('#recordingslist').append('<?=$view_voice?>');
		   }

	//2)音声時間DBデータを取得＆表示
		if(db_voice_chk != 'なし'){
			
			//DBから取得した音声時間を代入
			voice_time ='<?=$view_voice_time_copy?>';
			
		   }else{
		   		//データ未登録時の処理★★要検討
			   for(let i =1;i <= slide_num;i++){
					voice_time += default_audio_time+'/';
				}
//			   console.log("voice_time",voice_time);
		   }

		//音声時間リスト作成
		voice_time_split_mk(voice_time);
		//音声総時間の表示＆スライダー更新
		voice_time_all_disp(voice_time_split);
		//スライダー更新
		$('input[type="range"]').rangeslider('update', true);
	
		//audioタグ＆音声削除ボタン表示切替処理処理
		  voice_display(slide_num,slide_now_num);

		//アイコン初期設定
		icon_set();


});

//新着プレゼンリンクからの遷移
function getId_otherpresen(ele){
    var id_value = ele.id; // eleのプロパティとしてidを取得
	window.location.href = 'https://real-presen.sakura.ne.jp/presen_play.php?'+id_value; // 商用環境遷移
	
//	window.location.href = 'http://localhost/gs/presentation/presen_play.php?'+id_value; // 開発環境遷移
	
}
	
//スライド起動関数 （slick.jsを使用）
function slickjs(){
	
	  //現在のスライド枚数表示処理
	  $('.slider'+slide_ul_num).on('init', function(event, slick) {
			$('.current').text(slick.currentSlide + 1);
			$('.total').text(slick.slideCount);
		  
		  	slide_num = slick.slideCount;//スライド総数
			slide_now_num =slick.currentSlide + 1;//現在のスライド番号
		  		  
			//audioタグ＆音声削除ボタン表示切替処理処理
			  voice_display(slide_num,slide_now_num);
		  
		//スライド手動移動検知処理1(slick-arrowボタン押下時)
		//rangeslider_change_eachtop()で使用
		$('.slick-arrow').on('click',function(){
			slick_manual_flag = true;
		});
		
		//スライド手動移動検知処理2(マウスドラッグまたはスワイプされた時のイベント)
		$('.slider'+slide_ul_num).on('swipe', function(slick, direction){
			//スライド手動移動フラグ:false
			slick_manual_flag = true;
		});
 
	  })
		  .slick({
			// option here...
		  	// マウスドラッグでスライドの切り替えをするか [初期値:true]
		  	//draggable: false,
	  		// スライド/フェードさせるスピード（ミリ秒） [初期値:300]
		  	//アイコンバグ修正のため、スピード0に変更
	  		speed: 0,
		   	// 前次ボタンを表示するか [初期値:true]
		  	arrows: false
		  
		  
	  })
		  .on('beforeChange', function(event, slick, currentSlide, nextSlide) {
			$('.current').text(nextSlide + 1);
			slide_now_num = nextSlide + 1;
			console.log('現在のスライド番号：',slide_now_num);
		//audioタグ＆音声削除ボタン表示切替処理処理
		  voice_display(slide_num,slide_now_num);
		  
		//スライダーバー(全体)移動 関数(各スライドの頭出し用)
		rangeslider_change_eachtop();

	  });
	
}
	
//音声時間リスト作成関数 
function voice_time_split_mk(voice_time){
	//音声時間リスト作成(配列：1~)
	voice_time_split = voice_time.split('/');	
	//先頭にダミーを追加(スライド番号に合わせる)
	voice_time_split.unshift(0);
	voice_time_split.pop();//末尾を削除
}
	
//音声総時間の表示＆スライダーバー(全体)更新関数
function voice_time_all_disp(voice_time_split){
	
	voice_time_all = 0;
	
	//音声時間リストを音声総和時間に変換
	for(var i =1; i < voice_time_split.length; i++){
		voice_time_split[i] = Number(voice_time_split[i]);
		voice_time_all += voice_time_split[i];
	};
	
	console.log('音声時間リスト：',voice_time_split);

	//音声総時間取得＆挿入
	voice_time_all_html = toHms(voice_time_all/1000);
	console.log('voice_time_all_html：',voice_time_all_html);	

	//音声総時間挿入(html&max)
	$('#all_time').html(voice_time_all_html);
	$('#rangeslider').attr('max',voice_time_all).change();
	
}
	


//リンク＆tweetボタン生成処理
function link_mk(){
    $('#play_link').attr("value","https://real-presen.sakura.ne.jp/presen_play.php?slide_group="+slide_group+"&slide_num="+slide_num);
	
	//tweetボタン生成
	tweet_mk();
}

//tweetボタン生成
function tweet_mk(){

	//tweetボタン表示
	let tw_html = '<a href="https://twitter.com/share" class="twitter-share-button" data-size="large" data-text="真・プレゼン共有｜オススメのプレゼン：'+slide_name+'　URL：" data-url="https://real-presen.sakura.ne.jp/presen_play.php?slide_group='+slide_group+'&slide_num='+slide_num+'">Tweet</a>';
	
	$('#twbtn').prepend(tw_html);
	
	!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
}
	
//
////②スライド変更-------------------------------------
//function slide_update(){
////	$('#rec ,#rec_stop').prop("disabled", false);
////	$('.btn_effect').css("pointer-events", "auto");
//	
//	$('#update_type').toggle();
//}
//
////1-1)スライド一枚更新を選択
//
//$('#slide_update_one').change(function(){
//
//	//1)スライドUL取得処理
//	slide_ud_one_get(this.files);
//
//	//2)DB登録処理(ajax)
//	slide_ud_one_db();
//	
//});
//
//
////1-2)スライドUL-取得処理
//function slide_ud_one_get(this_files){
//
////	//前スライド(一枚)削除
////	$('.slide'+slide_now_num).remove();
//	
//	console.log('slide_now_num',slide_now_num);
//	
//	//スライドデータ(DB登録用)
//	console.log('this_files[0]',this_files[0]);
//	slide_data_ul = this_files[0];
//	
//	//スライドデータ(html表示用)
//	let slide_data_img ='';
//	
//	//	ULされたスライドのhtmlを作成
//	let file = this_files[0];
//
//	//アップロードされたファイル名からスライド名を取得
//	slide_name = file['name'];
//	console.log('slide_name',slide_name);
//
//	// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
//	let reader = new FileReader();
//	reader.readAsDataURL(file);
//	reader.onload = function() {
//	//新しいスライドimgの挿入
//	$('.slide_img_'+slide_now_num).attr("src",reader.result);
//	}
//}
//	
////1-3)スライドUL-DB登録処理(ajax)
//function slide_ud_one_db(){
//	
//	let fd = new FormData($('#update_form_one').get(0));
//	//$postで確認
//	fd.append('slide_name', slide_name);
//	fd.append('slide_group', slide_group);
//	fd.append('slide_now_num', slide_now_num);
//
//	$.ajax({
//		type: 'POST',
//		url: 'slide_update_one.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       console.log(data);
////	slide_group = data.split('/')[1];
//	console.log('スライド変更処理終了');
////	console.log('slide_group:',slide_group);		
//	});
//}	
//
//
////2-1)スライド一括更新を選択
//
//$('#slide_update_all').change(function(){
//
//	//音声＆アイコン削除の質問
//    if( confirm("音声＆アイコンも全て削除しますがよろしいですか？") ) {
//		//音声全削除処理
//		rec_del_all();
//		//アイコン全削除処理
//		icon_del_all();
//		alert("音声＆アイコンも全て削除しました。");
//		
//		//1)スライドUL取得処理
//		slide_ul_get(this.files);
//
//		//2)DB登録処理(ajax)
//		slide_ud_all_db();
//		
//	}
//    else {
//        alert("スライド一括更新をキャンセルしました。");
//    }
//	
//});
//
//
////2-2)スライドUD-取得処理
//	//初回UL時と同様
//	//slide_ul_get(this.files);
//	
////2-3)スライドUL-DB登録処理(ajax)
//function slide_ud_all_db(){
//	
//	let fd = new FormData($('#update_form_all').get(0));
//	//$postで確認
//	fd.append('slide_name', slide_name);
//	fd.append('slide_group', slide_group);
//	fd.append('slide_data_ul', slide_data_ul);
//
//
//	$.ajax({
//		type: 'POST',
//		url: 'slide_update_all.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       console.log(data);
//	console.log('スライド登録処理終了');
//	});
//}
//	

//④音声録音機能-----------------------------------
	
  function __log(e, data) {
	  console.log('音声ログ：',e,'  ',data || '');
      log.innerHTML = "\n" + e + " " + (data || '');
  }

  var audio_context;
  var recorder;
  var PassSec = 0;   // 秒数カウント用変数
  function startUserMedia(stream) {
    var input = audio_context.createMediaStreamSource(stream);
//    __log('Media stream created.');

    // Uncomment if you want the audio to feedback directly
    //input.connect(audio_context.destination);
    //__log('Input connected to audio context destination.');
    
	 //コンストラクタ
    recorder = new Recorder(input);
//    __log('Recorder initialised.');
  }

	
//音声録音フラグ
let recording_flag = false;
	
//録音時スライドごとアイコン削除（確認）確認処理を入れると、余計なクリック音が録音されるので、本処理は保留
function rec_icon_slide_rmCheck() {

			icon_del_slide();
			startRecording();

	//録音削除ボタンの表示判定
	let decision2 = document.getElementById('voice_slide_now_num_'+slide_now_num);
	
	if(decision2){
		//要素が存在したら～
		$('#rec_del').hide();
	}

//		if( confirm("スライド"+slide_now_num+"枚目に登録済のアイコンも全て削除しますがよろしいですか？") ) {
//			//アイコンスライド単位削除処理
//			icon_del_slide();
//			alert("スライド"+slide_now_num+"枚目に登録済のアイコンを全て削除しました。続けて録音を開始します。");
//			startRecording();
//
//		}
//		else {
//			alert("録音処理をキャンセルしました。");
//		}

}
//	
//	
////録音開始
//
//function startRecording() {
//
//	   
//	//音声録音フラグ
//	recording_flag = true;
//
//	//スライダーバー(全体)移動 関数(各スライドの頭出し用)
//	rangeslider_change_eachtop();
//
//	//録音関数
//	recorder && recorder.record();
//
//	//録音ボタン表示切替処理＆他ボタン無効処理
//	$('#rec').toggle();  
//	$('#rec_stop').toggle();
//	$('audio, .slick-arrow ,.db_slide,.rangeslider ,#all_play ').css("pointer-events", "none");
//
//	// タイマーをセット(1000ms間隔)
//	PassSec = 0;
//	PassageID = setInterval('show_voicelog()',1000);  
//
//}
//
//function show_voicelog() { 
//	PassSec++
//	__log('Recording...   ('+PassSec+'s)');
//}
//	
//	//録音停止
//function stopRecording(button) {
//
//	//音声録音フラグ
//	recording_flag = false;
//
//    recorder && recorder.stop();
//
//	//録音ボタン表示切替処理＆他ボタン有効処理
//	$('#rec').toggle();  
//	$('#rec_stop').toggle();
//	$('audio, .slick-arrow ,.db_slide,.rangeslider, #all_play ').css("pointer-events", "auto");
//
//    __log('Stopped recording.');
//	  
//	 // タイマーのクリア
//	 clearInterval( PassageID );  
//    
//    // create WAV download link using audio data blob
//    createDownloadLink();
//    recorder.clear();  
//  }
//
//  function createDownloadLink() {
//    recorder && recorder.exportWAV(function(blob) {
//	
////	console.log('音声データ：',blob);
//		
//	 //音声データを仮想urlに変換(セッション内のみ有効)
//      var url = URL.createObjectURL(blob);
//	//タグ作成
//      var div = document.createElement('div');
//      var au = document.createElement('audio');
//      var hf = document.createElement('a');
//
//	//旧音声(divタグ)削除 
//	 $('#voice_slide_now_num_'+slide_now_num).remove();
//
//	//divタグ編集
//	div.id = 'voice_slide_now_num_'+slide_now_num;
//	div.style ="display: block;";
////	div.innerHTML = 'スライド'+slide_now_num+'枚目の音声';
//
//		
//	//audioタグ編集
//      au.controls = true;
//      au.src = url;
//	  au.id = 'voice_slide_now_num_'+slide_now_num+'_audio';
//		au.style ="display:none;";
//		
//	//aタグ(音声DL)編集
//      hf.href = url;
//	//ダウンロード属性
//      hf.download = new Date().toISOString() + '.wav';
//      hf.innerHTML = hf.download;
//	//html挿入
//      div.appendChild(au);
//	//div.appendChild(hf);
//      recordingslist.appendChild(div);
//		
//	var audio = new Audio(); // audioの作成
//	audio.src = url; // 音声ファイルの指定
//	audio.load(); // audioの読み込み 
//	audio.addEventListener('loadedmetadata',function(e) {
//		// 音声時間の取得
//		let voice_time = audio.duration*1000;
//		console.log('時間(ms)：',voice_time); 
//		voice_ul(blob,voice_time);//音声アップロード
//
//		//音声時間リストに代入
//		voice_time_split[slide_now_num] = voice_time;
//		//音声総時間の表示＆スライダー更新
//		voice_time_all_disp(voice_time_split);
//		$('input[type="range"]').rangeslider('update', true);
//
//		});
//		
//		//audioタグ＆音声削除ボタン表示切替処理処理
//		  voice_display(slide_num,slide_now_num);
//
//    });
//	  
//
//  }

	//サポートチェック、マイクチェック？
	  window.onload = function init() {
		try {
		  // webkit shim
		  window.AudioContext = window.AudioContext || window.webkitAudioContext;
		  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
		  window.URL = window.URL || window.webkitURL;

		  audio_context = new AudioContext;
//		  __log('Audio context set up.');
//		  __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
		} catch (e) {
		  alert('No web audio support in this browser!');
		}

		navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
		  __log('No live audio input: ' + e);
		});
	  };
//	
//function voice_ul(soundBlob,voice_time){
//
//	//送信データ作成
//	var fd = new FormData();
//		//$fileで確認
//		fd.append('sound_blob', soundBlob);
//		//$postで確認
//		fd.append('file_name', 'slide'+slide_now_num+'_voice.wav');
//		fd.append('slide_name', slide_name);
//		fd.append('slide_group', slide_group);
//		fd.append('slide_now_num', slide_now_num);
//		fd.append('voice_time', voice_time);
//	
//	$.ajax({
//		type: 'POST',
//		url: 'voice_insert_update.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       console.log(data);
//	console.log('音声登録処理終了');
//	});
//
//
//}

//audioタグ＆音声削除ボタン表示切替処理処理
//(slide_now_numはセレクタに使用しないほうが良い。なぜかは不明。挙動がおかしくなる)
function voice_display(slide_num,slide_now_num){

	//audioタグ表示切替処理処理
	 for (let x = 1; x <= slide_num; x++){
		let decision = $('#voice_slide_now_num_'+x).css('display');
		 
		if(slide_now_num === x){
			if(decision === 'none'){
				//要素が非表示だったら～
				$('#voice_slide_now_num_'+x).show();
			}
		}else{
			if(decision === 'block'){
				//要素が表示だったら～
				$('#voice_slide_now_num_'+x).hide();
			}
		}
	}
	
	//録音削除ボタンの表示判定
	let decision2 = document.getElementById('voice_slide_now_num_'+slide_now_num);
	
	if(decision2){
		//要素が存在したら～
		$('#rec_del').show();
	}else{
		//要素が存在しなかったら
		$('#rec_del').hide();

	}
}
	
//
////⑤音声削除機能-----------------------------------
//
////音声削除時スライドごとアイコン削除確認
//function del_rec_icon_slide_rmCheck() {
//	
//	if( confirm("スライド"+slide_now_num+"枚目に登録済のアイコンも全て削除しますがよろしいですか？") ) {
//		//アイコンスライド単位削除処理
//		icon_del_slide();
//		//スライド音声のみ削除処理
//		del_Record_one();
//	}
//	else {
//		alert("音声削除処理をキャンセルしました。");
//	}
//}
//
//	
////1)1スライドの音声のみ削除
//function del_Record_one(){
//
//	//旧divタグ(旧音声)削除 
//	 $('#voice_slide_now_num_'+slide_now_num).remove();
//
//	//DB＆ファイル削除処理
//	voice_del_one();
//	
//	//audioタグ＆音声削除ボタン表示切替処理処理
//	  voice_display(slide_num,slide_now_num);
//}
//	
//function voice_del_one(){
//	//送信データ作成
//	var fd = new FormData();
//		//$postで確認
//		fd.append('slide_group', slide_group);
//		fd.append('slide_now_num', slide_now_num);
//	
//	$.ajax({
//		type: 'POST',
//		url: 'voice_del_one.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//		console.log(data);
//
//		//スライダー(全体)更新処理
//		//音声時間リストにデフォルト時間を代入
//		voice_time_split[slide_now_num] = default_audio_time;
//		//音声総時間の表示＆スライダー更新
//		voice_time_all_disp(voice_time_split);
//		$('input[type="range"]').rangeslider('update', true);
//
//		console.log('音声削除処理終了');
//	});
//}
//	
//	
////2)全スライドの音声削除-----------
//function rec_del_all(){
//
//	//旧divタグ(旧音声)全削除 
//	$("#recordingslist>div").remove();
//	
//	//DB＆ファイル削除処理
//	voice_del_all();
//
//}
//	
//	
//function voice_del_all(){
//	//送信データ作成
//	var fd = new FormData();
//		//$postで確認
//		fd.append('slide_group', slide_group);
//	
//	return $.ajax({
//		type: 'POST',
//		url: 'voice_del_all.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       	console.log(data);
//		
//		//スライダー(全体)更新処理
//		//音声時間リストにデフォルト時間を代入
//		for(let i =1;i <= slide_num;i++){
//			voice_time_split[i] = default_audio_time;
//		}
//		//音声総時間の表示＆スライダー更新
//		voice_time_all_disp(voice_time_split);
//		$('input[type="range"]').rangeslider('update', true);
//		
//		console.log('音声削除処理終了');
//	});
//}
//	
	
//⑥自動再生/一時停止ボタン押下後-----------------------
	
//1)自動再生処理
let all_play_flag = false;
let TARGET;
	
function all_play_btn(){
	if(all_play_flag){
		all_play_stop_tmp();
	}else{
		all_play();
		//audioタグ、スライド遷移無効、再生ボタン切り替え
		all_play_btn_start();
	}
}
	
function all_play(){
	all_play_flag = true;
	let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
	
	if(onSlideEnd_output().onSlideEnd_slide_num+1 == slide_num && onSlideEnd_time == voice_time_split[slide_num]){
		onSlideEnd_time = 0;
		$('.slider'+slide_ul_num).slick('slickGoTo', 0);
	}
	
	all_play_true(onSlideEnd_time);
}
	
function all_play_true(onSlideEnd_time){
	TARGET =  document.getElementById('voice_slide_now_num_'+slide_now_num+'_audio');

//	console.log('音声存在チェック',TARGET != null,TARGET);

	if(TARGET){
		TARGET.currentTime = onSlideEnd_time/1000;
		
		TARGET.play();
		//audioタグシークバー同期処理
		
		let NOW = TARGET.currentTime;
		let TOTAL = TARGET.duration*1000+100;
		TOTAL = TOTAL - NOW;
//		console.log('NOW：',NOW);
//		console.log('TOTAL：',TOTAL);
		
		//音声再生時スライダーバー(全体)同期
		if(!TARGET.paused ){
			TARGET.addEventListener("timeupdate", function() {
				//ターゲット切り替え時のバグ防止用の判定処理
				if(TARGET){
					let NOW_all_disp = 0;
					NOW_all_disp = NOW_all_set() + TARGET.currentTime*1000;
					//スライダーバー(全体)移動
					rangeslider_change(NOW_all_disp);
				}
			}, true);
		}
		setTimeout(next_slide, TOTAL);
	}else{
		let TOTAL = default_audio_time;//デフォルト3s
		console.log('TOTAL：',TOTAL);
		
		//音声再生時シークバー(全体)同期処理
		//現在のスライダー位置を加算
		PassSec = onSlideEnd_time;
		// タイマーをセット(500ms間隔)
		PassageID = setInterval('show_NOW_all_disp(0)',1000);
		setTimeout(next_slide, TOTAL);
	}
}

var next_slide = function(){
	if(slide_now_num !== slide_num && all_play_flag){
		//次のスライドへ移動
		$('.slider'+slide_ul_num).slick('slickNext');
		all_play_true(0);
	}else{
		//audioタグ、スライド移動有効
		all_play_btn_stop();
		all_play_flag = false;

//			最初のページに戻す処理　無効化
//			$('.slider'+slide_ul_num).slick('slickNext');
	}
}

	
//2)一時停止処理
function all_play_stop_tmp(){
	if(TARGET){
		TARGET.pause();
	}else{
		//音声データ未登録時のタイマーのクリア
		clearInterval( PassageID );
	}
	
	all_play_flag = false;
	//audioタグ、スライド移動有効
	all_play_btn_stop();
	
}
//スライダーバー(全体)遷移関数 (音声データがなかった時用)
function show_NOW_all_disp(NOW_all_disp) {

	PassSec += 1000;
	if(PassSec < default_audio_time){
		NOW_all_disp = NOW_all_set() + PassSec;
		//スライダーバー(全体)移動
		rangeslider_change(NOW_all_disp);
//		console.log('スライダーバー位置',NOW_all_disp);
	}else{
		NOW_all_disp = NOW_all_set() + PassSec;
		//スライダーバー(全体)移動
		rangeslider_change(NOW_all_disp);
//		console.log('スライダーバー位置_last',NOW_all_disp);
		//タイマークリア
		clearInterval( PassageID );
	}
}
	
//音声時間の総和取得関数(直前のスライドまで)
function NOW_all_set(){
		let NOW_all =0;
		for(var i = 1; i < slide_now_num; i++){
			NOW_all += voice_time_split[i];
		}
		console.log('NOW_all_set：',NOW_all);
		return NOW_all;
	}

//スライダーバー(全体)移動 関数
function rangeslider_change(NOW_all_disp){
	$('#rangeslider').val(NOW_all_disp).change();
	$('#now_time').html(toHms(NOW_all_disp/1000));
}



//スライド手動移動フラグ
let slick_manual_flag = false;
	
//スライダーバー(全体)移動 関数(各スライドの頭出し用)
function rangeslider_change_eachtop(){
	if(all_play_flag == false){
		
		if(slick_manual_flag || recording_flag){

//頭出し処理★失敗
//			//前回のスライド単体での秒数
//			let before_onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
//			let before_onSlideEnd_slide_num  = onSlideEnd_output().onSlideEnd_slide_num;
//			let all_next_time =0;
//			
//			if(before_onSlideEnd_time > 0){
//				//総時間を取得
//				for(var i = 1; i < before_onSlideEnd_slide_num+1; i++){
//					all_next_time += voice_time_split[i];
//				}
//				console.log('all_next_time',all_next_time);
//				let slide_next_num = before_onSlideEnd_slide_num+1;
//				console.log('slide_next_num',slide_next_num);
//				rangeslider_slick_change(all_next_time , slide_next_num);
//
//				
//			}else{
				console.log('slide_now_num',slide_now_num);
				//スライド手動移動中or録音中のときは頭出し
				let NOW_all_disp = NOW_all_set();
				rangeslider_change(NOW_all_disp);
				//スライド手動移動フラグ
				slick_manual_flag = false;
//			}
			
			
		}else{
			//スライダーバーでスライドさせたときは同期
			let NOW_all_disp = NOW_all_set();
			//.rangesliderにてスライダー(全体)変更時の秒数加算
			NOW_all_disp += onSlideEnd_output().onSlideEnd_time;
			rangeslider_change(NOW_all_disp);
			//スライド手動移動フラグ:true(次からは頭出し)
			slick_manual_flag = true;
		}
	}
}


//⑦停止ボタン押下後 不要？⇒削除(スライダー(全体)があるため)
//function all_play_stop(){
//	all_play_flag = false;
//	if(document.getElementById('voice_slide_now_num_'+slide_now_num+'_audio') != null){
//		TARGET.currentTime = 0;
//		TARGET.pause();
//	}
//	//audioタグ、スライド移動有効
//	all_play_btn_stop();
//}
	

//audioタグ、スライド移動、スライダー移動無効、
//再生ボタン切り替え
function all_play_btn_start(){
		$('audio, .slick-arrow ,.db_slide, .rangeslider, #rec').css("pointer-events", "none");
		$('.all_play').hide();
		$('.all_play_stop').show();
}

//audioタグ、スライド移動、スライダー移動有効、
//再生ボタン切り替え
function all_play_btn_stop(){
		$('audio, .slick-arrow ,.db_slide ,.rangeslider ,#rec').css("pointer-events", "auto");
		$('.all_play').show();
		$('.all_play_stop').hide();
}


//音声総時間(表示用) 作成関数	
function toHms(t) {
	var hms = "";
	var h = t / 3600 | 0;
	var m = t % 3600 / 60 | 0;
	var s = Math.round(t % 60);

	if (h != 0) {
		hms = h + ":" + padZero(m) + ":" + padZero(s);
	} else if (m != 0) {
		hms = m + ":" + padZero(s);
	} else {
		hms = "00:" + padZero(s) ;
	}

	return hms;

	function padZero(v) {
		if (v < 10) {
			return "0" + v;
		} else {
			return v;
		}
	}
}
	


	
//スライドバー移動時、スライド単体の秒数算出関数
	//以下の関数で使用
	//$('input[type="range"]').rangeslider(スライド移動)
	//rangeslider_change_eachtop()
	//all_play_true()
	
function onSlideEnd_output(){
	
	//現在のスライド全体での秒数
	let output = $('#rangeslider').val();
	
	//スライド単体での秒数
	let onSlideEnd_time = output;
	//比較総時間用
	let time = 0;
	//算出したスライド番号(0から始まる、スライド番号より1小さい数)
	let onSlideEnd_slide_num = 0;
	
	//何枚目のスライドか算出する
	for(let i =0; i < slide_num; i++){
		time += voice_time_split[i];
//		console.log('比較',output,time,time + voice_time_split[i+1]);
		
		//最後のスライドの場合、最終秒は最後のスライドに含める
		if((output >= time  && output < (time + voice_time_split[i+1]))||(i == (slide_num - 1) && output == (time + voice_time_split[i+1]))){
			onSlideEnd_slide_num = i;
			onSlideEnd_time = output - time;
		}
	}
	
	onSlideEnd_time = Number(onSlideEnd_time);
	
	let onSlideEnd_return ={"onSlideEnd_time":onSlideEnd_time,"onSlideEnd_slide_num":onSlideEnd_slide_num};
//	console.log('onSlideEnd_return',onSlideEnd_return);
	
	return onSlideEnd_return;
}

	
//アイコン表示処理-----------------------------------------
	
//アイコンリスト
let icon_list = [];
//アイコン画像格納場所
let icon_dir_path = 'upload_icon/';
//アイコン画像のsrc
let icon_src = '';
//デフォルトアイコン
let default_icon_name = '<?=$_SESSION["user_icon"]?>';
//アイコン全削除フラグ
let icon_del_all_flg = false;
//アイコンスライド単位削除_スライド番号
let icon_del_slide_num = 0;
//現在表示すべきアイコン番号
let icon_disp_list_num =0;

	
//アイコン初期設定
function icon_set(){

	//デフォルトアイコンリスト作成処理
	icon_list_default();

	//DBアイコンリスト受信処理
	//1)アイコン声DBデータを取得＆表示
	let db_icon_chk = '<?=$view_icon_id?>' ;
	console.log("DBのアイコン有無チェック(icon_id)",db_icon_chk);

	if(db_icon_chk != 'なし' ){

		//DBアイコンデータリスト取得
		let icon_list_db = <?php echo json_encode($view_icon_list_all, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

		console.log('icon_list_db：',icon_list_db);	

		//デフォルトアイコンリスト＋DBアイコンリスト結合処理
		for(let i = 0; i < icon_list_db.length; i++){
			icon_list_mk(icon_list_db[i].slide_now_num,icon_list_db[i].icon_data,icon_list_db[i].icon_start_time);
		}
   }

	//最初のアイコン表示処理
	icon_src = icon_list[0].icon_data;
	$('#icon').attr("src",icon_dir_path+icon_src);
	//アイコン枚数表示処理
	icon_num_disp(1);

}


//デフォルトアイコンリスト作成処理
function icon_list_default(){
	
	//アイコンリスト初期化
	icon_list = [];
	
	//デフォルトアイコンリスト作成処理
	for(let i = 1; i <= slide_num; i++){
		//アイコンリストデータ
		let icon_list_data = {"slide_now_num":i,"icon_start_time":0,"icon_data":default_icon_name};		
		icon_list.push(icon_list_data);
	}
	console.log('icon_listデフォルト：',icon_list);	
}
	
//1)アイコン表示算出処理-----------------
function icon_disp(onSlideEnd_time){

	//新アイコン画像のsrc
	let icon_src_new = '';
	//新アイコン画像のリスト番号
	let icon_list_num_new = '';
	
	//現在表示すべきアイコンを算出
	for(let i = 0; i < icon_list.length ; i++){
		//1)スライド番号チェック
		if(icon_list[i].slide_now_num == slide_now_num){
			//2)スライド中の経過時間チェック
			if(onSlideEnd_time >= icon_list[i].icon_start_time){
				//スライド中の経過時間がicon開始時間を超えていたら、画像を変更。一番遅い開始時間の画像を取得。
				icon_src_new = icon_list[i].icon_data;
				icon_list_num_new = i;
			   }
		   }
	   }

	
	//icon返却
	console.log('現在表示すべきアイコン：',icon_src_new);
	console.log('現在表示すべきアイコン番号：',icon_list_num_new);
	let icon_disp_return ={"icon_name":icon_src_new,"icon_list_num":Number(icon_list_num_new)};
	
	
	//アイコン枚数表示処理
	icon_num_disp(icon_list_num_new+1);
	
	return icon_disp_return;
}

	
////2)一つ前のアイコンを表示する関数
//function icon_back(){
//	
//	//スライド単体での秒数
//	let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
//	
//	//現在のアイコンリスト番号取得
//	let icon_list_num = icon_disp(onSlideEnd_time).icon_list_num;
//
//	console.log('icon_list_num_now',icon_list_num);
//
// 	//移動後の総時間
//	let all_next_time = 0;
//	//移動後のスライド番号
//	let slide_next_num = 1;
//	
//	if(icon_list_num !== 0){
//		//アイコンリスト番号を一つ戻す
//		icon_list_num--;
//		slide_next_num = icon_list[icon_list_num].slide_now_num;
//		//総時間を取得
//		for(var i = 1; i < slide_next_num; i++){
//			all_next_time += voice_time_split[i];
//		}
//		
//	all_next_time += icon_list[icon_list_num].icon_start_time;
//	}
//
//	console.log('次のiconリスト番号',icon_list_num);
//	console.log('次のスライド番号',slide_next_num);
//	console.log('次の総時間',all_next_time);
//	console.log('icon_list',icon_list);
//	
//  //スライダー＆スライダーバー(全体)一括移動関数
//  rangeslider_slick_change(all_next_time,slide_next_num);
//
//}
//
//	
////3)一つ後のアイコンを表示する関数
//function icon_front(){
//	
//	//スライド単体での現在の秒数
//	let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
//	
//	//現在のアイコンリスト番号取得
//	let icon_list_num = icon_disp(onSlideEnd_time).icon_list_num;
//
//	console.log('現在のアイコン番号',icon_list_num);
//
// 	//移動後の総時間
//	let all_next_time = 0;
//
//	//移動後のスライド番号(デフォルトは最終スライド)
//	let slide_next_num = slide_num;
//	
//	if(icon_list_num !== icon_list.length - 1){
//		//アイコンリスト番号を一つ進める
//		icon_list_num++;
//		//スライド番号を更新
//		slide_next_num = icon_list[icon_list_num].slide_now_num;
//		//総時間を取得
//		for(var i = 1; i < slide_next_num; i++){
//			all_next_time += voice_time_split[i];
//		}
//		
//	}else{
//		//最終スライドまでの総時間を取得
//		for(var i = 1; i < slide_next_num; i++){
//			all_next_time += voice_time_split[i];
//		}
//	}
//	
//	all_next_time += icon_list[icon_list_num].icon_start_time;
//
//	console.log('次のアイコンリスト番号',icon_list_num);
//	console.log('次のスライド番号',slide_next_num);
//	console.log('次の総時間',all_next_time);
//		console.log('icon_list',icon_list);
//
//  //スライダー＆スライダーバー(全体)一括移動関数
//  rangeslider_slick_change(all_next_time,slide_next_num);
//
//}
//	
//	
////①アイコンUL処理-----------------
//	
//$('#upicon').change(function(){
//	console.log('処理開始');
//	
//	//スライド単体での秒数(icon_start_timeになる)
//	let icon_start_time_new = onSlideEnd_output().onSlideEnd_time;
//
//	//1)アイコン登録情報取得処理
//	icon_ul_get(this.files);
//
//	//2)DB登録処理(ajax)
//	icon_ul_db(icon_start_time_new);
//	
//});
//
////自動再生中、アイコン変更ボタン押下⇒自動再生停止処理
//function icon_rec_check(){
//	console.log('all_play_flag',all_play_flag);
//	if(all_play_flag){
//	  		all_play_stop_tmp(); 
//	   }
//}
//
////1)アイコン登録情報取得処理
//function icon_ul_get(this_files){
//
//	//アイコンデータ
//	let file = this_files[0];
//	console.log('this_files',file);
//	//ULするアイコンのsrc
//	let icon_src_ul = '';
//
//	//アップロードされたファイル名からアイコン名を取得
//	let icon_name = file['name'];
//	console.log('icon_name',icon_name);
//
//	// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
//	let reader = new FileReader();
//	reader.readAsDataURL(file);
//	reader.onload = function() {
//		//icon変更
//		icon_src_ul = reader.result;
//		$('#icon').attr("src",icon_src_ul); 
//	}
//}
//
//
//	
////2-2)アイコンUL-DB登録処理(ajax)
//function icon_ul_db(icon_start_time_new){
//	
//	let fd = new FormData($('#upicon_form').get(0));
//	//$postで確認
//	fd.append('slide_group', slide_group);
//	fd.append('slide_now_num', slide_now_num);
//	fd.append('icon_start_time', icon_start_time_new);
//	//icon_start_timeが重複する場合はupdate処理
//
//	$.ajax({
//		type: 'POST',
//		url: 'icon_insert_update.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       console.log(data);
//	console.log('アイコン登録処理終了');
//		
//	//アイコン名取得
//	let icon_name = data.split('new_file_name')[1].split('/')[1];
//	console.log('新icon_name:',icon_name);
//	//アイコンリスト更新
//	icon_list_mk(slide_now_num,icon_name,icon_start_time_new);
//		
//	});
//}
	

//アイコンリスト更新関数(リスト作成＆枚数表示＆アイコン差し替え)
function icon_list_mk(slide_now_num,icon_name,icon_start_time_new){
	console.log('icon_start_time_new：',icon_start_time_new);

	//新アイコン情報
	let icon_new ={"slide_now_num":slide_now_num,"icon_start_time":icon_start_time_new,"icon_data":icon_name};
	//該当スライド末尾までのアイコン数
	let icon_num_slide_last;
	//新アイコンリスト挿入位置までのアイコン数(該当スライド中にデフォルト以外のiconがある場合)
	let icon_num_slide_in;

	if(slide_now_num == 1 && icon_start_time_new == 0){
	   		//1枚目のスライドの初めの画像はspliceでは無理
			//直接書き換える
			icon_list[0] = icon_new; 

	}else{
		//新アイコン情報作成
		for(let i = 0; i < icon_list.length ; i++){
			//1)スライド番号チェック
			if(icon_list[i].slide_now_num == slide_now_num){
				//該当スライド末尾までのアイコン数
				icon_num_slide_last = i+1;

				//2)新アイコン表示時間開始のチェック
				if(icon_start_time_new < icon_list[i].icon_start_time){
					//旧icon開始時間よりはやいicon開始時間があれば、ひとつ少ない個数を記録。(i個目)
					//上記の条件中で一番大きいアイコン数が残る
					icon_num_slide_in = i;
				   }else if(icon_start_time_new == icon_list[i].icon_start_time){
					   //[i]のリスト削除(実質上書き保存)
						icon_list.splice(i, 1); 
						icon_num_slide_in = i;
					   
				   }
			   }
		   }

		//アイコンリスト挿入
		if(icon_num_slide_in){
			// 先頭から第1引数個無視、そのあとに追加
			icon_list.splice(icon_num_slide_in, 0, icon_new );
			//アイコン枚数表示処理
			icon_num_disp(icon_num_slide_in+1);

		}else{
			icon_list.splice(icon_num_slide_last, 0, icon_new ); 
			//アイコン枚数表示処理
			icon_num_disp(icon_num_slide_last+1);
	   }
	}
		//アイコン挿入
//		console.log('挿入icon：',icon_new);
//		console.log('新icon_list：',icon_list);
		$('#icon').attr("src",icon_dir_path+icon_name);
}
	
//アイコン枚数表示処理
function icon_num_disp(icon_num){
	$('.icon_current').html(icon_num);
	$('.icon_total').html(icon_list.length);
}
	
//
////1)アイコン全削除処理
//function icon_del_all(){
//	
//	//アイコンスライド単位削除スライド番号
//	icon_del_slide_num = 0;
//	//1)アイコン全初期化処理(javascript)
//	icon_reset_all();		
//	//2)DB削除処理(ajax)
//	icon_del_db();
//	
//	console.log('新しいicon_list：',icon_list);
//}
//
////2)アイコンスライド単位削除処理
//function icon_del_slide(){
//	
//	//アイコンスライド単位削除スライド番号
//	icon_del_slide_num = slide_now_num;
//	//1)アイコンスライド単位初期化処理(javascript)
//	icon_reset_slide();		
//	//2)DB削除処理(ajax)
//	icon_del_db();
//	
//	console.log('新しいicon_list：',icon_list);
//}
//	
//
//function icon_del_db(){
//	//送信データ作成
//	var fd = new FormData();
//		//$postで確認
//		fd.append('slide_group', slide_group);
//		//全削除:0、スライド単位削除:0以上
//		fd.append('slide_now_num', icon_del_slide_num);
//
//	$.ajax({
//		type: 'POST',
//		url: 'icon_del.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//		console.log(data);
//		console.log('アイコン初期化処理終了');
//		//アイコンスライド初期化処理番号を初期値に戻す
//		icon_del_slide_num = 0;
//	});
//}
//	
//
/////アイコン全初期化処理
//function icon_reset_all(){
//
//	//デフォルトアイコンリスト作成処理
//	icon_list_default();
//	
//	//最初のアイコン表示処理
//	icon_src = icon_list[0].icon_data;
//	$('#icon').attr("src",icon_dir_path+icon_src);
//	//アイコン枚数表示処理
//	icon_num_disp(1);
//}
//	
//	
////アイコンスライド単位初期化処理
//function icon_reset_slide(){
//
//	//アイコン削除リスト([0]番目からlength分削除)	
//	let icon_splice_list = [];
//
//	//該当スライドアイコン初期化処理
//	for(let i = 0; i < icon_list.length ; i++){
//		//スライド番号チェック
//		if(icon_list[i].slide_now_num == slide_now_num){
//			icon_splice_list.push(i);
//	   }
//   }
//
//	console.log('アイコンスライド単位削除実行中',icon_splice_list,'のアイコンを削除');
//	
//	//該当スライド初期アイコン挿入処理
//	//※icon_list_mkではスライドの最初のアイコンは上書き保存される
//	//アイコンリスト更新関数(リスト作成＆枚数表示＆アイコン差し替え)
//	icon_list_mk(slide_now_num,default_icon_name,0);
//	
//	//スライドの最初のアイコン以外削除
//	//icon_splice_list[1]番目から要素数分-1
//	icon_list.splice(icon_splice_list[1],icon_splice_list.length-1);
//
//}
//
////2)アイコン単体削除処理
//function icon_del_one(){
//	
//	//1)アイコン単体削除リスト更新(javascript)
//	let icon_reset_one_data = icon_reset_one();		
//	//2)DB削除処理(ajax)
//	icon_del_one_db(icon_reset_one_data);
//	
//	console.log('新しいicon_list：',icon_list);
//}
//
//	
////アイコン単体削除処理
//function icon_reset_one(){
//	
//	let icon_reset_one_data;
//
//	//スライド単体での秒数
//	let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
//	
//	let icon_list_del_num = icon_disp(onSlideEnd_time).icon_list_num;
//	
//	console.log('アイコン単体削除実行中',icon_list_del_num,'番目のアイコンを削除');
//	let icon_time_chk = icon_list[icon_list_del_num].icon_start_time;
//	
//	if(icon_time_chk == 0){
//		console.log('スライド最初のアイコン。デフォルト上書き保存');
//		icon_list[icon_list_del_num].icon_data = default_icon_name;
//
//		//アイコン枚数表示処理(上書き保存なので、リスト番号表示には繰り上げが必要)
//		icon_num_disp(icon_list_del_num+1);
//		//(デフォルト)アイコン挿入
//		$('#icon').attr("src",icon_dir_path+default_icon_name);
//		
//		//削除情報取得
//		icon_reset_one_data = icon_list[icon_list_del_num];
//
//	   }else{
//
//		//削除情報取得
//		icon_reset_one_data = icon_list[icon_list_del_num];
//
//		console.log('スライド中のアイコン。削除'); icon_list.splice(icon_list_del_num,1);
//		   
//		//アイコン枚数表示処理(削除後の番号なので-1されるからリスト番号表示は繰り上げなくてよい)
//		icon_num_disp(icon_list_del_num);
//		//(デフォルト)アイコン挿入
//		   $('#icon').attr("src",icon_dir_path+icon_list[icon_list_del_num-1].icon_data);
// 	   }
//	
//	return icon_reset_one_data;
//}
//
//
//function icon_del_one_db(icon_reset_one_data){
//	
//	console.log('icon_reset_one_data',icon_reset_one_data);
//	
//	//送信データ作成
//	var fd = new FormData();
//		//$postで確認
//		fd.append('slide_group', slide_group);
//		//全削除:0、スライド単位削除:0以上
//		fd.append('slide_now_num', icon_reset_one_data.slide_now_num);
//		fd.append('icon_start_time', icon_reset_one_data.icon_start_time);
//	
//	$.ajax({
//		type: 'POST',
//		url: 'icon_del_one.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//		console.log(data);
//		console.log('アイコン単体削除処理終了');
//	});
//}
	
//スライダー＆スライダーバー(全体)一括移動関数
function rangeslider_slick_change(all_next_time,slide_next_num){
	//all_next_time：移動後の総時間
	//slide_next_num：移動後のスライド番号
	
	console.log('slide_next_num',slide_next_num);
	console.log('all_next_time',all_next_time);
	
	//スライダーバー(全体)移動
	rangeslider_change(all_next_time);

	//スライド手動移動フラグ
	slick_manual_flag = false;
	
	//スライド移動 
	$('.slider'+slide_ul_num).slick('slickGoTo', slide_next_num-1);
}
	
function slide_back(){
	// 前のスライドへ移動
	$('.slider'+slide_ul_num).slick('slickPrev');
}
	
function slide_front(){
	//スライド移動 
	$('.slider'+slide_ul_num).slick('slickNext');
}
	
//視聴リンク生成　コピー処理
let clipboadCopy_play_link = function(){
	var urltext = document.getElementById("play_link");
	urltext.select();
	document.execCommand("copy");
}

$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
});

</script>
  
</body>
</html>
