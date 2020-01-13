<?php
require_once 'functions.php';
require_once 'db.php';

// *****DB利用*****
// $item_idに対する商品情報をitemsテーブルから取得 
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = :item_id
  ";

  $params = array(':item_id' => $item_id);
  return fetch_query($db, $sql, $params);
}

// itemsテーブルから商品情報を取得、デフォルトで$is_openにfalseを渡す
function get_items($db, $is_open = false){
  //$is_openがデフォルト値と同様falseの場合、全てのitem情報を取得する。
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  //$is_openがtrueの場合、表示OK（status=1)のitem情報のみ取得する。
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

// itemsテーブルから全ての商品情報を取得
function get_all_items($db){
  return get_items($db);
}

// 表示OK（status=1）の商品情報のみ取得
function get_open_items($db){
  //$is_openにtrueを代入して、get_items関数を実行
  return get_items($db, true);
}

// 入力フォームで入力した情報が正常に登録できた場合、trueを返す
function regist_item($db, $name, $price, $stock, $status, $image){
  // 画像の型がjpeg、pngでない場合はエラーメッセージ出力し、空文字をfilenameに代入
  $filename = get_upload_filename($image);
  // 入力フォームで入力された値が正しいかチェック、一つでもNGの場合falseを返す
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  // 入力フォームで入力した情報をDBに挿入、画像の保存を実施、trueかfalseを返す
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

// 入力フォームで入力した情報をDBに挿入、画像の保存を実施、trueかfalseを返す
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  // トランザクション開始
  $db->beginTransaction();
  // 新規商品をitemsテーブルに追加、新規ファイルをassets/imagesフォルダに保存、成功したらtrueを返す
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  // 上記失敗した場合ロールバック、falseを返す
  $db->rollback();
  return false;
  
}
// itemsテーブルに新しい商品情報を追加
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(:name, :price, :stock, :filename, :status_value);
  ";

  $params = array(':name' => $name, ':price' => $price, ':stock' => $stock, ':filename' => $filename, '$status_value' => $status_value);
  return execute_query($db, $sql, $params);
}
// 商品表示ステータスの変更
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = :status
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  $params = array(':status' => $status, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

// 該当商品について、itemsテーブルの在庫数を更新
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = :stock
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  $params = array(':stock' => $stock, ':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}

// $item_idの商品情報を削除、該当画像ファイルを削除、成功したらtrueを、失敗したらfalseを返す
function destroy_item($db, $item_id){
  // $item_idの商品情報を取得
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  // トランザクション開始
  $db->beginTransaction();
  // 商品の削除と、該当ファイルの削除を実施
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}
// 対象商品を削除（itemsテーブルから$item_idの行を削除）
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = :item_id
    LIMIT 1
  ";
  
  $params = array(':item_id' => $item_id);
  return execute_query($db, $sql, $params);
}


// 非DB
// 商品表示ステータスがopenの場合、trueを返す
function is_open($item){
  return $item['status'] === 1;
}

// フォームで入力された値が正しいかチェック、返り値はtrueかfalse
function validate_item($name, $price, $stock, $filename, $status){
  // 文字列の長さが指定の範囲内であるかチェック、返り値はfalseかtrue
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);
  // $name, $price, $stock, $filename, $statusの値が全て正しく入っている場合は、trueを返す
  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// $nameの文字列の長さが、指定範囲外であればエラーメッセージを出力しfalseを返す。範囲内であればTrueを返す。
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}
// $priceについて、バリデーションチェック、0以上の整数の場合true、そうでない場合はfalseを返す。
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
// $stockについて、バリデーションチェック、0以上の整数の場合true、そうでない場合はfalseを返す。
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}
// $filenameに値が入っている場合はtrue、空の場合はfalseを返す。
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}
// $statusに値が入っている場合はtrue、空の場合はfalseを返す。
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}