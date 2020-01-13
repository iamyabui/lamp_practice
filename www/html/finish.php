<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
//１，tokenの照合
$token_post = get_post('token');

if(is_valid_csrf_token($token_post) === false){
  set_error('不正なアクセスです。');
  redirect_to(HOME_URL);
}

//２，再度新しいトークンを書き換え
$token = get_csrf_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);
// 在庫からカート内商品購入数を引いて、itemsテーブルの在庫数を更新
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 
// カート内の合計金額を取得
$total_price = sum_carts($carts);

header('X-FRAME-OPTIONS: DENY');
// finish_view.phpに一度だけ飛ぶ
include_once '../view/finish_view.php';