# jzsdk 示例
```
<?php

$config = array(
        'key' => 'apiKey', //您的应用key
        'prefix' => 'Prefix', //签名前缀
        'secret' => 'Your Secret', //您的应用secret 密钥
        'url' => 'http://oa.jingzhuan.cn/server/', //API请求地址
        'debug' => false, //是否启用debug模式
        );
$client = new \jzoa\sdk\client($config);

$client->api = 'common.base.staff.getStaffById';
$client->staff_id = 1000;
$response = $client->get();

```
