<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_now_num"]) || $_POST["slide_now_num"]=="" ||
  !isset($_POST["slide_group"]) || $_POST["slide_group"]=="" ||
  !isset($_POST["icon_start_time"]) || $_POST["icon_start_time"]==""
){
  exit('ParamError');
}

//1. POST受信
$slide_group    = $_POST["slide_group"];//スライドグループ
$slide_now_num  = $_POST["slide_now_num"];//スライド番号
$icon_start_time = $_POST["icon_start_time"];//アイコン表示開始総時間

// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//ファイルデータはこれで表示


//2. DB接続
$pdo = db_con();//functions.phpから呼び出し

//アイコンid
$icon_id;

//アイコンid取得SQL(重複するicon開始時間の検知)
//sqlのselect実行文
$icon_table_sql = 'SELECT * FROM icon_table
WHERE user_id='. $_SESSION["user_id"].' AND 
	slide_group= '. $slide_group.' AND 
	slide_now_num = '. $slide_now_num.' AND 
	icon_start_time = '. $icon_start_time.' 
	ORDER BY icon_id DESC LIMIT 1';

$stmt = $pdo->prepare($icon_table_sql);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('Error:アイコンid取得取得失敗');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$icon_id = $r["icon_id"];
			$icon_data_old = $r["icon_data"];
 		}
}



//Fileアップロードチェック(前回のファイル削除＆今回のファイル登録)
if (isset($_FILES["upicon"]) && $_FILES["upicon"]["error"] == '0') {

//3.情報取得＆File名の変更
    $file_name = $_FILES["upicon"]["name"]; 
	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["upicon"]["tmp_name"]; 

	//アップロード先のTempフォルダ
    $file_dir_path = "upload_icon/";  //画像ファイル保管先

	//***File名の変更***(ユニークファイル名)
	$extension = pathinfo($file_name, PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
	$file_name = date("YmdHis")."_slide_group".$slide_group."_slide_now_num".$slide_now_num."_icon_start_time".$icon_start_time."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

	echo 'new_file_name/'.$file_name.'/';
	
	
	
	//更新or新規登録判定判定
	if (isset($icon_id)){
			echo '/　前アイコンidあり/update処理　/';
		
			//4.FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path ) ) {
				if ( move_uploaded_file( $tmp_path, $file_dir_path . $file_name ) ) {
				//一時フォルダからupload_slide/へ移動、ファイル名変更
				chmod( $file_dir_path . $file_name, 0644 );//ファイルに権限付与 0644
				echo "新アイコン:".$file_name . "を".$file_dir_path."にアップロードしました。/";
					if(unlink($file_dir_path . $icon_data_old  )){
						echo "旧スライド:".$icon_data_old  . "を削除しました。/";
					}else{
						echo "旧スライド:".$icon_data_old . "を削除できませんでした。/";
					}

			} else {
					echo $file_name . "を".$file_dir_path."にアップロードできませんでした。";//Error文字
			}
    	}// FileUpload [--End--]

		//5．DB更新登録
		//前回iconがある場合(update)
		$stmt = $pdo->prepare("UPDATE icon_table SET icon_data = :icon_data,create_date= sysdate() WHERE icon_id= :icon_id");
		$stmt->bindValue(':icon_data', $file_name, PDO::PARAM_STR);
		$stmt->bindValue('icon_id', $icon_id, PDO::PARAM_INT);
		$status = $stmt->execute();
		//実行後、エラーだったらfalseが返る
		//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
		//数値の場合はPDO::PARAM_INT
		//phpの予約語に注意★

		//４．エラー表示
		if($status==false){
			queryError($stmt);
		}
		

	
	}else{
		echo '/　前回アイコンidなし/insert処理　/';

			//4.FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path ) ) {
				if ( move_uploaded_file( $tmp_path, $file_dir_path . $file_name ) ) {
				//一時フォルダからupload_slide/へ移動、ファイル名変更
				chmod( $file_dir_path . $file_name, 0644 );//ファイルに権限付与 0644
				echo "新アイコン:".$file_name . "を".$file_dir_path."にアップロードしました。/";
			} else {
					echo "新アイコン:".$file_name . "を".$file_dir_path."にアップロードできませんでした。/";//Error文字
			}
    	}// FileUpload [--End--]

		//5．DB新規登録
		//前回iconがない場合(insert)
			$stmt = $pdo->prepare("INSERT INTO icon_table(icon_id,  slide_group, slide_now_num, icon_start_time, icon_data, user_id, create_date )VALUES(NULL, :slide_group , :slide_now_num, :icon_start_time, :icon_data, :user_id, sysdate())");
			$stmt->bindValue(':slide_group', $slide_group, PDO::PARAM_INT); 
			$stmt->bindValue(':slide_now_num', $slide_now_num, PDO::PARAM_INT);
			$stmt->bindValue(':icon_start_time', $icon_start_time, PDO::PARAM_INT);
			$stmt->bindValue(':icon_data', $file_name, PDO::PARAM_STR);
			$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
			$status = $stmt->execute();
			echo '/新ファイル：'.$file_name . "をDBに新規登録しました。";

		//４．エラー表示
		if($status==false){
			queryError($stmt);
		}
		
	}

}else{
		echo "アイコンを受信できませんでした。";
}



?>
