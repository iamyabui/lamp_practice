<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();
// トークンの取得
$token = get_csrf_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);
// ログインユーザがAdminでない場合、ログイン画面へ飛ぶ
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// 全商品の情報を取得
$items = get_all_items($db);

header('X-FRAME-OPTIONS: DENY');
// admin_view.phpを一回だけ読み込む
include_once '../view/admin_view.php';
