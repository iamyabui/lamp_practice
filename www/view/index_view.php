<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  
  <title>商品一覧</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'index.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  

  <div class="container">
    <h1>商品一覧</h1>
    <!-- messages.phpを実行、 エラーメッセージの表示-->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <div class="text-right">
      <form action="index.php" method="get">
        <select name="sort">
          <option value="new">新着順</option>
          <option value="cheap">価格が安い順</option>
          <option value="expen">価格が高い順</option>
        </select>
        <input type="submit" value="並び替え">
      </form> 
    </div>

    <div class="card-deck">
      <div class="row">
        <!-- 表示OKの商品全てについて実施 -->
      <?php foreach($items as $item){ ?>
        <div class="col-6 item">
          <div class="card h-100 text-center">
            <div class="card-header">
              <?php print(h($item['name'])); ?>
            </div>
            <figure class="card-body">
              <img class="card-img" src="<?php print(h(IMAGE_PATH . $item['image'])); ?>">
              <figcaption>
                <?php print(h(number_format($item['price']))); ?>円
                <!-- 在庫がある場合、カート追加ボタンを表示、postでindex_add_cart.phpに、item_idとtokenを渡す -->
                <?php if($item['stock'] > 0){ ?>
                  <form action="index_add_cart.php" method="post">
                    <input type="submit" value="カートに追加" class="btn btn-primary btn-block">
                    <input type="hidden" name="item_id" value="<?php print(h($item['item_id'])); ?>">
                    <input type="hidden" name="token" value="<?php print(h($token)); ?>">
                  </form>
                  <!-- 在庫がない場合、ボタンの表示はされずカートに追加できない。 -->
                <?php } else { ?>
                  <p class="text-danger">現在売り切れです。</p>
                <?php } ?>
              </figcaption>
            </figure>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
  </div>
  
</body>
</html>