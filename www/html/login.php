<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();
$token = get_csrf_token();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

header('X-FRAME-OPTIONS: DENY');
include_once '../view/login_view.php';