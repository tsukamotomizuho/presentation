<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_name"]) || $_POST["slide_name"]=="" 
){
  exit('ParamError');
}

//1. POST受信
$slide_name    = $_POST["slide_name"];//スライド名


 echo var_dump($_POST);//filenameはこれで表示
 echo var_dump($_FILES);//blobデータはこれで表示


//2. DB接続
$pdo = db_con();//functions.phpから呼び出し


//スライドグループ取得SQL
	//スライドグループ(初期値)
	$slide_group_id=0;

	//sqlのselect実行文
	$voice_table_sql = 'SELECT * FROM slide_table  ORDER BY slide_id DESC LIMIT 1';

	$stmt = $pdo->prepare($voice_table_sql);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$slide_group_id      = $r["slide_group_id"] + 1;
		}


//Fileアップロードチェック
if (isset($_FILES["upfile"])) {
    //情報取得
    $file_name = $_FILES["upfile"]["name"]; 
	$slide_data ='';
	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["upfile"]["tmp_name"]; 
	
	//"/usr/www/tmp/1.jpg"アップロード先のTempフォルダ
    $file_dir_path = "upload/";  //画像ファイル保管先

	for ($i=0; $i < count($file_name); $i++) {
		//***File名の変更***(ユニークファイル名)
		$extension = pathinfo($file_name[$i], PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
		$file_name[$i] = date("YmdHis")."_slide_group_id".$slide_group_id."_slide_now_num".$i."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

		
			// FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path[$i] ) ) {
				if ( move_uploaded_file( $tmp_path[$i], $file_dir_path . $file_name[$i] ) ) {
					//一時フォルダからupload/1.jpgへ移動、ファイル名は変更可能
					chmod( $file_dir_path . $file_name[$i], 0644 );//ファイルに権限付与 0644
					echo $file_name[$i] . "をアップロードしました。";

			} else {
					echo $file_name[$i] . "をアップロードできませんでした。";//Error文字
			}
    	}// FileUpload [--End--]
	}
}else{
		echo "スライドを受信できませんでした。";
}





//ファイル名挿入(デバック用)
//$slide_data = $file_name[$i];
//スライド番号


for ($i=0; $i < count($file_name); $i++) {

//３．SQLを作成(stmlの中で)
$stmt = $pdo->prepare("INSERT INTO slide_table(slide_id,slide_group_id, slide_name, slide_num, slide_now_num, slide_data, user_id,create_date )VALUES(NULL, :slide_group_id,:slide_name, :slide_num, :slide_now_num, :slide_data, :user_id, sysdate())");
$stmt->bindValue(':slide_group_id', $slide_group_id, PDO::PARAM_INT); 
$stmt->bindValue(':slide_name', $slide_name, PDO::PARAM_STR); 
$stmt->bindValue(':slide_num', count($file_name), PDO::PARAM_INT);
$stmt->bindValue(':slide_now_num', $i, PDO::PARAM_INT);
$stmt->bindValue(':slide_data', $file_name[$i], PDO::PARAM_STR);
$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT
//phpの予約語に注意★
	}


//４．エラー表示
if($status==false){
	queryError($stmt);
}

//ajax処理のため、以下は不要
//if($status==false){
//	queryError($stmt);
//  
//}else{//処理が終われば『index.php』に戻る。
//	
//	if(!isset($_SESSION["chk_ssid"]) || 
//	   $_SESSION["chk_ssid"] != session_id()
//	  ){
//		  header("Location: home.php");//スペース必須
//		  exit;//おまじない
//	}else{
//		  header("Location: home.php");//スペース必須
//		  exit;//おまじない
//	}
//	
//
//}
?>
