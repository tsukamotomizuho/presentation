<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
ssidChk();//セッションチェック関数

//jsonでの値の受け渡しに失敗。
//原因が不明。デバックできなかった。★要質問			
//1. POSTデータ取得
//if(
//  !isset($_POST["type"]) || $_POST["type"]=="" ||
//  !isset($_POST["word"]) || $_POST["word"]=="" ||
//  !isset($_POST["kind"]) || $_POST["kind"]=="" ||
//  !isset($_POST["feel"]) || $_POST["feel"]==""
//){
//  exit('ParamError');
//	
//}
//
//$player_name   = $_SESSION["player_name"]
//$word   = $_POST["word"];
//$type   = $_POST["type"];
//$kind   = $_POST["kind"];
//$feel   = $_POST["feel"];


//1. POSTデータ取得
if(
  !isset($_POST["lesson"]) || $_POST["lesson"]=="" 
){
  exit('ParamError');
	
}

$player_name   = $_SESSION["player_name"];
$lesson   = $_POST["lesson"];

list($word, $type, $kind ,$feel) = split('[/.-]', $lesson);
//echo "word: $word; type: $type; kind: $kind; feel: $feel<br />\n";

//2.  DB接続します
$pdo = db_con();

//３．SQLを作成(stmlの中で)
$stmt = $pdo->prepare("INSERT INTO word_table(id, player_name, word, type, kind, feel)VALUES(NULL, :player_name, :word, :type, :kind, :feel)");
$stmt->bindValue(':player_name', $player_name, PDO::PARAM_STR); 
$stmt->bindValue(':word', $word, PDO::PARAM_STR);
$stmt->bindValue(':type', $type, PDO::PARAM_STR);
$stmt->bindValue(':kind', $kind, PDO::PARAM_STR);
$stmt->bindValue(':feel', $feel, PDO::PARAM_STR);

$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT

//３．データ表示
$view_table="";
if($status==false){
    echo "false";
}else{
    echo "success";//echoでreturn
}
?>
