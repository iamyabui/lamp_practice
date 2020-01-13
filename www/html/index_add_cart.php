<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();
//index.phpで作成されたトークンをindex_view.php経由で取得
$token = get_post('token');
//cart_view.phpのpostで受け取ったトークンと、cart.phpで取得したトークンの照合が失敗した場合
if(is_valid_csrf_token($token) === false){
  // セッションにエラーメッセージを渡す
  set_error('不正なアクセスです。');
  // index.phpに飛ぶ
  redirect_to(HOME_URL);
}
// セッションに保管したuser_idがない場合（ログインしていない場合）ログインページに飛ぶ
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);
// 表示OKの商品情報のみ取得
$item_id = get_post('item_id');

// カート内に選択した商品がない場合、カートテーブルに商品を追加、ある場合は該当商品の購入予定数のみ更新
if(add_cart($db, $item_id, $user['user_id'])){
  set_message('カートに商品を追加しました。');
} else {
  set_error('カートの更新に失敗しました。');
}

// index.phpに飛ぶ
redirect_to(HOME_URL);