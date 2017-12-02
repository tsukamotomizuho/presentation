<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数
//$_SESSION["kanri_flg"] == 1 セッション関数


//2. DB接続
$pdo = db_con();

//３．SQLを作成(スライド取得)
$stmt = $pdo->prepare("SELECT * FROM slide_table ORDER BY slide_id DESC  LIMIT 1");
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る


$view_slide = '<div class="slider0">';//slider開始タグ
$view_slide_id   = "なし";
$view_slide_data ='';//スライド画像ファイル名の羅列
$view_slide_name ='';//スライド名
$view_slide_num  ='';//スライドの総数
$file_dir_path   = "upload/";  //画像ファイル保管先

//4.取得スライド or エラー表示
if($status==false){
	queryError($stmt);
}else{//正常
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){


		$view_slide_data .= $r["slide_data"];
		$view_slide_data_copy = $view_slide_data;
		$view_slide_data_copy = str_replace("/", "★", $view_slide_data_copy );
		
		$view_slide_name .= $r["slide_name"];
		$view_slide_id   = $r["slide_id"];
		$view_slide_num  = $r["slide_num"];	
		//第1=ターゲット, 第2=元の文字􀀁
		$view_slide_data = explode("/" , $view_slide_data );

		for ($i=1; $i < count($view_slide_data); $i++) {		
			$view_slide .= '<div class="db_slide">';
			$view_slide .= 	'<img src="'.$file_dir_path.$view_slide_data[$i].'" class="img-responsive img-rounded slide sample" alt="dbスライド" ></div>';
		}
			$view_slide .= '</div>'; //slider終了タグ
	}
}

//5．SQLを作成(音声取得)
//スライド番号ごとに取り出す
	$view_voice = '';//html表示用
	$view_voice_id   = "なし";
	$view_voice_copy=''; //デバック用

for($i=1; $i <= $view_slide_num; $i++){
		
		//sqlのselect実行結果(件数)確認用
		$sql = 'SELECT COUNT(*) FROM voice_table WHERE slide_id ='.$view_slide_id.' AND slide_now_num ='.$i;
	
		$res = $pdo->prepare($sql);
		$status1 = $res->execute();
		
		//sqlのselect実行文
		$voice_table_sql = 'SELECT * FROM voice_table WHERE slide_id ='.$view_slide_id.' AND slide_now_num ='.$i.' ORDER BY voice_id DESC LIMIT 1';

		$stmt = $pdo->prepare($voice_table_sql);
		$status2 = $stmt->execute();
		//実行後、エラーだったらfalseが返る
	

	//音声タグ作成
		$view_slide_now_num ='';
		$view_voice_data    ='';
	
 if ($status1) {

  /* SELECT 文にマッチする行数をチェックする */
  if ($res->fetchColumn() > 0) {

	if($status2==false){
			queryError($stmt);
	}else{//正常

	  
	  while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

				$view_voice_id      = $r["voice_id"];
				$view_slide_now_num = $r["slide_now_num"];
				$view_voice_data    = $r["voice_data"];
				$view_voice_copy    .= $r["voice_data"].'/';
				

				$view_voice .= '<div id="slide_now_num_'.$view_slide_now_num.'" style="display: block;">';//開始タグ
				$view_voice .= 'スライド'.$view_slide_now_num.'枚目の音声';
				$view_voice .= '<audio id="slide_now_num_'.$view_slide_now_num.'_audio" controls="" src=""></audio>';
//				$view_voice .= '<a href="#" download="">音声ファイル名</a>';
				$view_voice .= '</div>';//終了タグ

				
			}
		}  
	  
    }
    /* 行がマッチしなかった場合、voice_dataに『/』を挿入 */
  else {
	  $view_voice_copy    .= $r["voice_data"].'/';
	  
//				$view_voice .= '<div id="slide_now_num_'.$i.'" style="display: none;">';//開始タグ
//				$view_voice .= 'スライド'.$i.'枚目の音声';
//				$view_voice .= '<audio controls="" src=""></audio>';
//				$view_voice .= '</div>';//終了タグ

    }
}
	
	


}
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
			<div class="alert alert-warning">
			  <strong>xxxさん</strong> 
		</div>

		<img src="img/icon_sample.png" class="img-responsive img-rounded slide" alt="アイコンサンプル画像" >
		</div>
		

	
	<form id="upfile_form" method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="upfile" >
			<h4><span class="label label-warning btn_effect">①スライドUL(フォルダごと)</span></h4>
			<input type="file" id="upfile"  name="upfile[]" webkitdirectory style="display:none;" />
		</label>
		<label for="save" >
			<h4><span class="label label-warning btn_effect_slde">②スライドをDB登録(ajax)</span></h4>
			<button type="button" id="save" onclick="slide_ul()" style="display:none;"></button>
		</label>
	</form>
	
	<form id="update_form" method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="update" >
			<h4><span class="label label-warning btn_effect">③スライドを変更</span></h4>
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
		<h4><span class="label label-info btn_effect">④音声録音</span></h4>		  
  		<button id="rec" onclick="startRecording(this);" style="display:none;">record</button>
  	</label>
 
 	<label for="rec_stop" >
 		<h4><span class="label label-info btn_effect">⑤録音停止(ajax)</span></h4>		  
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
		<div class="alert alert-warning">
			<div id="slide_name">スライド名：</div>
			<div class="slick-counter">現在のスライド：<span class="current"></span> 枚目/ <span class="total"></span>枚中</div>
		</div>
		<div class="slide_area">
			<div class="sample_slide" >
				<div class="slider0">
				<div class="sample"><img src="img/slide_sample.png" class="img-responsive img-rounded slide sample" alt="サンプル画像" ></div>
				<div class="sample"><img src="img/slide_sample1.png" class="img-responsive img-rounded slide sample" alt="サンプル画像1" ></div>
				<div id="slide2"><img src="img/slide_sample2.png" class="img-responsive img-rounded slide" alt="サンプル画像2" ></div>
				<div id="slide3"><img  src="img/slide_sample3.png" class="img-responsive img-rounded slide" alt="サンプル画像3" ></div>
				</div>
			</div>
			<div><?=$view_slide?></div>
		</div>
	</div>

  </div>
 </div>
