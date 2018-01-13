<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数


//入力チェック(受信確認処理追加)　ソート処理①
if(
  !isset($_POST["slide_name"]) || $_POST["slide_name"]=="" ||
  !isset($_POST["slide_data_ul"]) || $_POST["slide_data_ul"]=="" 
){
  exit('ParamError');
}

//1. POST受信
$slide_name    = $_POST["slide_name"];//スライド名
$slide_data_ul = $_POST["slide_data_ul"];//ソート済スライド配列番号　ソート処理②

// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//blobデータはこれで表示

//ソート済スライド配列番号　配列化　ソート処理③
$slide_data_ul = explode("/", $slide_data_ul);
array_pop($slide_data_ul);
echo var_dump($slide_data_ul);

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//①スライド総数と最新のスライドグループを取得＋1
	$stmt = $pdo->prepare("SELECT * FROM slide_table WHERE user_id =".$_SESSION["user_id"]." ORDER BY slide_group DESC LIMIT 1");
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

	//最新の取得スライドからslide_groupとslide_numを取得
	if($status==false){
		queryError($stmt);
	}else{//正常
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$slide_group      = $r["slide_group"] + 1;
		}
	}

////スライドグループ取得SQL
//	//スライドグループ(初期値)
//	$slide_group=0;
//
//	//sqlのselect実行文
//	$voice_table_sql = 'SELECT * FROM slide_table WHERE user_id='. $_SESSION["user_id"].' ORDER BY slide_id DESC LIMIT 1';
//
//	$stmt = $pdo->prepare($voice_table_sql);
//	$status = $stmt->execute();
//	//実行後、エラーだったらfalseが返る
//
//if($status==false){
//	queryError($stmt);
//	exit('Error:スライドグループ取得SQL');
//}else{
//	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
//			$slide_group      = $r["slide_group"] + 1;
//		}
//}

	echo 'slide_group/'.$slide_group.'/';	

//Fileアップロードチェック
if (isset($_FILES["upfile"])) {
    //情報取得
    $file_name = $_FILES["upfile"]["name"]; 
	$slide_data ='';
	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["upfile"]["tmp_name"]; 
	
	//"/usr/www/tmp/1.jpg"アップロード先のTempフォルダ
    $file_dir_path = "upload_slide/";  //画像ファイル保管先

	for ($i=0; $i < count($file_name); $i++) {
		
		$slide_now_num = $i + 1;
		
		//ソート済スライド番号　ソート処理④
		$sorted_i = $slide_data_ul[$i];
		
		//***File名の変更***(ユニークファイル名)
		$extension = pathinfo($file_name[$sorted_i], PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
		$file_name[$sorted_i] = date("YmdHis")."_slide_group".$slide_group."_slide_now_num".$slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

		
			// FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path[$sorted_i] ) ) {
				if ( move_uploaded_file( $tmp_path[$sorted_i], $file_dir_path . $file_name[$sorted_i] ) ) {
					//一時フォルダからupload_slide/1.jpgへ移動、ファイル名は変更可能
					chmod( $file_dir_path . $file_name[$sorted_i], 0644 );//ファイルに権限付与 0644
					echo "スライド:".$file_name[$sorted_i] . "をフォルダにアップロードしました。/";

			} else {
					echo $file_name[$sorted_i] . "をフォルダにアップロードできませんでした。";//Error文字
			}
    	}// FileUpload [--End--]
	}
}else{
		echo "スライドを受信できませんでした。";
}

//スライド総数を取得
$slide_num =count($file_name);

//スライド番号(file_now_num)を一致させるため、一つずらす。　ソート処理⑤
//array_unshift($file_name, "dumy");
array_unshift($slide_data_ul, "dumy");

for ($i=1; $i <= $slide_num ; $i++) {

	//ソート済スライド番号　ソート処理⑥
	$sorted_i = $slide_data_ul[$i];
		
	//３．SQLを作成(stmlの中で)
	$stmt = $pdo->prepare("INSERT INTO slide_table(slide_id,slide_group, slide_name, slide_num, slide_now_num, slide_data, user_id,create_date )VALUES(NULL, :slide_group,:slide_name, :slide_num, :slide_now_num, :slide_data, :user_id, sysdate())");
	$stmt->bindValue(':slide_group', $slide_group, PDO::PARAM_INT); 
	$stmt->bindValue(':slide_name', $slide_name, PDO::PARAM_STR); 
	$stmt->bindValue(':slide_num', $slide_num, PDO::PARAM_INT);
	$stmt->bindValue(':slide_now_num', $i, PDO::PARAM_INT);
	$stmt->bindValue(':slide_data', $file_name[$sorted_i], PDO::PARAM_STR);
	$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る
	//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)

	//４．エラー表示
	if($status==false){
		queryError($stmt);
		echo "スライド:".$file_name[$sorted_i] . "をDBに登録できませんでした。/";
	}else{
		echo "スライド:".$file_name[$sorted_i] . "をDBに登録しました。/";
	}

}



?>
