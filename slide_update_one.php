<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" ||
  !isset($_POST["slide_name"]) || $_POST["slide_name"]==""||
  !isset($_POST["slide_group"]) || $_POST["slide_group"]==""
){
  exit('ParamError');
}

//1. POST受信
$slide_name     = $_POST["slide_name"];//スライド名
$slide_group    = $_POST["slide_group"];//スライドグループ
$slide_now_num  = $_POST["slide_now_num"];//スライド番号


 echo var_dump($_POST);//filenameはこれで表示
 echo var_dump($_FILES);//blobデータはこれで表示


//2. DB接続
$pdo = db_con();//functions.phpから呼び出し


//スライドグループ取得SQL

	//sqlのselect実行文
	$slide_table_sql = 'SELECT * FROM slide_table
	WHERE user_id='. $_SESSION["user_id"].' AND 
		slide_group= '. $slide_group.' AND 
		slide_now_num = '. $slide_now_num.' 
		ORDER BY slide_id DESC LIMIT 1';

	$stmt = $pdo->prepare($slide_table_sql);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('Error:過去スライド取得(単体)失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$slide_data_old = $r["slide_data"];
			$slide_id = $r["slide_id"];
			$slide_num = $r["slide_num"];
 			echo $slide_data_old;
 			echo '　スライドid　'.$slide_id;
		}
}

//Fileアップロードチェック(前回のファイル削除＆今回のファイル登録)
if (isset($_FILES["slide_update_one"])) {
    //情報取得
    $file_name = $_FILES["slide_update_one"]["name"]; 
	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["slide_update_one"]["tmp_name"]; 
	
	//"/usr/www/tmp/1.jpg"アップロード先のTempフォルダ
    $file_dir_path = "upload/";  //画像ファイル保管先

	//***File名の変更***(ユニークファイル名)
	$extension = pathinfo($file_name, PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
	$file_name = date("YmdHis")."_slide_group".$slide_group."_slide_now_num".$slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

			echo '　新スライド名　'.$file_name;
		
			// FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path ) ) {
				if ( move_uploaded_file( $tmp_path, $file_dir_path . $file_name ) ) {
				//一時フォルダからupload/へ移動、ファイル名変更
				chmod( $file_dir_path . $file_name, 0644 );//ファイルに権限付与 0644
				echo "新スライド:".$file_name . "をアップロードしました。/";
					if(unlink($file_dir_path . $slide_data_old )){
						echo "旧スライド:".$slide_data_old . "を削除しました。/";
					}else{
						echo "旧スライド:".$slide_data_old . "を削除できませんでした。/";
					}

			} else {
					echo $file_name . "をアップロードできませんでした。";//Error文字
			}
    	}// FileUpload [--End--]

}else{
		echo "スライドを受信できませんでした。";
}

//３．SQLを作成(stmlの中で)

$stmt = $pdo->prepare("UPDATE slide_table SET slide_data = :slide_data,create_date= sysdate() WHERE slide_id= :slide_id");
$stmt->bindValue(':slide_data', $file_name, PDO::PARAM_STR);
$stmt->bindValue(':slide_id', $slide_id, PDO::PARAM_STR);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
//数値の場合はPDO::PARAM_INT
//phpの予約語に注意★

//４．エラー表示
if($status==false){
	queryError($stmt);
}

?>
