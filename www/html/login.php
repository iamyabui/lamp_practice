<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();
// トークン取得
$token = get_csrf_token();
// 受け取ったセッション(user_id)に値が入っている場合（ログイン済の場合）index.phpに飛ぶ
if(is_logined() === true){
  redirect_to(HOME_URL);
}

header('X-FRAME-OPTIONS: DENY');
// login_view.phpを一度だけ実施
include_once '../view/login_view.php';