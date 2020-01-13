<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();
$token = get_post('token');

if(is_valid_csrf_token($token) === false){
  set_error('不正なアクセスです。');
  redirect_to(HOME_URL);
}

if(is_logined() === true){
  redirect_to(HOME_URL);
}

$name = get_post('name');
$password = get_post('password');

$db = get_db_connect();

// ログインしようとしているユーザIDのユーザ情報を取得
$user = login_as($db, $name, $password);
// ユーザ名もしくはPasswordが誤っていた場合、エラーメッセージ表示
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
// ログインユーザがAdminの場合、admin.phpに飛ぶ
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
// アドミンユーザでない場合、index.phpに飛ぶ
redirect_to(HOME_URL);