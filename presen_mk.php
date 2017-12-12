<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

//セッション関数(ユーザ情報)
$_SESSION["user_name"] = 'テストユーザ' ;
$_SESSION["user_id"] = '1' ;

//データがないときの処理記述要？★


//2. DB接続
$pdo = db_con();
	
//３．SQLを作成(スライド取得)

$view_slide_group ="なし";
$view_slide_num  ='';//スライドの総数

//①スライド総数と最新のスライドグループ(★要検討)を取得
	$stmt = $pdo->prepare("SELECT * FROM slide_table WHERE user_id =".$_SESSION["user_id"]." ORDER BY slide_group DESC LIMIT 1");
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

	//最新の取得スライドからslide_groupとslide_numを取得
	if($status==false){
		queryError($stmt);
	}else{//正常
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$view_slide_group = $r["slide_group"];
			$view_slide_num      = $r["slide_num"];
		}
	}
		
	
$view_slide = '<div class="slider0">';//slider開始タグ
$view_slide_id   = "なし";
$view_slide_data ='';//スライド画像ファイル名
$view_slide_data_copy ='' ;//デバック用
$view_slide_name ='';//スライド名
$file_dir_path = "upload/";  //画像ファイル保管先

//②スライドを一枚ずつ取得＆表示html作成	
	for($i=1; $i <= $view_slide_num; $i++){

		//sqlのselect実行結果(件数)確認用
		$stmt = $pdo->prepare("SELECT * FROM slide_table WHERE user_id =".$_SESSION["user_id"]." AND
			slide_group =".$view_slide_group." AND 
			slide_now_num =".$i." 
			ORDER BY slide_id DESC LIMIT 1");
		
		$status = $stmt->execute();

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
	$voice_file_dir_path = "upload_sound/";  //画像ファイル保管先

for($i=1; $i <= $view_slide_num; $i++){
		
		//sqlのselect実行結果(件数)確認用
		$sql = 'SELECT COUNT(*) FROM voice_table 
		WHERE user_id ='.$_SESSION["user_id"].' AND 
		slide_group ='.$view_slide_group.' AND 
		slide_now_num ='.$i;
	
		$res = $pdo->prepare($sql);
		$status1 = $res->execute();
		
		//sqlのselect実行文(最新の音声を取得)
		$voice_table_sql = 'SELECT * FROM voice_table WHERE user_id ='.$_SESSION["user_id"].' AND 
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

				$view_voice .= '<div id="slide_now_num_'.$i.'" style="display: block;">';//開始タグ
				$view_voice .= 'スライド'.$i.'枚目の音声';
				$view_voice .= '<audio id="slide_now_num_'.$i.'_audio" controls="" src="'.$voice_file_dir_path.$view_voice_data.'" ></audio>';
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
 
  <title>home</title>
</head>

<body>

<!-- Head[Start] -->
<header>
<nav class="navbar navbar-default">
	<h3>スライド作成画面</h3>
</nav>

</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div class="play_disp">
<!--class="container" 中央ぞろえ-->

	<div class="room_name"></div>

<div class="container">
  <div class="row">
	<div class="col-xs-4 col-sm-3 select_div" >
		<div>
			<div class="icon_info">
			  <strong><?=$_SESSION["user_name"]?>さん</strong> 
		</div>

		<img src="img/icon_sample.png" class="img-responsive img-rounded slide" alt="アイコンサンプル画像" >
		</div>

	<form id="upfile_form" method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="upfile">
			<h3><span class="label label-warning btn_effect "><span class="glyphicon glyphicon-level-up"></span>　①スライド登録</span></h3>
			<input type="file" id="upfile"  class="btn btn-warning"  name="upfile[]" webkitdirectory style="display:none;" />
		</label>

	</form>
	
	
		<button  id="slide_update" type="button" class="btn btn-primary" onclick="slide_update();" style="margin-bottom:10px"><span class="glyphicon glyphicon-wrench"></span>　②スライド変更</button>

<div id="update_type" style="display:none;">
	<form id="update_form_one" method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="slide_update_one" >
			<h3><span class="label label-primary btn_effect "><span class="glyphicon glyphicon-open-file"></span>　一枚</span></h3>
<!--			<button type="button" class="btn btn-primary btn-sm" style ="margin:5px 10px;"><span class="glyphicon glyphicon-open-file"></span>　一枚</button>-->
			<input type="file" id="slide_update_one" name="slide_update_one" style="display:none"/>
		</label>
<!--
	</form>
	
	<form id="update_form_all" method="post" action="slide_insert.php" enctype="multipart/form-data">
-->
		<label for="slide_update_all" >
<!--			<button type="button" class="btn btn-primary btn-sm" style ="margin:5px 10px;"><span class="glyphicon glyphicon-level-up"></span>　一括</button>			-->
			<h3><span class="label label-primary btn_effect "><span class="glyphicon glyphicon-level-up"></span>　一括</span></h3>
			<input type="file" id="slide_update_all"  name="slide_update_all[]" webkitdirectory  style="display:none;"/>
		</label>
	</form>
</div>


	 <button  id="rec" type="button" class="btn btn-danger" onclick="startRecording(this);" style="margin-bottom:10px"><span class="glyphicon glyphicon-record"></span>　③音声録音</button>
	 
	 <button  id="rec_stop" type="button" class="btn btn-danger" onclick="stopRecording(this);" style="display:none;"style="margin-bottom:20px"><span class="glyphicon glyphicon-pause"></span>　④録音停止</button>

  <h5>- Recordings status -</h5>
  <div id="log" style = "margin-bottom:10px;"></div>   	
  <div id="recordingslist"></div>
  
  <button id="all_play" type="button" class="btn btn-success all_play" onclick="all_play_btn();"><span class="glyphicon glyphicon-play"></span>　⑥自動再生</button>
  <button type="button" class="btn btn-success all_play_stop" onclick="all_play_btn();" style="display:none;"><span class="glyphicon glyphicon-pause"></span>　⑦一時停止</button>
   	  	   	
	</div>
	<div class="col-xs-1 col-sm-1" >
	</div>	
   
		
	<div class="col-xs-7 col-sm-8" >
		<div class="slide_info">
			<div id="slide_name">スライド名：</div>
			<div class="slick-counter">現在のスライド：<span class="current"></span> 枚目/ <span class="total"></span>枚中</div>
		</div>
		<div class="slide_area">
			<div class="sample_slide" >
				<div class="slider0">
				<div class="sample"><img src="img/slide_sample.png" class="img-responsive img-rounded slide sample" alt="サンプル画像" ></div>
				<div class="sample"><img src="img/slide_sample1.png" class="img-responsive img-rounded slide sample" alt="サンプル画像1" ></div>
				</div>
			</div>
			<div class="db_slide" ><?=$view_slide?></div>
		</div>
		<div class="slidebar_area">

			<input id="rangeslider" type="range" min="0" max="100" value="0" data-rangeslider>
			<output></output>
			<div id="time_area"><div id='now_time'>0:00</div>/<div id='all_time'>0:00</div></div>


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
	
	//1.スライドDBデータ取得＆表示処理--------------
	//DBのスライド有無チェック
		slide_id ='<?=$view_slide_id?>';
		console.log("DBのスライド有無チェック",slide_id);

	if(slide_id != 'なし'){
		//スライドデバッグ用
		slide_name ='<?=$view_slide_name?>';
		let view_slide_data ='<?=$view_slide_data_copy?>';
		let view_slide_num = '<?=$view_slide_num?>';
		slide_group = '<?=$view_slide_group?>';

		console.log('スライド名',slide_name);
		console.log('スライドデータ',view_slide_data);
		console.log('スライドid',slide_id);
		console.log('スライドグループid',slide_group);
		console.log('スライド総数',view_slide_num);

	   $('.sample_slide').remove();
	   $('#slide_name').append(slide_name);

		slide_num = view_slide_num;

	   }else{
		   //データ未登録時の処理★★要検討
			$('.db_slide').remove();
		   //サンプルスライド表示時はdbから取得したスライド表示タグを削除する。でないと、slider0が2つ存在することになり、スライドのカウントがおかしくなる。
	   }

	//2.スライド起動関数---------------------- 
		slickjs();

	//3.音声DBデータ取得＆表示処理----------------------

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
			   console.log("voice_time",voice_time);
		   }

		//音声時間リスト作成
		voice_time_split_mk(voice_time);
		//音声総時間の表示＆スライダー更新
		voice_time_all_disp(voice_time_split);
		//スライダー更新
		$('input[type="range"]').rangeslider('update', true);
	
	
		//audioタグ表示切替処理処理
		  voice_display(slide_num,slide_now_num);

});


