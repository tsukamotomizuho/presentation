<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

 echo var_dump($_POST);//filenameはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_group"]) || $_POST["slide_group"]=="" || 
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" || 
  !isset($_POST["icon_start_time"]) || $_POST["icon_start_time"]=="" 

){
  exit('ParamError：POST受信失敗');
}

//1. POST受信
	//スライドグループ
	$slide_group=$_POST["slide_group"];
	//スライド番号
	$slide_now_num=$_POST["slide_now_num"];
	//アイコン表示開始時間
	$icon_start_time=$_POST["icon_start_time"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//3.旧アイコンファイルデータ取得＆旧ファイル削除
$icon_id_old;
$icon_data_old;

//アップロード先のフォルダ
$file_dir_path = "upload_icon/";

//sqlのselect実行文(icon_id取得)
	$icon_table_sql = 'SELECT * FROM icon_table 
	WHERE user_id='. $_SESSION["user_id"].' AND 
		slide_group = '.$slide_group.' AND 
		slide_now_num = '.$slide_now_num.' AND 
		icon_start_time = '.$icon_start_time;

$stmt = $pdo->prepare($icon_table_sql);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('/ParamError：アイコンid取得SQL失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		$icon_id_old = $r["icon_id"];
		$icon_data_old = $r["icon_data"];
		
		if(unlink($file_dir_path . $r["icon_data"] )){
			echo "/旧アイコン:".$r["icon_data"] . "をフォルダから削除しました。/";
		}else{
			echo "/旧アイコン:".$r["icon_data"] . "をフォルダから削除できませんでした。/";
		}
	}
}

 echo '　　/slide_group：'.$slide_group. '　　';
 echo '　　/icon_id_old：'.$icon_id_old. '　　';


//5．旧スライドDB削除

	$stmt = $pdo->prepare('DELETE FROM icon_table  WHERE icon_id= :icon_id');	
	//SQL実行
	$stmt->bindValue(':icon_id', $icon_id_old, PDO::PARAM_INT);
	$status = $stmt->execute();
	echo '/旧ファイル：'.$icon_data_old . "をDBから削除しました。";
	
	//実行後、エラーだったらfalseが返る
	echo '　　/SQLステータス：'.$status;



if($status==false){
	queryError($stmt);
}



?>
