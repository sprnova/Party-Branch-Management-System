<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

<form method="post" action="wikiSearch_list.php">
    <input name="key" type="text">
    <input type="submit">
</form>


<?php
$GLOBALS['word'] = $_POST["key"];
?>

<?php

/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function send_post($url, $post_data) {

    $postData = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postData,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options); // 用于请求方式、头信息、body、超时的设置
    $result = file_get_contents($url, false, $context); // 把整个文件的内容读入一个字符串

    return $result;
}

//使用方法
$post_data = array(
    'action' => 'opensearch',
    'search' => $word,
    'limit' => 10,
    'format' => 'json'
);
//$GLOBALS['infoSearch'] = json_decode(send_post('https://en.wikipedia.org/w/api.php', $post_data),false);
//echo (send_post('https://en.wikipedia.org/w/api.php', $post_data));
//echo ($infoSearch[1][0]);
?>
<div>
    <p>What you've just searched is: <?php echo($word) ?></p>
</div>
<div>
    <?php
    for($i = 0; $i < $infoSearch[1].count(); $i++) {
        echo("<p>[1][");
        echo($i);
        echo("]</p>");
    }
    ?>
</div>
</body>