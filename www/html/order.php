<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'order.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

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

$orders = get_user_orders($db, $user);

header('X-FRAME-OPTIONS: DENY');
// order_view.phpに一度だけ飛ぶ
include_once '../view/order_view.php';