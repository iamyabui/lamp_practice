<?php

function dd($var){
  var_dump($var);
  exit();
}

function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

// xxx_view.phpでpostされた値を返す、空の場合空文字を返す
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

// セッション変数に値が入っている場合、セッション変数の値を返す
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  //セッション変数に値がない場合、空文字を返す
  return '';
}

// 指定されたカラムに、値を代入してセッションにわたす??
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

// エラーメッセージをセッション変数の__errorsカラムに代入する。
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

function get_errors(){
  // セッションに渡された'__errors'カラムの値（エラーメッセージ）を$errorsに代入（空の場合は空文字を代入）
  $errors = get_session('__errors');

  // セッションに渡された'__errors'カラムの値が空の場合、空の配列を渡す??
  if($errors === ''){
    return array();
  }

  // ＄_SESSION['__errors']に配列を渡す??
  set_session('__errors',  array());
  // 
  return $errors;
}

function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//受け取ったセッション(user_id)に値が入っている場合⇛trueを返す
//受け取ったセッション(user_id)が空の場合⇛falseを返す
function is_logined(){
  return get_session('user_id') !== '';
}

function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  // 画像（$file['tmp_name']）の型を代入
  $mimetype = exif_imagetype($file['tmp_name']);
  // PERMITTED_IMAGE_TYPES配列に画像の方を代入??
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];

  return get_random_string() . '.' . $ext;
}

function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}
// 新規の画像ファイルを/assets/images/配下に保存
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}
// 新規の画像ファイルを/assets/images/から削除
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}


// 文字列の長さをチェック
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  // 文字列の長さを取得
  $length = mb_strlen($string);
  // 文字列の長さが最小値より大きく、最大値より小さい場合はtrue、そうでない場合はfalseを返す
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}
// バリデーションチェック、返り値はtrueかfalse
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}
// 結果が１の場合、is_valid_formatの返り値はtrue、0の場合はfalseを返す
function is_valid_format($string, $format){
  // $stringに対して、正規表現のマッチングを確認、マッチした場合１を返す
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  // HTTP POSTによりアップロードされたものかチェック
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  // 画像ファイルの型を取得
  $mimetype = exif_imagetype($image['tmp_name']);
  // 画像ファイルの型をチェック、png,jpegでない場合はエラーメッセージ出力
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

//htmlタグのエスケープ処理を実行
function h($string){
  return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// トークンの生成
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
}