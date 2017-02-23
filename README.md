# jzsdk 示例
```
<?php

$config = array(
        'key' => 'apiKey', //您的应用key
        'prefix' => 'Prefix', //签名前缀
        'secret' => 'Your Secret', //您的应用secret 密钥
        'url' => 'http://www.server.com', //API请求地址
        'debug' => false, //是否启用debug模式
        );
$client = new \jzsdk\client($config);

//单个参数赋值
$client->api = 'test.api';
$client->user_id = 1000;

//批量赋值
$client->setParams(array('name'=>'yeyupl','email'=>'yeyupl@qq.com'));

//GET请求
$response = $client->get();

//POST请求
$response = $client->post();

//获得组装GET参数后的URL
echo $client->getUrl();

//获得所有参数
print_r($client->getParams());

```