</div>

<!-- Main[End] -->




<script>
//①スライドUL機能-----------------------------------

//グローバル変数
let slide_now_num ='';//現在のスライド番号
let slide_num ='';//スライド総数
	

	//DBのスライド有無チェック
	//メモ：javascriptでphpを呼び出す際は、''でくくる
	let view_slide_id ='<?=$view_slide_id?>';
	console.log("DBのスライド有無チェック",view_slide_id);

	if(view_slide_id != 'なし'){
		//スライドデバッグ用
		let view_slide_name ='<?=$view_slide_name?>';
		let view_slide_data ='<?=$view_slide_data_copy?>';
		let view_slide_num = '<?=$view_slide_num?>';
		console.log('スライド名',view_slide_name);
		console.log('スライドデータ',view_slide_data);
		console.log('スライドid',view_slide_id);
		console.log('スライド総数',view_slide_num);

	   $('.sample_slide').remove();
	   $('#slide_name').append('<?=$view_slide_name?>');
		
		slide_num = view_slide_num;
		
	   }

   
//スライド表示機能 （slick.jsを使用）
	$(function () {
	
	  //現在のスライド枚数表示処理
	  $('.slider0').on('init', function(event, slick) {
			$('.current').text(slick.currentSlide + 1);
			$('.total').text(slick.slideCount);
		  
		  	slide_num = slick.slideCount;//スライド総数
			slide_now_num =slick.currentSlide + 1;//現在のスライド番号
		  
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
		  
	  });	
		

	});


	
	
	
//グローバル関数	
	//アップロード回数
	let slide_ul_num = 0;
	//スライドデータ(DB登録用)
	let	slide_data_ul;

	
//①ファイルUL押下
$('#upfile').change(function(){
	$('.slider'+slide_ul_num).remove();//サンプル削除
	$('#rec , #rec_stop').prop("disabled", true);
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

				//audioタグ表示切替処理処★★ここから！！
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
			}
	  	}
	}
	
});

	
	
	
	
//②スライドをDB登録ボタン押下
	
function slide_ul(){
	$('#rec ,#rec_stop').prop("disabled", false);
	$('.btn_effect').css("pointer-events", "auto");
	
	let fd = new FormData($('#upfile_form').get(0));
	
	$.ajax({
		type: 'POST',
		url: 'slide_insert.php',
		data: fd,
		processData: false,
		contentType: false
	}).done(function(data) {
       console.log(data);
	console.log('スライド登録成功');
	});
}

//③スライド変更
function slide_update(){
//	$('#rec ,#rec_stop').prop("disabled", false);
//	$('.btn_effect').css("pointer-events", "auto");
	
	$('#update_type').toggle();
}

function slde_update_all(){

//	let fd = new FormData($('#upfile_form').get(0));
//	
//	$.ajax({
//		type: 'POST',
//		url: 'slide_insert.php',
//		data: fd,
//		processData: false,
//		contentType: false
//	}).done(function(data) {
//       console.log(data);
//	console.log('スライド登録成功');
//	});

}
	

	
//④音声録音機能-----------------------------------