//スライド起動関数 （slick.jsを使用）
function slickjs(){
	
	  //現在のスライド枚数表示処理
	  $('.slider'+slide_ul_num).on('init', function(event, slick) {
			$('.current').text(slick.currentSlide + 1);
			$('.total').text(slick.slideCount);
		  
		  	slide_num = slick.slideCount;//スライド総数
			slide_now_num =slick.currentSlide + 1;//現在のスライド番号
		  
		    console.log("slide_num：",slide_num);
			console.log("slide_now_num：",slide_now_num);
		  
			//audioタグ表示切替処理処理
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
//		    // マウスドラッグでスライドの切り替えをするか [初期値:true]
//		  	draggable: false,
	  })
		  .on('beforeChange', function(event, slick, currentSlide, nextSlide) {
			$('.current').text(nextSlide + 1);
			slide_now_num = nextSlide + 1;
			console.log('現在のスライド番号：',slide_now_num);


		//audioタグ表示切替処理処理
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
	

//①ファイル初期UL押下時-------------

$('#upfile').change(function(){

	//1)スライドUL取得処理
	slide_ul_get(this.files);

	//2)DB登録処理(ajax)
	slide_ul_db();
	
});


//1)スライドUL-取得処理
function slide_ul_get(this_files){

	//前スライド削除
	$('.slider'+slide_ul_num).remove();
	
	slide_ul_num++;
	console.log('スライドULチェック',slide_ul_num);//全く同じファイルを連続ULするとカウントされない。
	
	//スライドデータ(DB登録用)
	console.log('this_files',this_files);
	slide_data_ul = this_files;
	
	//スライドデータ(html表示用)
	let slide_data ='';
	
	//ULするスライドの枚数
	let new_slide_num = this_files.length -1;
	
	//スライド全体
	let slider_add = '';
	slider_add += '<div class="slider'+slide_ul_num+'">';
	
	//スライダー(全体)更新処理
	voice_time_split = [];
	voice_time = '';
	for(let i =0;i <= new_slide_num;i++){
		voice_time += default_audio_time+'/';
	}
	
	//音声リスト作成
	voice_time_split_mk(voice_time);
	//音声総時間の表示
	voice_time_all_disp(voice_time_split);
	//スライダー更新
	$('input[type="range"]').rangeslider('update', true);
	
	//ULされたスライドのhtmlを作成
	for (let i = 0; i < this_files.length; i++) {

		// 選択されたファイル情報を取得
		let file = this_files[i];

		//アップロードされたファイル名からスライド名を取得
		slide_name = file['webkitRelativePath'].split('/')[0];

		// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
		let reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = function() {
			let slide_disp_num = i+1;

			//何枚でもアップロードできるように変更
			//スライド追加
			slide_data += reader.result+"/";	
			let slide_add = '';		
			slide_add += '<div id="slide'+ slide_disp_num+'">';
			slide_add += '<img src="'+reader.result+'" class="img-responsive img-rounded slide slide_img_'+slide_disp_num+'" alt="ULスライド'+slide_disp_num+'枚目" >';
			slide_add += '</div>'; 
			slider_add += slide_add;

			//スライドがすべてULされたときの処理
			if(i == new_slide_num){
				//新しいsliderタグの挿入
				slider_add += '</div>';//slider終了タグ
				$(".slide_area").prepend(slider_add);
				//前回の音声削除
				$("#recordingslist>div").remove();

				//新しいスライド起動 
				slickjs();

			}
		}
	}

}
	
//2)スライドUL-DB登録処理(ajax)
function slide_ul_db(){
	
	let fd = new FormData($('#upfile_form').get(0));
	//$postで確認
	fd.append('slide_name', slide_name);

	$.ajax({
		type: 'POST',
		url: 'slide_insert.php',
		data: fd,
		processData: false,
		contentType: false
	}).done(function(data) {
       console.log(data);
	slide_group = data.split('/')[1];
	console.log('スライド登録処理終了');
	console.log('slide_group:',slide_group);		
	});
}




//②スライド変更-------------------------------------
function slide_update(){
//	$('#rec ,#rec_stop').prop("disabled", false);
//	$('.btn_effect').css("pointer-events", "auto");
	
	$('#update_type').toggle();
}

//②スライド一括更新を選択

$('#slide_update_one').change(function(){

	//1)スライドUL取得処理
	slide_ud_one_get(this.files);

	//2)DB登録処理(ajax)
	slide_ud_one_db();
	
});


//1)スライドUL-取得処理
function slide_ud_one_get(this_files){

//	//前スライド(一枚)削除
//	$('.slide'+slide_now_num).remove();
	
	console.log('slide_now_num',slide_now_num);
	
	//スライドデータ(DB登録用)
	console.log('this_files[0]',this_files[0]);
	slide_data_ul = this_files[0];
	
	//スライドデータ(html表示用)
	let slide_data_img ='';
	
	//	ULされたスライドのhtmlを作成
	let file = this_files[0];

	//アップロードされたファイル名からスライド名を取得
	slide_name = file['name'];
	console.log('slide_name',slide_name);

	// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
	let reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = function() {
	//新しいスライドimgの挿入
	$('.slide_img_'+slide_now_num).attr("src",reader.result);
	}
}
	
//2)スライドUL-DB登録処理(ajax)
function slide_ud_one_db(){
	
	let fd = new FormData($('#slide_update').get(0));
	//$postで確認
	fd.append('slide_name', slide_name);
	fd.append('slide_group', slide_group);
	fd.append('slide_now_num', slide_now_num);

	$.ajax({
		type: 'POST',
		url: 'slide_update_one.php',
		data: fd,
		processData: false,
		contentType: false
	}).done(function(data) {
       console.log(data);
	slide_group = data.split('/')[1];
	console.log('スライド変更処理終了');
	console.log('slide_group:',slide_group);		
	});
}	

	
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
	
//録音開始
function startRecording(button) {

	//音声録音フラグ
	recording_flag = true;

	//スライダーバー(全体)移動 関数(各スライドの頭出し用)
	rangeslider_change_eachtop();

	//録音関数
	recorder && recorder.record();

	//録音ボタン表示切替処理＆他ボタン無効処理
	$('#rec').toggle();  
	$('#rec_stop').toggle();
	$('audio, .slick-arrow ,.db_slide,.rangeslider ,#all_play ').css("pointer-events", "none");

	// タイマーをセット(1000ms間隔)
	PassSec = 0;
	PassageID = setInterval('show_voicelog()',1000);  

}

function show_voicelog() { 
	PassSec++
	__log('Recording...   ('+PassSec+'s)');
}
	
	//録音停止
function stopRecording(button) {

	//音声録音フラグ
	recording_flag = false;

    recorder && recorder.stop();

	//録音ボタン表示切替処理＆他ボタン有効処理
	$('#rec').toggle();  
	$('#rec_stop').toggle();
	$('audio, .slick-arrow ,.db_slide,.rangeslider, #all_play ').css("pointer-events", "auto");

    __log('Stopped recording.');
	  
	 // タイマーのクリア
	 clearInterval( PassageID );  
    
    // create WAV download link using audio data blob
    createDownloadLink();
    recorder.clear();  
  }

  function createDownloadLink() {
    recorder && recorder.exportWAV(function(blob) {
	
	console.log('音声データ：',blob);
		
	 //音声データを仮想urlに変換(セッション内のみ有効)
      var url = URL.createObjectURL(blob);
	//タグ作成
      var div = document.createElement('div');
      var au = document.createElement('audio');
      var hf = document.createElement('a');

	//旧divタグ(旧音声)削除 
	//※表示のみ。データは残る。
	 $('#slide_now_num_'+slide_now_num).remove();

	//divタグ編集
	div.id = 'slide_now_num_'+slide_now_num;
	div.style ="display: block;";
	div.innerHTML = 'スライド'+slide_now_num+'枚目の音声';

		
	//audioタグ編集
      au.controls = true;
      au.src = url;
	  au.id = 'slide_now_num_'+slide_now_num+'_audio';

	//aタグ(音声DL)編集
      hf.href = url;
		//ダウンロード属性
      hf.download = new Date().toISOString() + '.wav';
      hf.innerHTML = hf.download;
		console.log('url：',url);
		console.log(hf.download);
	//html挿入
      div.appendChild(au);
      div.appendChild(hf);
      recordingslist.appendChild(div);
		
	var audio = new Audio(); // audioの作成
	audio.src = url; // 音声ファイルの指定
	audio.load(); // audioの読み込み 
	audio.addEventListener('loadedmetadata',function(e) {
		// 音声時間の取得
		let voice_time = audio.duration*1000;
		console.log('時間(ms)：',voice_time); 
		voice_ul(blob,voice_time);//音声アップロード

		//音声時間リストに代入
		voice_time_split[slide_now_num] = voice_time;
		//音声総時間の表示＆スライダー更新
		voice_time_all_disp(voice_time_split);
		$('input[type="range"]').rangeslider('update', true);
		
		});
	
    });
  }

	//サポートチェック、マイクチェック？
	  window.onload = function init() {
		try {
		  // webkit shim
		  window.AudioContext = window.AudioContext || window.webkitAudioContext;
		  navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
		  window.URL = window.URL || window.webkitURL;

		  audio_context = new AudioContext;
		  __log('Audio context set up.');
		  __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
		} catch (e) {
		  alert('No web audio support in this browser!');
		}

		navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
		  __log('No live audio input: ' + e);
		});
	  };
	
function voice_ul(soundBlob,voice_time){

	//送信データ作成
	var fd = new FormData();
		//$fileで確認
		fd.append('sound_blob', soundBlob);
		//$postで確認
		fd.append('file_name', 'slide'+slide_now_num+'_voice.wav');
		fd.append('slide_name', slide_name);
		fd.append('slide_group', slide_group);
		fd.append('slide_now_num', slide_now_num);
		fd.append('voice_time', voice_time);
	
	$.ajax({
		type: 'POST',
		url: 'voice_insert.php',
		data: fd,
		processData: false,
		contentType: false
	}).done(function(data) {
       console.log(data);
	console.log('音声登録処理終了');
	});


}

//audioタグ表示切替処理処理
//(slide_now_numはセレクタに使用しないほうが良い。なぜかは不明。挙動がおかしくなる)
function voice_display(slide_num,slide_now_num){

	 for (let x = 1; x <= slide_num; x++){
		let decision = $('#slide_now_num_'+x).css('display');
		if(slide_now_num === x){

				if(decision === 'none'){
					//要素が非表示だったら～
					$('#slide_now_num_'+x).show();
				}
		}else{
				if(decision === 'block'){
					//要素が表示だったら～
					$('#slide_now_num_'+x).hide();
				}
		}
	}
}


	
//⑥自動再生/一時停止ボタン押下後
	
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
	
	//スライダー(全体)=最終秒のとき、最初から再生する
	if(onSlideEnd_output().onSlideEnd_slide_num+1 == slide_num && onSlideEnd_time == voice_time_split[slide_num]){
		onSlideEnd_time = 0;
		$('.slider'+slide_ul_num).slick('slickGoTo', 0);
	}
	
	all_play_true(onSlideEnd_time);
}



	
function all_play_true(onSlideEnd_time){
	TARGET =  document.getElementById('slide_now_num_'+slide_now_num+'_audio');

	console.log('音声存在チェック',TARGET != null,TARGET);

	if(TARGET){
		TARGET.currentTime = onSlideEnd_time/1000;
		
		TARGET.play();
		//audioタグシークバー同期処理
		
		let NOW = TARGET.currentTime;
		let TOTAL = TARGET.duration*1000+100;
		TOTAL = TOTAL - NOW;
		console.log('NOW：',NOW);
		console.log('TOTAL：',TOTAL);
		
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
		console.log('スライダーバー位置',NOW_all_disp);
	}else{
		NOW_all_disp = NOW_all_set() + PassSec;
		//スライダーバー(全体)移動
		rangeslider_change(NOW_all_disp);
		console.log('スライダーバー位置_last',NOW_all_disp);
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
			//スライド手動移動中or録音中のときは頭出し
			let NOW_all_disp = NOW_all_set();
			rangeslider_change(NOW_all_disp);
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
//	if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
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
	

	
//スライダーバー(全体)設置関数
//rangeslider.js-2.3.0 を使用
$('input[type="range"]').rangeslider({
	polyfill: false,
    // Callback function スライダー起動時
    onInit: function() {
		$('output').html(0);
	},
	
    // Callback function　スライダー移動時
    onSlide: function(position, value) {

		let output = $('#rangeslider').val();
		console.log('onSlide：',output);
		$('output').html(output);
		
	},
	
	// Callback function() スライダー停止時
	onSlideEnd: function(position, value) {

			//スライド単体での秒数(他関数で使用)
			let onSlideEnd_time = onSlideEnd_output().onSlideEnd_time;
			let onSlideEnd_slide_num  = onSlideEnd_output().onSlideEnd_slide_num;

		if(!all_play_flag){
			//スライド手動移動フラグ:false
			slick_manual_flag = false;
			$('.slider'+slide_ul_num).slick('slickGoTo', onSlideEnd_slide_num);
			console.log('スライダーで移動',onSlideEnd_slide_num +1);
			
			}
		}
});

	
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
		console.log('比較',output,time,time + voice_time_split[i+1]);
		
		//最後のスライドの場合、最終秒は最後のスライドに含める
		if((output >= time  && output < (time + voice_time_split[i+1]))||(i == (slide_num - 1) && output == (time + voice_time_split[i+1]))){
			onSlideEnd_slide_num = i;
			onSlideEnd_time = output - time;
		}
	}
	
	onSlideEnd_time = Number(onSlideEnd_time);
	
	let onSlideEnd_return ={"onSlideEnd_time":onSlideEnd_time,"onSlideEnd_slide_num":onSlideEnd_slide_num};
	console.log('onSlideEnd_return',onSlideEnd_return);
	
	return onSlideEnd_return;
}
	

	
	
	
</script>
  
</body>
</html>
