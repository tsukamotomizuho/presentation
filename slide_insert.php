<?php

session_start();

//0.外部ファイル読み込み
include("functions.php");
//ssidChk();//セッションチェック関数


//入力チェック(受信確認処理追加)
if(
  !isset($_POST["slide_name"]) || $_POST["slide_name"]=="" 
){
  exit('ParamError');
}

//1. POST受信
$slide_name    = $_POST["slide_name"];//スライド名


// echo var_dump($_POST);//filenameはこれで表示
// echo var_dump($_FILES);//blobデータはこれで表示


//2. DB接続
$pdo = db_con();//functions.phpから呼び出し


//スライドグループ取得SQL
	//スライドグループ(初期値)
	$slide_group=0;

	//sqlのselect実行文
	$voice_table_sql = 'SELECT * FROM slide_table WHERE user_id='. $_SESSION["user_id"].' ORDER BY slide_id DESC LIMIT 1';

	$stmt = $pdo->prepare($voice_table_sql);
	$status = $stmt->execute();
	//実行後、エラーだったらfalseが返る

if($status==false){
	queryError($stmt);
	exit('Error:スライドグループ取得SQL');
}else{
	while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
			$slide_group      = $r["slide_group"] + 1;
		}
}

	echo 'slide_group/'.$slide_group.'/';	

//Fileアップロードチェック
if (isset($_FILES["upfile"])) {
    //情報取得
    $file_name = $_FILES["upfile"]["name"]; 
	$slide_data ='';
	
	//"1.jpg"ファイル名取得
    $tmp_path  = $_FILES["upfile"]["tmp_name"]; 
	
	//"/usr/www/tmp/1.jpg"アップロード先のTempフォルダ
    $file_dir_path = "upload_slide/";  //画像ファイル保管先

	for ($i=0; $i < count($file_name); $i++) {
		
		$slide_now_num = $i + 1;
		//***File名の変更***(ユニークファイル名)
		$extension = pathinfo($file_name[$i], PATHINFO_EXTENSION); //拡張子取得(jpg, png, gif)
		$file_name[$i] = date("YmdHis")."_slide_group".$slide_group."_slide_now_num".$slide_now_num."_" .md5(session_id()) . "." . $extension;  //ユニークファイル名作成//md5：暗号化

		
			// FileUpload [--Start--]
			if ( is_uploaded_file( $tmp_path[$i] ) ) {
				if ( move_uploaded_file( $tmp_path[$i], $file_dir_path . $file_name[$i] ) ) {
					//一時フォルダからupload_slide/1.jpgへ移動、ファイル名は変更可能
					chmod( $file_dir_path . $file_name[$i], 0644 );//ファイルに権限付与 0644
					echo "スライド:".$file_name[$i] . "をアップロードしました。/";

			} else {
					echo $file_name[$i] . "をアップロードできませんでした。";//Error文字
			}
    	}// FileUpload [--End--]
	}
}else{
		echo "スライドを受信できませんでした。";
}

//スライド総数を取得
$slide_num =count($file_name);

//スライド番号(file_now_num)を一致させるため、一つずらす。
array_unshift($file_name, "dumy");

for ($i=1; $i <= $slide_num ; $i++) {

//３．SQLを作成(stmlの中で)
$stmt = $pdo->prepare("INSERT INTO slide_table(slide_id,slide_group, slide_name, slide_num, slide_now_num, slide_data, user_id,create_date )VALUES(NULL, :slide_group,:slide_name, :slide_num, :slide_now_num, :slide_data, :user_id, sysdate())");
$stmt->bindValue(':slide_group', $slide_group, PDO::PARAM_INT); 
$stmt->bindValue(':slide_name', $slide_name, PDO::PARAM_STR); 
$stmt->bindValue(':slide_num', $slide_num, PDO::PARAM_INT);
$stmt->bindValue(':slide_now_num', $i, PDO::PARAM_INT);
$stmt->bindValue(':slide_data', $file_name[$i], PDO::PARAM_STR);
$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
$status = $stmt->execute();
//実行後、エラーだったらfalseが返る
//PDO::PARAM_STR 文字列なら追加(セキュリティ向上)
	}


//４．エラー表示
if($status==false){
	queryError($stmt);
}

?>
