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
			$view_slide_data_copy .= $view_slide_data.'★';
			$view_slide_name      = $r["slide_name"];
			$view_slide_id        = $r["slide_id"];
			$view_slide_now_num   = $r["slide_now_num"];	
			$view_slide .= '<div class="db_slide">';
			$view_slide .= 	'<img src="'.$file_dir_path.$view_slide_data.'" class="img-responsive img-rounded slide" alt="dbスライド" ></div>';
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
		<label for="upfile" >
			<h4><span class="label label-warning btn_effect">①スライド登録(フォルダ)</span></h4>
			<input type="file" id="upfile"  name="upfile[]" webkitdirectory style="display:none;" />
		</label>

	</form>
	
	<form id="update_form" method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="update" >
			<h4><span class="label label-warning btn_effect">②スライドを変更</span></h4>
			<button type="button" id="update" onclick="slide_update()" style="display:none;"></button>
		</label>
		<div id="update_type" style="display:none;">

		<label for="slde_update_one" >
			<h5><span class="label label-warning btn_effect" style ="margin:10px 10px;">1)今のスライドだけ変更【未実装】</span></h5>
			<input type="file" id="slde_update_one"  name="slde_update_one" style="display:none"/>
		</label>
		<label for="slde_update_all" >
			<h5><span class="label label-warning btn_effect" style ="margin:10px 10px;">2)スライド一括変更(フォルダごと)【未実装】</span></h5>
			<input type="file" id="slde_update_all"  name="slde_update_all[]" webkitdirectory  style="display:none;"/>
		</label>
	
			</div>
				

	</form>


		  
	<label for="rec" >
		<h4><span class="label label-info btn_effect">③音声録音</span></h4>		  
  		<button id="rec" onclick="startRecording(this);" style="display:none;">record</button>
  	</label>
 
 	<label for="rec_stop" >
 		<h4><span class="label label-info btn_effect">④録音停止(ajax)</span></h4>		  
  		<button id="rec_stop" onclick="stopRecording(this);"  style="display:none;">stop</button>
   	</label>
 
   	  	  
  <h5>- Recordings status -</h5>
  <div id="log" style = "margin-bottom:10px;"></div>   	
  <div id="recordingslist"></div>
  
  	<label for="all_play_btn" >
 		<h4><span class="label label-success btn_effect all_play" style="display:block;">⑥自動再生</span></h4>
		<h4><span class="label label-success btn_effect all_play_stop" style="display:none;">⑥一時停止</span></h4>	  
  		<button id="all_play_btn" onclick="all_play_btn();"  style="display:none;">再生/一時停止</button>
   	</label>
   	
<!--
 	<label for="all_play" >
 		<h4><span class="label label-success btn_effect">自動再生</span></h4>		  
  		<button id="all_play" onclick="all_play();"  style="display:none;">start</button>
   	</label>
-->

 	<label for="all_play_stop" >
 		<h4><span class="label label-success btn_effect">⑦停止</span></h4>		  
  		<button id="all_play_stop" onclick="all_play_stop();"  style="display:none;">stop</button>
   	</label>

<!--
 	<label for="all_play_stop_tmp" >
 		<h4><span class="label label-success btn_effect">一時停止</span></h4>		  
  		<button id="all_play_stop_tmp" onclick="all_play_stop_tmp();"  style="display:none;">stop</button>
   	</label>
-->
 

   	  	   	
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

	
//①スライドUL機能-----------------------------------	
$("[slider-volume]")
.each(function() {
    var input = $(this);
    $("<span>")
    .addClass("output")
    .insertAfter($(this));
})
.bind("slider:ready slider:changed", function( event, data) {
    $(this)
    .nextAll(".output:first")
    .html(data.value.toFixed(3));  // 数値表示の小数点以下数
}); 
	
//グローバル変数
let slide_now_num ='';//現在のスライド番号
let slide_num ='';//スライド総数
let slide_id ='';//スライドid
let slide_group ='';//スライドグループid
let slide_name = '';

	//DBのスライド有無チェック
	//メモ：javascriptでphpを呼び出す際は、''でくくる
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
	   		$('.db_slide').remove();
		   //サンプルスライド表示時はdbから取得したスライド表示タグを削除する。でないと、slider0が2つ存在することになり、スライドのカウントがおかしくなる。
	   }

   
