<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//blobデータはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_group"]) || $_POST["slide_group"]==""
){
  exit('ParamError：POST受信失敗');
}

//1. POST受信
	//スライドグループ
	$slide_group=$_POST["slide_group"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//3.旧音声ファイルデータ取得＆旧ファイル削除
$voice_id_old   = array();
$voice_data_old = array();

//アップロード先のフォルダ
$file_dir_path = "upload_voice/";

//sqlのselect実行文
$slide_table_sql = 'SELECT * FROM voice_table 
WHERE user_id='. $_SESSION["user_id"].
	' AND slide_group = '.$slide_group;

$stmt = $pdo->prepare($slide_table_sql);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('/ParamError：ボイスid取得SQL失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		array_push($voice_id_old, $r["voice_id"]);
		array_push($voice_data_old, $r["voice_data"]);
		
		if(unlink($file_dir_path . $r["voice_data"] )){
			echo "/旧音声:".$r["voice_data"] . "をフォルダから削除しました。/";
		}else{
			echo "/旧音声:".$r["voice_data"] . "をフォルダから削除できませんでした。/";
		}
	}
}

 echo '　　/slide_group：'.$slide_group. '　　';
 echo '　　/voice_id_old：'.var_dump($voice_id_old). '　　';


//5．旧スライドDB削除
$voice_num_old =count($voice_id_old);

for ($i=0; $i < $voice_num_old ; $i++) {
	$stmt = $pdo->prepare('DELETE FROM voice_table  WHERE voice_id= :voice_id');	
	//SQL実行
	$stmt->bindValue(':voice_id', $voice_id_old[$i], PDO::PARAM_INT);
	$status = $stmt->execute();
	echo '/旧ファイル：'.$voice_data_old[$i] . "をDBから削除しました。";
	
	//実行後、エラーだったらfalseが返る
	echo '　　/SQLステータス：'.$status;
}


if($status==false){
	queryError($stmt);
}



?>
