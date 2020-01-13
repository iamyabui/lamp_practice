<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();
$_SESSION = array();
// クッキーセッションをパラメータで取得
$params = session_get_cookie_params();
// Cookieの値を空文字、有効期限を過去の時間に設定してクッキーを削除
setcookie(session_name(), '', time() - 42000,
  // クッキーを保存するPath
  $params["path"], 
  // クッキーが有効なドメイン
  $params["domain"],
  // クッキーのセキュアフラグ(HTTPS)
  $params["secure"], 
  // クッキーの通信フラグ（HTTP)
  $params["httponly"]
);
// セッションの削除
session_destroy();

redirect_to(LOGIN_URL);

