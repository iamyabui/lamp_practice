<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>購入履歴画面</h1>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php if(count($orders) > 0){ ?>
      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($orders as $order){ ?>
          <tr>
            <td><?php print(h($order['order_id']));?></td>
            <td><?php print(h($order['created'])); ?></td>
            <td><?php print(h(number_format($order['price_sum']))); ?>円</td>
            <td>
              <form method="get" action="detail.php">
                <input type="submit" value="明細" class="btn btn-secondary">
                <input type="hidden" name="order_id" value="<?php print(h($order['order_id'])); ?>">
              </form>
            </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入履歴無</p>
    <?php } ?> 
  </div>
</body>
</html>