//グローバル変数
	
	//音声データファイル名
	let view_voice_data ='<?=$view_voice_copy?>';
	console.log('取得した音声データ：');
	console.log(view_voice_data);
	let voice_data_split = view_voice_data.split('/');
	console.log(voice_data_split);
	
	
	//DBの音声の有無チェック＆DB取得音声を表示
	let db_voice_chk = '<?=$view_voice_id?>' ;
	console.log("DBの音声有無チェック(voice_id)",db_voice_chk);

	if(db_voice_chk != 'なし'){
		
		//1)phpで作成したタグを挿入
		$('#recordingslist').append('<?=$view_voice?>');
		console.log("slide_num：",slide_num);
		
		//2)タグに情報を追記
		for (let y = 0; y < slide_num ; y++){
				
			// divタグ用番号
			let div_slide_now_num = y+1;
			//音声データ格納先
			let voice_data_path = 'upload_sound/'+voice_data_split[y];
			
			console.log('スライド番号：',div_slide_now_num,'、voice_data_path：',voice_data_path);
			//var url = URL.createObjectURL(blob);
			$('#slide_now_num_'+div_slide_now_num+'>audio').attr('src', voice_data_path);

			
			}
			
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
	voice_ul(blob);
		
	 //音声データを仮想urlに変換(セッション内のみ有効)
      var url = URL.createObjectURL(blob);
	//タグ作成
      var div = document.createElement('div');
      var au = document.createElement('audio');
      var hf = document.createElement('a');

	//旧divタグ(旧音声)削除 
	//※表示のみ。データは残る。あとでphp側で処理要★
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
	
function voice_ul(soundBlob){

	//送信データ作成
	var fd = new FormData();
		//$fileで確認
		fd.append('sound_blob', soundBlob);
		//$postで確認
		fd.append('file_name', 'slide'+slide_now_num+'_voice.wav');
		fd.append('slide_name', '<?=$view_slide_name?>');
		fd.append('slide_id', '<?=$view_slide_id?>');
		fd.append('slide_now_num', slide_now_num);

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
//			console.log('decision',decision,x); 
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
	
	
//	⑥自動再生/一時停止ボタン押下後
	function all_play_btn(){
		
		if(all_play_flag){
			all_play_stop_tmp();
			$('.all_play').show();
			$('.all_play_stop').hide();
		}else{
			all_play();
			$('.all_play').hide();
			$('.all_play_stop').show();
		}
	}

	
//1)自動再生処理
let all_play_flag = false;
var TARGET;
	
	function all_play(){
		all_play_flag = true;
		all_play_true();
	}
		
	function all_play_true(){
		console.log('音声存在チェック',document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null);
		
		var TOTAL = 0;
		TARGET =  document.getElementById('slide_now_num_'+slide_now_num+'_audio');

		if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
			
			
			TARGET.play();
			//audioタグシークバー同期処理
			//シークバーが終了位置にあると、自動的に頭出ししてくれる。audio.jsの処理？

			let NOW =TARGET.currentTime*1000;
			TOTAL = TARGET.duration*1000;
			TOTAL = TOTAL - NOW;
			console.log('NOW：',NOW);
			console.log('TOTAL：',TOTAL);

		}else{
			TOTAL = 3000;
			console.log('TOTAL：',TOTAL);
			//デフォルト3s
		}
		

		var next_slide = function(){

			if(slide_now_num !== slide_num && all_play_flag){
					$('.slider'+slide_ul_num).slick('slickNext');
			   all_play_true();
				}else{
					//再生ボタン表示切替
					$('.all_play').show();
					$('.all_play_stop').hide();
					
					//最初のページに戻す処理　無効化					//$('.slider'+slide_ul_num).slick('slickNext');
				}
  		}
		 
		setTimeout(next_slide, TOTAL);
	}

//2)一時停止処理
	function all_play_stop_tmp(){
		
		all_play_flag = false;
		if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
					TARGET.pause();
			
				}
}
	
//⑦停止ボタン押下後 不要？
	function all_play_stop(){
		
		all_play_flag = false;
		if(document.getElementById('slide_now_num_'+slide_now_num+'_audio') != null){
			TARGET.currentTime = 0;
			TARGET.pause();
			
				}

}
	



	
  </script>
  
<!--recorder.js 音声録音ライブラリ読み込み-->
 <script src="Recorderjs-master/dist/recorder.js"></script>
</body>
</html>
