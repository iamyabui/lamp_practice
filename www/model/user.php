<?php
require_once 'functions.php';
require_once 'db.php';

// 該当ユーザIDのユーザ情報を取得
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = :user_id
    LIMIT 1
  ";

  $params = array(':user_id' => $user_id);
  return fetch_query($db, $sql, $params);
}
// 該当ユーザのユーザ情報を取得
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = :name
    LIMIT 1
  ";

  $params = array(':name' => $name);
  return fetch_query($db, $sql, $params);
}
// ログイン成功後、セッションにuser_idを保存
function login_as($db, $name, $password){
  // ユーザ情報を取得
  $user = get_user_by_name($db, $name);
  // ユーザ情報の取得失敗もしくは、パスワードが入力されたパスワードと一致しない場合、falseを返す
  if($user === false || $user['password'] !== $password){
    return false;
  }
  // セッションにuser_idを保存する
  set_session('user_id', $user['user_id']);
  return $user;
}


function get_login_user($db){
  // セッションに保存されたuser_idを$login_user_idに渡す
  $login_user_id = get_session('user_id');
  // ログインユーザのユーザ情報を返す
  return get_user($db, $login_user_id);
}

// ユーザ名とパスワードがバリデーションチェックがOKの場合、usersテーブルに新規ユーザ情報を登録
function regist_user($db, $name, $password, $password_confirmation) {
  // ユーザ名とパスワードのバリデーションチェックがOKの場合はtrue、どちらか一つでもNGの場合はfalseを返す
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  return insert_user($db, $name, $password);
}

// 該当のユーザについてAdminである場合TRUEを返す、Admin出ない場合False
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}
// ユーザ名とパスワードのバリデーションチェックがOKの場合はtrue、どちらか一つでもNGの場合はfalseを返す
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}
// ユーザ名のバリデーションチェック
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
// passwordのバリデーションチェック
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}
// usersテーブルに新規ユーザ情報を登録
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (:name, :password);
  ";

  $params = array(':name' => $name, ':password' => $password);
  return execute_query($db, $sql, $params);
}

// 履歴画面、明細画面にデータをテーブルに挿入
function regist_order_transaction($db, $user_id, $carts){
  
  // トランザクション開始
  $db->beginTransaction();
  // カートで購入した内容をordersテーブルとdetailsテーブルに挿入、成功したらtrueを返す
  if(insert_order($db, $user_id) 
    && insert_detail($db, $carts['item_id'], $carts['amount'],$carts['price'])){
    $db->commit();
    return true;
  }
  // 上記失敗した場合ロールバック、falseを返す
  $db->rollback();
  return false;
  
}

// ordersテーブルに新しい購入明細を追加
function insert_order($db, $user_id){
  $sql = "
    INSERT INTO
      orders(
          user_id
      )
    VALUES(:user_id)
    ";

    $params = array(':user_id' => $user_id);
    return execute_query($db, $sql, $params);
}

// ？？detailsテーブルに入れるorder_idを取得したいが、取得方法がわからない
function get_order_id($db, $now_date){
  $sql = "
    SELECT
      order_id
    FROM
      order
    WHERE
      created = :now_date
    ";

    $params = array(':now_date' => $now_date);
    return execute_query($db, $sql, $params);
}

// detailsテーブルに新しい購入明細を追加
function insert_detail($db, $item_id, $amount, $price_bought){
  $now_date = date('Y-m-d H:i:s');
  $order_id = get_order_id($db, $now_date);
  $sql = "
    INSERT INTO
      details(
          order_id,
          item_id,
          amount,
          price_bought
      )
    VALUES(:order_id, :item_id, :amount, :price_bougt)
    ";

    $params = array(':order_id' => $order_id, ':item_id' => $item_id, ':amount' => $amount, ':price_bougt' => $price_bought);
    return execute_query($db, $sql, $params);
}