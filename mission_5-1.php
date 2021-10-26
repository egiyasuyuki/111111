<?php

// データベースへ接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

// データベース内にテーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tbtest"
  . " ("
  . "num char(32),"
  . "name char(32),"
  . "comment TEXT,"
  . "time TEXT,"
  . "password char(32)"
  . ");";
$stmt = $pdo->query($sql);

$editName = "";
$editComment = "";
$editNumber = "";
$editPassword = "";

// 投稿について

if (!empty($_POST["comment"]) && !empty($_POST["name"]) && !empty($_POST["password"])) {


  // 新規投稿
  if (empty($_POST["editNumber"])) {
    $number = 0;
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
      if (!empty($row[0])) {
        $number = $row[0];
      } else {
        $number = 0;
      }
    }

    // データベースへの書き込み
    $sql = $pdo->prepare("INSERT INTO tbtest (num, name, comment, time, password) VALUES (:num, :name, :comment, :time, :password)");
    $sql->bindParam(':num', $num, PDO::PARAM_STR);
    $sql->bindParam(':name', $name, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':time', $time, PDO::PARAM_STR);
    $sql->bindParam(':password', $password, PDO::PARAM_STR);

    $num = $number + 1;
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $time = date("Y/m/d H:i:s");
    $password = $_POST["password"];
    $sql->execute();
  } else {

    // 編集投稿

    $num = $_POST["editNumber"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $time = date("Y/m/d H:i:s");
    $password = $_POST["password"];

    // データレコードを編集
    $sql = 'UPDATE tbtest SET name=:name, comment=:comment, time=:time, password=:password WHERE num=:num';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':num', $num, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':time', $time, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    // エラーが出ないように最後に空白を渡す
    $editNumber = "";
    $editComment = "";
    $editName = "";
    $editPassword = "";
  }
}

// 削除について

if (!empty($_POST["delete"]) && !empty($_POST["password"])) {
  $delete = $_POST["delete"];
  $password = $_POST["password"];

  // データベースからデータレコードを抽出
  $num = $delete;
  $sql = 'SELECT * FROM tbtest WHERE num=:num ';
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':num', $num, PDO::PARAM_INT);
  $stmt->execute();
  $results = $stmt->fetchAll();
  foreach ($results as $row) {
    //パスワードの判定
    if ($row['password'] == $password) {
      $sql = 'delete from tbtest where num=:num';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':num', $num, PDO::PARAM_STR);
      $stmt->execute();
    }
  }
}

// 編集について

if (!empty($_POST["edit"])) {
  $edit = $_POST["edit"];
  $password = $_POST["password"];

  // データレコードを抽出
  $sql = 'SELECT * FROM tbtest';
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row) {
    if ($row['num'] == $edit && $row['password'] == $password) {
      $editNumber = $row['num'];
      $editName = $row['name'];
      $editComment = $row['comment'];
      $editPassword = $row['password'];
    }
  }
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
</head>

<body>

  <form method="post" action="">
    <p><span>名前：</span><input type="text" name="name" value="<?php echo $editName; ?>"></p>
    <p><span>コメント：</span><input type="text" name="comment" value="<?php echo $editComment; ?>"></p>
    <p><span>パスワード：</span><input type="password" name="password" value="<?php echo $editPassword; ?>"></p>
    <p><input type="hidden" name="editNumber" value="<?php echo $editNumber; ?>" placeholder="数字です。"></p>
    <p><input type="submit" value="送信"></p>
  </form>

  <hr>

  <form method="post" action="">
    <p><span>削除番号入力：</span><input type="number" name="delete"></p>
    <p><span>パスワード：</span><input type="password" name="password"></p>
    <p><input type="submit" value="削除"></p>
  </form>

  <hr>

  <form method="post" action="">
    <p>指定した投稿番号の投稿を編集</p>
    <p><span>編集番号入力：</span><input type="number" name="edit"></p>
    <p><span>パスワード：</span><input type="password" name="password"></p>
    <p><input type="submit" value="編集"></p>
  </form>


  <?php

  // ブラウザへの表示
  $sql = 'SELECT * FROM tbtest';
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row) {
    echo $row['num'] . ',';
    echo $row['name'] . ',';
    echo $row['comment'] . ',';
    echo $row['time'] . ',';
    echo $row['password'] . '<br>';
  }

  $sql = 'SHOW TABLES';
  $result = $pdo->query($sql);
  foreach ($result as $row) {
    echo $row[0];
    echo '<br>';
  }
  echo "<hr>";
  ?>
</body>

</html>