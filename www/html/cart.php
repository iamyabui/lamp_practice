<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
// トークンの取得
$token = get_csrf_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);
// ログインユーザのカート情報を渡す
$carts = get_user_carts($db, $user['user_id']);
// ログインユーザのカート内合計金額を計算
$total_price = sum_carts($carts);

header('X-FRAME-OPTIONS: DENY');
include_once '../view/cart_view.php';