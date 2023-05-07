<?php

htmlspecialchars("'",ENT_QUOTES,'UTF-8');
htmlspecialchars('<',ENT_QUOTES,'UTF-8');
htmlspecialchars('>',ENT_QUOTES,'UTF-8');

date_default_timezone_set("Asia/Tokyo");

$comment_array = array();
$pdo = null;
$stmt = null;

//DB接続
try {
    $pdo = new PDO('mysql:host=localhost;dbname=xs354585_bbsphp',"xs354585_user01","rxsrAhHG69XGz.a");    
}catch (PDOException $e) {
    echo $e->getMessage();
};

//フォームを打ち込んだとき

//名前・コメントを入力しなかったとき
if (!empty($_POST["submitButton"])) {

    if (empty($_POST["username"]) and empty($_POST["comment"]))  {
        echo "名前とコメントを入力してください";
    }
    else if (empty($_POST["username"])) {
        echo "名前を入力してください";
    } else if (empty($_POST["comment"])) {
        echo "コメントを入力してください";
    }

    // 日付の表示形式
    $postDate = date("Y-m-d H:i:s");


    if(!empty($_POST["username"]) and !empty($_POST["comment"])) {
        try {
            // エスケープ処理
            $comment = htmlspecialchars($_POST['comment'],ENT_QUOTES,'UTF-8');
            $username = htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8');

            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`username`, `comment`, `postDate`) VALUES (:username, :comment, :postDate);");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':postDate',$postDate, PDO::PARAM_STR);
    
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }
}


// DBからコメントデータを取得
$sql = "SELECT `id`,`username`,`comment`,`postDate`FROM`bbs-table`";
$comment_array = $pdo->query($sql);


//DB接続を閉じる
$pdo = null;



?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="title">PHPで掲示板アプリ</h1>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach($comment_array as $comment): ?>
            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <span>名前：</span>
                        <p class="username"></p><?php echo $comment["username"]; ?></p>
                        <time>:<?php echo $comment["postDate"]; ?></time>
                    </div>
                    <p class="comment"><?php echo $comment["comment"]; ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </section>
        <form class="formWrapper" method="POST">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="">名前：</label>
                <input type="text" name="username" autocomplete="off">
            </div>
            <div>
                <textarea class="commentTextArea" name="comment"></textarea>
            </div>
        </form>
    </div>
</body>
</html>