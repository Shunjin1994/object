<?php

// if(!empty($_POST)){
//     //DBへの接続準備
//     $dsn = 'mysql:dbname=js_advance;host=localhost;charset=utf8';
//     $user = 'root';
//     $password = 'root';
//     $options = array(
//         // SQL実行失敗時に例外をスロー
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         // デフォルトフェッチモードを連想配列形式に設定
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//         // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
//         // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
//         PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
//     );

//     // PDOオブジェクト生成（DBへ接続）
//     $dbh = new PDO($dsn, $user, $password, $options);

//     //SQL文（クエリー作成）
//     $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email');

//     //プレースホルダに値をセットし、SQL文を実行
//     $stmt->execute(array(':email' => $_POST['email']));

    $result = 0;

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //結果が0でない場合
    if(!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD', $result)){
        echo json_encode(array(
            'errorFlg' => false,
            'msg' => 'メールアドレスの形式ではありません。'
        ));
    }else{
        echo json_encode(array(
            'errorFlg' => true,
        ));
    }
    exit();
// }