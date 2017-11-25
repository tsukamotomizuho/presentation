<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

 echo var_dump($_POST);//filenameはこれで表示
 echo var_dump($_FILES);//blobデータはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["file_name"]) || $_POST["file_name"]=="" ||
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" ||
  !isset($_POST["slide_name"]) || $_POST["slide_name"]=="" 
){
  exit('ParamError');
}

//1. POST受信
	//音声ファイル名取得
	$file_name=$_POST["file_name"];
	//スライド番号取得
	$slide_now_num=$_POST["slide_now_num"];
	//スライド名
	$slide_name=$_POST["slide_name"];
	
 echo $file_name;
 echo '　　'+$slide_now_num;
 echo '　　'+$slide_now_num;

//Fileアップロードチェック
if (isset($_FILES["slide_name"])) {

//ここから★★！！
	$sound_data ='';
	
	//アップロード先のTempフォルダ	
	$tmp_path  = $_FILES["sound_blob"]["tmp_name"]; 
	
	//画像ファイル保管先
    $file_dir_path = "upload_sound/";  

    //***File名の変更***(ユニークファイル名)
    $extension = pathinfo($file_name, PATHINFO_EXTENSION); //拡張子取得(.wav)
    $file_name = date("YmdHis")."_slide_".slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

	$slide_data = $slide_data."/".$file_name[$i];

    $img="";  //画像表示orError文字を保持する変数
		
    // FileUpload [--Start--]
    if ( is_uploaded_file( $tmp_path[$i] ) ) {
        if ( move_uploaded_file( $tmp_path[$i], $file_dir_path . $file_name[$i] ) ) {
			//一時フォルダからupload/1.jpgへ移動、ファイル名は変更可能
            chmod( $file_dir_path . $file_name[$i], 0644 );//ファイルに権限付与 0644
            //echo $file_name . "をアップロードしました。";

        } else {
            //$img = "Error:アップロードできませんでした。"; //Error文字
        }
    }
    // FileUpload [--End--]

}else{
//    $img = "画像が送信されていません"; //Error文字
}


//
////2. DB接続
//$pdo = db_con();//functions.phpから呼び出し
//
////３．SQLを作成(stmlの中で)
//$stmt = $pdo->prepare("INSERT INTO slide_table(slide_id, slide_name, slide_num, slide_data, create_date )VALUES(NULL, :slide_name, :slide_num, :slide_data, sysdate())");
//$stmt->bindValue(':slide_name', $slide_name, PDO::PARAM_STR); 
//$stmt->bindValue(':slide_num', count($file_name), PDO::PARAM_INT);
//$stmt->bindValue(':slide_data', $slide_data, PDO::PARAM_STR);
//$status = $stmt->execute();
////実行後、エラーだったらfalseが返る
////PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
////数値の場合はPDO::PARAM_INT
////phpの予約語に注意★
//
////４．エラー表示
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
//}	


?>