//スライド設置関数 （slick.jsを使用）
	$(function () {
	
	  //現在のスライド枚数表示処理
	  $('.slider0').on('init', function(event, slick) {
			$('.current').text(slick.currentSlide + 1);
			$('.total').text(slick.slideCount);
		  
		  	slide_num = slick.slideCount;//スライド総数
			slide_now_num =slick.currentSlide + 1;//現在のスライド番号
		  
		    console.log("slide_num：",slide_num);
			console.log("slide_now_num：",slide_now_num);
		  
			//audioタグ表示切替処理処理
			  voice_display(slide_num,slide_now_num);
	  })
		  .slick({
			// option here...
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
	});


	
	
	
//グローバル関数	
	//アップロード回数
	let slide_ul_num = 0;
	//スライドデータ(DB登録用)
	let	slide_data_ul;
	
//①ファイルUL押下(DB登録も含む)
$('#upfile').change(function(){
	$('.slider'+slide_ul_num).remove();//サンプル削除
//	$('#rec , #rec_stop').prop("disabled", true);
	$('.btn_effect').css("pointer-events", "none");

	//前回ULしたスライドorサンプルスライド削除   
	if(slide_ul_num > 0){
		$('.slider'+slide_ul_num).remove();
	}
	
	slide_ul_num++;
	console.log('スライドULチェック',slide_ul_num);//全く同じファイルを連続ULするとカウントされない。
	
	//スライドデータ(DB登録用)
	console.log('this.files',this.files);
	slide_data_ul = this.files;
	
	//スライドデータ(html表示用)
	let slide_data ='';
	
	//ULするスライドの枚数
	let new_slide_num = this.files.length -1;
	
	//スライド全体
	let slider_add = '';
	slider_add += '<div class="slider'+slide_ul_num+'">';	

		
	//ULされたスライドのhtmlを作成
	for (let i = 0; i < this.files.length; i++) {

		// 選択されたファイル情報を取得
		let file = this.files[i];
		
		//アップロードされたファイル名からスライド名を取得
		slide_name = file['webkitRelativePath'].split('/')[0];

		// readerのresultプロパティに、データURLとしてエンコードされたファイルデータを格納
		let reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = function() {

			//何枚でもアップロードできるように変更
			//スライド追加
			slide_data += reader.result+"/";	
			let slide_add = '';		
			slide_add += '<div id="slide'+ i+'">';
			slide_add += '<img src="'+reader.result+'" class="img-responsive img-rounded slide" alt="ULスライド'+i+'枚目" >';
			slide_add += '</div>'; 
			slider_add += slide_add;

			//スライドがすべてULされたときの処理
			if(i == new_slide_num){
				//新しいsliderタグの挿入
				slider_add += '</div>';//slider終了タグ
				$(".slide_area").prepend(slider_add);
				//前回の音声削除
				$("#recordingslist>div").remove();
				//新しくslick関数を動作させる
				$('.slider'+slide_ul_num).on('init', function(event, slick) {
					
				$('.current').text(slick.currentSlide + 1);
				$('.total').text(slick.slideCount);

				console.log('slide_num',slick.slideCount);

				//audioタグ表示切替処理処
				  voice_display(slide_num,slide_now_num);

			  })
				  .slick({
					// option here...
			  })
				  .on('beforeChange', function(event, slick, currentSlide, nextSlide) {
							$('.current').text(nextSlide + 1);
							slide_now_num = nextSlide + 1;
							console.log('現在のスライド番号：',slide_now_num);
					
		//audioタグ表示切替処理処理
		  voice_display(slide_num,slide_now_num);
		  });
		//スライダーバー(全体)移動 関数(各スライドの頭出し用)
		rangeslider_change_eachtop();

				
		}
	  }
	}
	
	//DB登録処理(ajax)
	slide_ul();
	
});

	
//DB登録処理(ajax)
function slide_ul(){
	$('#rec ,#rec_stop').prop("disabled", false);
	$('.btn_effect').css("pointer-events", "auto");
	
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

//②スライド変更
function slide_update(){
//	$('#rec ,#rec_stop').prop("disabled", false);
//	$('.btn_effect').css("pointer-events", "auto");
	
	$('#update_type').toggle();
}

function slde_update_all(){

}
	

	
//④音声録音機能-----------------------------------

//グローバル変数
	
	//音声データファイル名
	let voice_data ='<?=$view_voice_data_copy?>';
	let voice_data_split = voice_data.split('/');
	console.log('取得した音声データ：');
	console.log(voice_data_split);
	
	//DBの音声の有無チェック＆DB取得音声を表示
	let db_voice_chk = '<?=$view_voice_id?>' ;
	console.log("DBの音声有無チェック(voice_id)",db_voice_chk);

	if(db_voice_chk != 'なし'){
		//phpで作成したタグを挿入
		$('#recordingslist').append('<?=$view_voice?>');
		console.log("slide_num：",slide_num);
			
	   }
	
	
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

	//録音開始
  function startRecording(button) {
	PassSec = 0;
    recorder && recorder.record();
//    button.disabled = true;
//    button.nextElementSibling.disabled = false;
 	PassageID = setInterval('show_voicelog()',1000);   // タイマーをセット(1000ms間隔)

  }

	function show_voicelog() { 
		PassSec++
    __log('Recording...   ('+PassSec+'s)');
	}
	
	//録音停止
  function stopRecording(button) {
    recorder && recorder.stop();
//    button.disabled = true;
//    button.previousElementSibling.disabled = false;
    __log('Stopped recording.');
	 clearInterval( PassageID );   // タイマーのクリア
    
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
		let voice_time = audio.duration;
		console.log('時間：',voice_time); // 総時間の取得
		voice_ul(blob,voice_time);//音声アップロード
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
		fd.append('voice_time', voice_time*1000);
	
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


//スライド時間取得処理
//for文×durationで一枚ずつ取得⇒失敗
//録音時に音声時間を取得して、DBに登録して、取得する方法に変更

	//音声データ時間
	let voice_time_all = 0;//総和
	let voice_time = 0;
	let default_audio_time = 3000;//デフォルト音声時間(3s)

	//DBの音声の有無チェック＆DB取得音声を表示
	if(db_voice_chk != 'なし'){
		//phpで取得した音声時間を代入
		voice_time ='<?=$view_voice_time_copy?>';
	   }else{
		   for(let i =1;i <= slide_num;i++)
		   voice_time += default_audio_time+'/';
	   }

	//音声時間リスト(配列：1~)
	let voice_time_split = voice_time.split('/');	

	//先頭にダミーを追加(スライド番号に合わせる)
	voice_time_split.unshift(0);
	voice_time_split.pop();//末尾を削除
	
	//音声時間リストを時間に変換
	for(var i =1; i < voice_time_split.length; i++){
		voice_time_split[i] = Number(voice_time_split[i]);
		voice_time_all += voice_time_split[i];
	};

	console.log('取得した音声時間：',voice_time_split);
	console.log('voice_time_all：',voice_time_all/1000);	
	
	//音声総時間取得＆挿入
	let voice_time_all_view = toHms(voice_time_all/1000);
	
	//音声総時間挿入(html&max)
	$('#all_time').html(voice_time_all_view);
	$('#rangeslider').attr('max',voice_time_all);
	
	
//	⑥自動再生/一時停止ボタン押下後
	
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
	all_play_true();		
}

function all_play_true(){
	var TOTAL = 0;

	TARGET =  document.getElementById('slide_now_num_'+slide_now_num+'_audio');

	console.log('音声存在チェック',document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null,TARGET);

	if(TARGET != null){

		TARGET.play();
		//audioタグシークバー同期処理
		//シークバーが終了位置にあると、自動的に頭出ししてくれる。audio.jsの処理？

		let NOW =TARGET.currentTime*1000;
		TOTAL = TARGET.duration*1000;
		TOTAL = TOTAL - NOW;
		console.log('NOW：',NOW);
		console.log('TOTAL：',TOTAL);
		
		//音声再生時シークバー(全体)同期
		if(!TARGET.paused ){
			TARGET.addEventListener("timeupdate", function() {

				let NOW_all_disp = 0;
				NOW_all_disp = NOW_all_set() + TARGET.currentTime*1000;

				//スライダーバー(全体)移動
				rangeslider_change(NOW_all_disp);

			}, true);
		}

	}else{
		TOTAL = default_audio_time;
		console.log('TOTAL：',TOTAL);
		//デフォルト3s
		
		//音声再生時シークバー(全体)同期
		PassSec = 0;
		PassageID = setInterval('show_NOW_all_disp(0)',10);
		// タイマーをセット(100ms間隔)

	}
	
	var next_slide = function(){
		clearInterval( PassageID );   // タイマーのクリア
		
		if(slide_now_num !== slide_num && all_play_flag){
			$('.slider'+slide_ul_num).slick('slickNext');
			all_play_true();
		}else{
			//audioタグ、スライド移動有効
				all_play_btn_stop();
				all_play_flag = false;



//			最初のページに戻す処理　無効化
//			$('.slider'+slide_ul_num).slick('slickNext');
		}
	}

	setTimeout(next_slide, TOTAL);

}

//スライダーバー(全体)移動(音声データがなかった時用)
function show_NOW_all_disp(NOW_all_disp) {

	PassSec += 10;
	if(PassSec <= default_audio_time){
		NOW_all_disp = NOW_all_set() + PassSec;
		//スライダーバー(全体)移動
		rangeslider_change(NOW_all_disp);
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

//スライダーバー(全体)移動 関数(各スライドの頭出し用)
function rangeslider_change_eachtop(){
	if(all_play_flag == false){
		let NOW_all_disp = NOW_all_set();
		rangeslider_change(NOW_all_disp);
	}
}
	
//2)一時停止処理
function all_play_stop_tmp(){

	all_play_flag = false;
	if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
				TARGET.pause();
	}

	//audioタグ、スライド移動有効
		all_play_btn_stop();
		clearInterval( PassageID );   // タイマーのクリア

}

//⑦停止ボタン押下後 不要？
function all_play_stop(){
	all_play_flag = false;
	if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
		TARGET.currentTime = 0;
		TARGET.pause();

	}
	
	//audioタグ、スライド移動有効
	all_play_btn_stop();
}
	

function all_play_btn_start(){
		//audioタグ、スライド移動無効、再生ボタン切り替え
		$('audio, .slick-arrow ,.db_slide').css("pointer-events", "none");
		$('.all_play').hide();
		$('.all_play_stop').show();
}

function all_play_btn_stop(){
		//audioタグ、スライド移動有効、再生ボタン切り替え
		$('audio, .slick-arrow ,.db_slide').css("pointer-events", "auto");
		$('.all_play').show();
		$('.all_play_stop').hide();
}

//音声総和(表示用) 作成関数	
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
	update: true,
	
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
		
		
	}
});

	
	
	
	
	
	
	
</script>
  
</body>
</html>
