<?php
session_start();

//0.外部ファイル読み込み
include("functions.php");

$lid = $_POST["lid"];
$lpw = $_POST["lpw"];

//1.  DB接続します
$pdo = db_con();//functions.phpから呼び出し

//2. データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM gs_user_table WHERE lid=:lid AND life_flg=:life_flg");
$stmt->bindValue(':lid', $lid);
$stmt->bindValue(':life_flg', 0);
$res = $stmt->execute();



//3. SQL実行時にエラーがある場合
if($res==false){
	queryError($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch(); //1レコードだけ取得する方法


//5. 該当レコードがあればSESSIONに値を代入
if( password_verify($_POST["lpw"],$val["lpw"])){
  $_SESSION["chk_ssid"]  = session_id();
  $_SESSION["kanri_flg"] = $val['kanri_flg'];
  $_SESSION["name"]      = $val['name'];
  header("Location: select.php");
}else{
  //logout処理を経由して前画面へ
  header("Location: logout.php");
}

exit();

?>

