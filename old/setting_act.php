<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数

//1. POST受信
$player_name    = $_POST["player_name"];
//$password     = $_POST["password"];
$password       = password_hash($_POST["password"], PASSWORD_DEFAULT);
$gender         = $_POST["gender"];
$birthday       = $_POST["birthday"];

//2. DB接続
$pdo = db_con();

//３．SQLを作成(stmlの中で)
$stmt = $pdo->prepare("INSERT INTO player_table(id, player_name, password, gender, birthday, indate)VALUES(NULL, :player_name, :password, :gender, :birthday, sysdate())");
$stmt->bindValue(':player_name', $player_name, PDO::PARAM_STR); 
$stmt->bindValue(':password', $password, PDO::PARAM_STR);
$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
$stmt->bindValue(':birthday', $birthday, PDO::PARAM_STR);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT

//４．エラー表示
if($status==false){
  $error = $stmt->errorInfo();
  exit("QueryError:".$error[2]);//エラー表示
  
}else{
  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["player_name"] = $player_name;
  header("Location: intoro.php");//スペース必須
  exit;//おまじない

}
?>
