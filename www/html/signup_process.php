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
$password_confirmation = get_post('password_confirmation');

$db = get_db_connect();

// ユーザ名とパスワードのバリデーションチェック後、新規ユーザの登録
try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  // バリデーションチェックに失敗した場合、エラーメッセージを表示してsignup.phpに飛ぶ
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
  // DB処理において失敗した場合、エラーメッセージを表示してsignup.phpに飛ぶ
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
// ログイン成功後、セッションにuser_idを保存
login_as($db, $name, $password);
// index.phpに飛ぶ
redirect_to(HOME_URL);