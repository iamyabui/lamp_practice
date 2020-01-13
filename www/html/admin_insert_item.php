<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();
$token = get_post('token');

if(is_valid_csrf_token($token) === false){
  set_error('不正なアクセスです。');
  redirect_to(HOME_URL);
}

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);
// ログインユーザがAdminでない場合、ログイン画面へ飛ぶ
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
// admin_view.phpのpost処理で取得した商品名、値段、商品表示ステータス、在庫、画像を取得
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');

$image = get_file('image');
// 管理ページのフォームで商品を新規登録
if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}


redirect_to(ADMIN_URL);