<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<!--<link rel="stylesheet" href="css/main.css" />-->
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}</style>
<title>ログイン</title>
</head>
<body>

<header>

  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
      <div class="navbar-brand" href="login.php">管理者ログイン</div>
      </div>
    </div>
  </nav>
  
</header>

<!-- lLOGINogin_act.php は認証処理用のPHPです。 -->
<form name="form1" action="login_act.php" method="post">
<div>ID : <input type="text" name="lid" /></div>
<div>PW: <input type="password" name="lpw" /></div>
<div><input type="submit" value="LOGIN" /></div>
</form>
<a class="navbar-brand" href="free_select.php">[一般ユーザ様はこちらからどうぞ]</a>

</body>
</html>