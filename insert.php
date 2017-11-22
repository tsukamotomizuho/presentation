<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

////入力チェック(受信確認処理追加)
//if(
//  !isset($_POST["book_name"]) || $_POST["book_name"]=="" ||
//  !isset($_POST["book_url"]) || $_POST["book_url"]=="" ||
//  !isset($_POST["book_comment"]) || $_POST["book_comment"]==""
//){
//  exit('ParamError');
//}
//
////1. POST受信
//$book_name    = $_POST["book_name"];
//$book_url     = $_POST["book_url"];
//$book_comment = $_POST["book_comment"];
//



//Fileアップロードチェックs
if (isset($_FILES["upfile"])) {
    //情報取得
    $file_name = $_FILES["upfile"]["name"]; 

    //デバック
	var_dump($file_name);
	echo"<br>配列0：";
	echo $file_name[0];
	echo"<br>配列数：";
	echo count($file_name); 
	echo"<br>";
	
	$slide_name ='テストスライド';
	$slide_data ='';

	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["upfile"]["tmp_name"]; 
	echo"ファイル名配列";
	var_dump($tmp_path);
	echo"<br>";	

	
	//"/usr/www/tmp/1.jpg"アップロード先のTempフォルダ
    $file_dir_path = "upload/";  //画像ファイル保管先

	for ($i=0; $i < count($file_name); $i++) {
    echo $i;
	echo"<br>";
    //***File名の変更***(ユニークファイル名)
    $extension = pathinfo($file_name[$i], PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
    $file_name[$i] = date("YmdHis").$i."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

	$slide_data = $slide_data."/".$file_name[$i];

    $img="";  //画像表示orError文字を保持する変数
    // FileUpload [--Start--]
    if ( is_uploaded_file( $tmp_path[$i] ) ) {
        if ( move_uploaded_file( $tmp_path[$i], $file_dir_path . $file_name[$i] ) ) {
			//一時フォルダからupload/1.jpgへ移動、ファイル名は変更可能
            chmod( $file_dir_path . $file_name[$i], 0644 );//ファイルに権限付与 0644
            //echo $file_name . "をアップロードしました。";
            //$img = '<img width="300" src="'. $file_dir_path . $file_name . '" >'; //画像表示用HTML
        } else {
            //$img = "Error:アップロードできませんでした。"; //Error文字
        }
    }
    // FileUpload [--End--]
	}
}else{
//    $img = "画像が送信されていません"; //Error文字
}

echo 'スライドデータ<br>';
echo $slide_data;
echo '<br>';

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//３．SQLを作成(stmlの中で)
$stmt = $pdo->prepare("INSERT INTO slide_table(id, slide_name, slide_num, slide_data,
update)VALUES(NULL, :slide_name, :slide_num, :slide_data, sysdate() )");
$stmt->bindValue(':slide_name', $slide_name, PDO::PARAM_STR); 
$stmt->bindValue(':slide_num', count($file_name), PDO::PARAM_STR);
$stmt->bindValue(':slide_data', $slide_data, PDO::PARAM_STR);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT

//４．エラー表示
if($status==false){
	queryError($stmt);
  
}else{//処理が終われば『index.php』に戻る。
	
	if(!isset($_SESSION["chk_ssid"]) || 
	   $_SESSION["chk_ssid"] != session_id()
	  ){
		  header("Location: free_index.php");//スペース必須
		  exit;//おまじない
	}else{
		  header("Location: index.php");//スペース必須
		  exit;//おまじない
	}
	

}
?>
