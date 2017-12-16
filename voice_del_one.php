<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//blobデータはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" ||
  !isset($_POST["slide_group"]) || $_POST["slide_group"]==""
){
  exit('ParamError：POST受信失敗');
}

//1. POST受信
	//スライド番号取得
	$slide_now_num=$_POST["slide_now_num"];
	//スライドグループ
	$slide_group=$_POST["slide_group"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//3.音声ファイル取得SQL
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
	exit('/ParamError：ボイスid取得SQL失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$voice_id_old  = $r["voice_id"];
			$voice_data_old  = $r["voice_data"];
		}
}

 echo '/slide_now_num：'.$slide_now_num;
 echo '　　/slide_group：'.$slide_group. '　　';
 echo '　　/voice_id_old：'.$voice_id_old. '　　';
 echo '　　/voice_data_old：'.$voice_data_old. '　　';


//アップロード先のフォルダ
$file_dir_path = "upload_voice/";  //画像ファイル保管先

//旧音声ファイル削除
if($voice_id_old){
	if(unlink($file_dir_path . $voice_data_old )){
		echo "/旧音声:".$voice_data_old . "をファイルから削除しました。/";
	}else{
		echo "/Error：旧音声:".$voice_data_old . "をファイルから削除できませんでした。/";
	}
}


//5．音声削除SQL作成
if($voice_id_old){
	$stmt = $pdo->prepare('DELETE FROM voice_table  WHERE voice_id= :voice_id');	
	//SQL実行
	$stmt->bindValue(':voice_id', $voice_id_old, PDO::PARAM_INT);
	$status = $stmt->execute();
	echo '/旧ファイル：'.$voice_data_old . "をDBから削除しました。";
}

//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)

echo '　　/SQLステータス：'.$status;

if($status==false){
	queryError($stmt);
}



?>
