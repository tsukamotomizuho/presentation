<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

 echo var_dump($_POST);//filenameはこれで表示


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_group"]) || $_POST["slide_group"]=="" || 
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" 

){
  exit('ParamError：POST受信失敗');
}

//1. POST受信
	//スライドグループ
	$slide_group=$_POST["slide_group"];
	//スライド番号
	$slide_now_num=$_POST["slide_now_num"];

//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//3.旧アイコンファイルデータ取得＆旧ファイル削除
$icon_id_old   = array();
$icon_data_old = array();

//アップロード先のフォルダ
$file_dir_path = "upload_icon/";

//sqlのselect実行文(全削除orスライド単位削除で場合分け)
if($slide_now_num == 0){
	//アイコン全削除の場合
	$icon_table_sql = 'SELECT * FROM icon_table 
	WHERE user_id='. $_SESSION["user_id"].
		' AND slide_group = '.$slide_group;
}else{
	//アイコンスライド単位削除の場合
	$icon_table_sql = 'SELECT * FROM icon_table 
	WHERE user_id='. $_SESSION["user_id"].' AND 
		slide_group = '.$slide_group.' AND 
		slide_now_num = '.$slide_now_num;
}


$stmt = $pdo->prepare($icon_table_sql);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('/ParamError：アイコンid取得SQL失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		array_push($icon_id_old, $r["icon_id"]);
		array_push($icon_data_old, $r["icon_data"]);
		
		if(unlink($file_dir_path . $r["icon_data"] )){
			echo "/旧アイコン:".$r["icon_data"] . "をフォルダから削除しました。/";
		}else{
			echo "/旧アイコン:".$r["icon_data"] . "をフォルダから削除できませんでした。/";
		}
	}
}

 echo '　　/slide_group：'.$slide_group. '　　';
 echo '　　/icon_id_old：'.var_dump($icon_id_old). '　　';


//5．旧スライドDB削除
$icon_num_old =count($icon_id_old);

for ($i=0; $i < $icon_num_old ; $i++) {
	$stmt = $pdo->prepare('DELETE FROM icon_table  WHERE icon_id= :icon_id');	
	//SQL実行
	$stmt->bindValue(':icon_id', $icon_id_old[$i], PDO::PARAM_INT);
	$status = $stmt->execute();
	echo '/旧ファイル：'.$icon_data_old[$i] . "をDBから削除しました。";
	
	//実行後、エラーだったらfalseが返る
	echo '　　/SQLステータス：'.$status;
}


if($status==false){
	queryError($stmt);
}



?>
