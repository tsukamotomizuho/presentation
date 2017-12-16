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
  !isset($_POST["slide_group"]) || $_POST["slide_group"]=="" ||
  !isset($_POST["voice_time"]) || $_POST["voice_time"]==""
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
	//スライドグループ
	$slide_group=$_POST["slide_group"];
	//音声時間
	$voice_time=$_POST["voice_time"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//3.前回の音声ファイル取得SQL
$voice_id_old=null;
$voice_data_old=null;
	
	//sqlのselect実行文
	$slide_table_sql = 'SELECT * FROM voice_table 
	WHERE user_id='. $_SESSION["user_id"].
		' AND slide_group = '.$slide_group.
		' AND slide_now_num = '.$slide_now_num;

	$stmt = $pdo->prepare($slide_table_sql);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('ParamError：スライドid取得SQL失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$voice_id_old  = $r["voice_id"];
			$voice_data_old  = $r["voice_data"];
		}
}

 echo '/slide_now_num：'.$slide_now_num;
 echo '　　/slide_name：'.$slide_name;
 echo '　　/slide_group：'.$slide_group. '　　';
 echo '　　/voice_time：'.$voice_time. '　　';

if($voice_id_old){
 echo '　　/voice_id_old：'.$voice_id_old. '　　';
 echo '　　/voice_data_old：'.$voice_data_old. '　　';
}

//Fileチェック＆アップロード＆前回のファイル削除
if (isset($_FILES["sound_blob"]) && $_FILES["sound_blob"]["error"] == '0') {

	$sound_data ='';
	
	//アップロード先のTempフォルダ	
	$tmp_path  = $_FILES["sound_blob"]["tmp_name"]; 
	
	//画像ファイル保管先
    $file_dir_path = "upload_voice/";  

    //***File名の変更***(ユニークファイル名)
    $extension = pathinfo($file_name, PATHINFO_EXTENSION); //拡張子取得(.wav)
    $file_name = date("YmdHis")."_slide_group".$slide_group."_slide_num".$slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化
	
		
    // FileUpload [--Start--]
    if ( is_uploaded_file( $tmp_path ) ) {
		//新音声ファイルアップロード
        if ( move_uploaded_file( $tmp_path, $file_dir_path . $file_name ) ) {
			//一時フォルダからupload/1.jpgへ移動、ファイル名は変更可能
            chmod( $file_dir_path . $file_name, 0644 );//ファイルに権限付与 0644
            echo "/新音声:".$file_name . "をアップロードしました。";
			//旧音声ファイル削除
			if($voice_id_old){
				if(unlink($file_dir_path . $voice_data_old )){
					echo "/旧音声:".$voice_data_old . "を削除しました。/";
				}else{
					echo "/Error：旧音声:".$voice_data_old . "を削除できませんでした。/";
				}
			}
        } else {
            echo '/Error：新音声'.$file_name . "をアップロードできませんでした。";
//Error文字
        }
    }
    // FileUpload [--End--]

}else{
            echo '/Error：'.$file_name . "が受信できませんでした。";
}


//5．音声登録SQL作成

if($voice_id_old){
	//前回音声がある場合(update)
	$stmt = $pdo->prepare("UPDATE voice_table SET voice_data = :voice_data,create_date= sysdate() WHERE voice_id= :voice_id");
	$stmt->bindValue(':voice_data', $file_name, PDO::PARAM_STR);
	$stmt->bindValue(':voice_id', $voice_id_old, PDO::PARAM_STR);
	$status = $stmt->execute();
	echo '/新ファイル：'.$file_name . "でDBを更新しました。";
}else{
//前回音声がない場合(insert)
	$stmt = $pdo->prepare("INSERT INTO voice_table(voice_id,  slide_group, slide_now_num, voice_data, voice_time, user_id, create_date )VALUES(NULL, :slide_group , :slide_now_num, :voice_data, :voice_time, :user_id, sysdate())");
	$stmt->bindValue(':slide_group', $slide_group, PDO::PARAM_INT); 
	$stmt->bindValue(':slide_now_num', $slide_now_num, PDO::PARAM_INT);
	$stmt->bindValue(':voice_data', $file_name, PDO::PARAM_STR);
	$stmt->bindValue(':voice_time', $voice_time, PDO::PARAM_INT);
	$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
	$status = $stmt->execute();
	echo '/新ファイル：'.$file_name . "をDBに新規登録しました。";
}

//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT
//phpの予約語に注意★

echo '　　SQLステータス：'.$status;

if($status==false){
	queryError($stmt);
}



?>
