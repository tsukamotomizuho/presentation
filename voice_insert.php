<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//blobデータはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["file_name"]) || $_POST["file_name"]=="" ||
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" ||
  !isset($_POST["slide_name"]) || $_POST["slide_name"]==""||
  !isset($_POST["slide_group"]) || $_POST["slide_group"]==""
){
  exit('ParamError：POST受信失敗');
}

//1. POST受信
	//音声ファイル名取得
	$file_name=$_POST["file_name"];
	//スライド番号取得
	$slide_now_num=$_POST["slide_now_num"];
	//スライド名
	$slide_name=$_POST["slide_name"];
	//スライド名
	$slide_group=$_POST["slide_group"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し
//
////3.スライドid取得SQL ★廃止
//	//sqlのselect実行文
//	$slide_table_sql = 'SELECT * FROM slide_table 
//	WHERE user_id='. $_SESSION["user_id"].
//		' AND slide_group = '.$slide_group.
//		' AND slide_now_num = '.$slide_now_num.
//		' ORDER BY slide_id DESC LIMIT 1';
//
//	$stmt = $pdo->prepare($slide_table_sql);
//	$status = $stmt->execute();
//	//実行後、エラーだったらfalseが返る
//
//if($status==false){
//	queryError($stmt);
//	exit('ParamError：スライドid取得SQL失敗');
//}else{
//	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
//			$slide_id  = $r["slide_id"];
//		}
//}

 echo '/slide_now_num：'.$slide_now_num;
 echo '　　/slide_name：'.$slide_name;
 echo '　　/slide_group：'.$slide_group. '　　';


//Fileアップロードチェック
if (isset($_FILES["sound_blob"]) && $_FILES["sound_blob"]["error"] == '0') {

	$sound_data ='';
	
	//アップロード先のTempフォルダ	
	$tmp_path  = $_FILES["sound_blob"]["tmp_name"]; 
	
	//画像ファイル保管先
    $file_dir_path = "upload_sound/";  

    //***File名の変更***(ユニークファイル名)
    $extension = pathinfo($file_name, PATHINFO_EXTENSION); //拡張子取得(.wav)
    $file_name = date("YmdHis")."_slide_group".$slide_group."_slide_num".$slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化
	
		
    // FileUpload [--Start--]
    if ( is_uploaded_file( $tmp_path ) ) {
        if ( move_uploaded_file( $tmp_path, $file_dir_path . $file_name ) ) {
			//一時フォルダからupload/1.jpgへ移動、ファイル名は変更可能
            chmod( $file_dir_path . $file_name, 0644 );//ファイルに権限付与 0644
            echo $file_name . "をアップロードしました。";

        } else {
            echo 'Error：'.$file_name . "をアップロードできませんでした。";
//Error文字
        }
    }
    // FileUpload [--End--]

}else{
            echo 'Error：'.$file_name . "が受信できませんでした。";
}

//5．音声登録SQL作成
$stmt = $pdo->prepare("INSERT INTO voice_table(voice_id,  slide_group, slide_now_num, voice_data, user_id, create_date )VALUES(NULL, :slide_group , :slide_now_num, :voice_data, :user_id, sysdate())");
$stmt->bindValue(':slide_group', $slide_group, PDO::PARAM_INT); 
$stmt->bindValue(':slide_now_num', $slide_now_num, PDO::PARAM_INT);
$stmt->bindValue(':voice_data', $file_name, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
$status = $stmt->execute();

//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT
//phpの予約語に注意★

echo '　　SQLステータス：'.$status;

if($status==false){
	queryError($stmt);
}



?>
