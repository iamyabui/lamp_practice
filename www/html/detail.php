<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'order.php';
require_once MODEL_PATH . 'detail.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();
//order.phpで作成されたトークンをindex_view.php経由で取得
$token = get_post('token');
//cart_view.phpのpostで受け取ったトークンと、cart.phpで取得したトークンの照合が失敗した場合
if(is_valid_csrf_token($token) === false){
  // セッションにエラーメッセージを渡す
  set_error('不正なアクセスです。');
  // index.phpに飛ぶ
  redirect_to(HOME_URL);
}

// セッションに保管したuser_idがない場合（ログインしていない場合）
if(is_logined() === false){
    // login.phpに飛ぶ
    redirect_to(LOGIN_URL);
  }

$db = get_db_connect();
// ログインユーザの情報を取得
$user = get_login_user($db);

// post処理でorder_idを取得
$order_id = get_post('order_id');
// 該当のorder_idについてdetailsテーブルから明細を取得
$details = get_user_details($db, $order_id);
// order_idの購入日時とuser_idを配列で取得
$orders = get_order($db, $order_id);
// order_idの合計金額を計算
$total_price =  sum_orders($db, $order_id);

if(is_admin($user) === false){
  if($orders['user_id'] !== $user['user_id']){
    redirect_to(ORDER_URL);
  }
}

header('X-FRAME-OPTIONS: DENY');
// detail_view.phpに一度だけ飛ぶ
include_once '../view/detail_view.php';