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


$view_slide = '<div class="slider">';//slider開始タグ
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
	$voice_table_sql = 'SELECT * FROM voice_table WHERE slide_id ='.$view_slide_id.' AND slide_now_num ='.$i.' ORDER BY voice_id DESC LIMIT 1';

	$stmt = $pdo->prepare($voice_table_sql);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

//音声タグ作成
	$view_slide_now_num ='';
	$view_voice_data    ='';
	$file_dir_path      = "upload_voice/";//音声ファイル保管先

//6．audioタグ or エラー表示
	if($status==false){
		queryError($stmt);
	}else{//正常
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){

			$view_voice_id      = $r["voice_id"];
			$view_slide_now_num = $r["slide_now_num"];
			$view_voice_data    = $r["voice_data"];
			$view_voice_copy    .= $r["voice_data"].'/';


			$view_voice .= '<div id="slide_now_num_'.$view_slide_now_num.'">';//開始タグ
			$view_voice .= 'スライド'.$view_slide_now_num.'枚目の音声';
			$view_voice .= '<audio controls="" src=""></audio>';
			$view_voice .= '<a href="#" download="">音声ファイル名</a>';
			$view_voice .= '</div>';//終了タグ
			
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
<!--		<a class="navbar-brand" href="logout.php">スライド作成画面</a>-->
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
		

	
	<form method="post" action="slide_insert.php" enctype="multipart/form-data">
		<label for="upfile" >
			<h4><span class="label label-warning btn_effect">①スライドUL</span></h4>
			<input type="file" id="upfile"  name="upfile[]" webkitdirectory style="display:none;" />
		</label>
		<label for="save" >
			<h4><span class="label label-warning btn_effect">②スライドをDB登録</span></h4>
			<input id="save" type="submit" value="DB保存" style="display:none;" />
		</label>
	<!--★★ajax処理で送信に変更-->
	<!--https://qiita.com/yasumodev/items/cffb735f46ffd489a4db-->
	</form>
		  
	<label for="rec" >
		<h4><span class="label label-info btn_effect">③音声録音</span></h4>		  
  		<button id="rec" onclick="startRecording(this);" style="display:none;">record</button>
  	</label>
 
 	<label for="rec_stop" >
 		<h4><span class="label label-info btn_effect">④録音停止(ajax)</span></h4>		  
  		<button id="rec_stop" onclick="stopRecording(this);"  style="display:none;">stop</button>
   	</label>
   	
   	  
  <h5>Recordings</h5>
  <div id="recordingslist"></div>
  
  <h5>Log</h5>
  <div id="log"></div>

 	<label for="voice_save" >
 		<h4><span class="label label-info btn_effect">⑤音声をDB登録</span></h4>		  
  		<button id="voice_save" onclick="stopRecording(this);"  style="display:none;">stop</button>
   	</label>


		  		  <button id="input_btn3" type="button" class="btn btn-warning btn-block">④再生</button>


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
				<div class="slider">
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
	
	//スライドデバッグ用
		let view_slide_name ='<?=$view_slide_name?>';
		let view_slide_data ='<?=$view_slide_data_copy?>';
		let view_slide_id ='<?=$view_slide_id?>';
	
		console.log('スライド名',view_slide_name);
		console.log('スライドデータ',view_slide_data);
		console.log('スライドid',view_slide_id);
	

	//DBのスライド有無チェック
	//メモ：javascriptでphpを呼び出す際は、''でくくる
	let db_slide_chk = '<?=$view_slide_id?>' ;
	console.log("DBのスライド有無チェック",db_slide_chk);

	if(db_slide_chk != 'なし'){
	   $('.sample_slide').remove();
	   $('#slide_name').append('<?=$view_slide_name?>');
	   }

   
//スライド表示機能 （slick.jsを使用）
	
	$(function() {
	  //現在のスライド枚数表示処理
	  $('.slider').on('init', function(event, slick) {
			$('.current').text(slick.currentSlide + 1);
			$('.total').text(slick.slideCount);
		  
		  	slide_num =slick.slideCount;//スライド総数
			slide_now_num =slick.currentSlide + 1;//現在のスライド番号
	  })
		  .slick({
			// option here...
	  })
		  .on('beforeChange', function(event, slick, currentSlide, nextSlide) {
			$('.current').text(nextSlide + 1);
			slide_now_num = nextSlide + 1;
			console.log('現在のスライド番号：',slide_now_num);
		  	
		//audioタグ切替処理処理★★切り替えができない？なぜ？★★
//		 for (let x = 1; x <= slick.slideCount; x++){
//			 if(slide_now_num == x){
//				 $('#slide_now_num_'+slide_now_num).show();
//				 		  console.log('表示：',x);
//				}else{
//					$('#slide_now_num_'+slide_now_num).hide();
//					
//						console.log('非表示：',x);
//				}
//		 }
		  
		  
	  });
	});
	
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
			if(i == new_slide_num){
				slider_add += '</div>';
				$(".slide_area").prepend(slider_add);
				$('.slider'+slide_ul_num).slick();
				
		
			   }
		}
	}
	
});


//②音声録音機能-----------------------------------

//グローバル変数
	
	//音声データファイル名
		let view_voice_data ='<?=$view_voice_copy?>';
		console.log('取得した音声データ：');
		console.log(view_voice_data);
		let voice_data_split = view_voice_data.split('/');
		console.log(voice_data_split);
	
	
	//DBの音声の有無チェック＆DB取得音声を表示
	let db_voice_chk = '<?=$view_voice_id?>' ;
	console.log("DBの音声有無チェック",db_voice_chk);

	if(db_voice_chk != 'なし'){
		
	//1)phpで作成したタグを挿入
	$('#recordingslist').append('<?=$view_voice?>');
	
	//2)タグに情報を追記
//	for (let y = 1; y <= slide_num ; y++) ){
//
//		 //音声データを仮想urlに変換(セッション内のみ有効)
//		  let voice_data_path = 'upload_voice/'+voice_data_split[y];
//		
//		//音声データをローカルからどうやって取得するかわからない？★★ここから！！
//		
//		  var url = URL.createObjectURL(blob);
//
//		   $('#slide_now_num_'+y audio).attr({
//			  'src': xxx
//		   });
//	   
//
//		}
			
	   }
	
	
  function __log(e, data) {
	  console.log('音声ログ：',e,'  ',data || '');
      log.innerHTML += "\n" + e + " " + (data || '');
  }

  var audio_context;
  var recorder;

  function startUserMedia(stream) {
    var input = audio_context.createMediaStreamSource(stream);
    __log('Media stream created.');

    // Uncomment if you want the audio to feedback directly
    //input.connect(audio_context.destination);
    //__log('Input connected to audio context destination.');
    
	 //コンストラクタ
    recorder = new Recorder(input);
    __log('Recorder initialised.');
  }

	//録音開始
  function startRecording(button) {
    recorder && recorder.record();
//    button.disabled = true;
//    button.nextElementSibling.disabled = false;
    __log('Recording...');
  }

	//録音停止
  function stopRecording(button) {
    recorder && recorder.stop();
//    button.disabled = true;
//    button.previousElementSibling.disabled = false;
    __log('Stopped recording.');
    
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
//	div.style ="display: block;";
	div.innerHTML = 'スライド'+slide_now_num+'枚目の音声';

		
	//audioタグ編集
      au.controls = true;
      au.src = url;
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
	console.log('音声登録成功');
	});


}
	
  </script>
  
<!--recorder.js 音声録音ライブラリ読み込み-->
 <script src="Recorderjs-master/dist/recorder.js"></script>
</body>
</html>
