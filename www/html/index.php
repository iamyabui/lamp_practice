<?php
require_once '../conf/const.php';
require_once '../model/functions.php';
require_once '../model/user.php';
require_once '../model/item.php';

session_start();
// トークンの取得
$token = get_csrf_token();
// セッションに保管したuser_idがない場合（ログインしていない場合）
if(is_logined() === false){
  // login.phpに飛ぶ
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);
// 表示OKの商品情報のみ取得
$items = get_open_items($db);

 // クリックジャッキング対策
 header('X-FRAME-OPTIONS: DENY');
//index_view.phpを読み込み、ただし二回目の場合読み込まない 
include_once '../view/index_view.php';