<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
//cart.phpで作成されたトークンをcart_view.php経由で取得
$token = get_post('token');

//cart_view.phpのpostで受け取ったトークンと、cart.phpで取得したトークンの照合
// 照合に失敗した場合
if(is_valid_csrf_token($token) === false){
  // セッションにエラーメッセージを渡す
  set_error('不正なアクセスです。');
  // index.phpに飛ぶ
  redirect_to(HOME_URL);
}

// ログインしていない場合、ログインページに飛ぶ
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//DBへ接続
$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);

// cart_view.phpでpostしたcart_idを取得
$cart_id = get_post('cart_id');
// ログインユーザのカート情報を渡す
$carts = get_user_carts($db, $user['user_id']);
// ログインユーザのカート内合計金額を計算
$total_price = sum_carts($carts);
// ordersテーブルにカラムを追加
insert_order($db, $user['user_id']);
// ordersテーブルで追加されたorder_idを取得
$order_id = get_order_id($db);
// detailsテーブルにカラムを追加
insert_detail($db, $order_id, $carts['item_id'], $carts['amount'], $carts['price']);

// cart_view.phpでpostしたcart_idに対して行ごと削除
if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  set_error('カートの削除に失敗しました。');
}

//cart.phpに飛ぶ
redirect_to(CART_URL);