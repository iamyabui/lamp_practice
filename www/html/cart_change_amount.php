<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
// cart.phpのpost処理で取得したトークンの取得
$token = get_post('token');
// cart.phpで作成されたトークンとpostで受け取ったトークンの照合が失敗した場合
if(is_valid_csrf_token($token) === false){
  // エラーメッセージをセッション変数に渡す
  set_error('不正なアクセスです。');
  // index.phpに飛ぶ
  redirect_to(HOME_URL);
}

// セッションに保管したuser_idがない場合（ログインしていない場合）
if(is_logined() === false){
  // login.phpに飛ぶ
  redirect_to(LOGIN_URL);
}

//DB接続
$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);

// cart_view.phpでpostしたcart_idを取得
$cart_id = get_post('cart_id');
// cart_view.phpでpostしたamountを取得
$amount = get_post('amount');

// postで渡されたカート番号に対して購入予定数を更新
if(update_cart_amount($db, $cart_id, $amount)){
  set_message('購入数を更新しました。');
} else {
  set_error('購入数の更新に失敗しました。');
}
// cart.phpに飛ぶ
redirect_to(CART_URL);