<?php
/*
 * SDK客户端请求类
 * @author 罗会铸 yeyupl@qq.com
 */

namespace jzsdk;

class client {

    private $_http;
    private $_config;
    private $_data;
    private $_params = array();
    private $_url;
    private $_ts;

    /**
     * client constructor.
     * @param array $config
     * $config = array(
     *   'key' => 'apiKey', //您的应用key
     *   'prefix' => 'Prefix', //签名前缀
     *   'secret' => 'Your Secret', //您的应用secret 密钥
     *   'url' => 'http://oa.jingzhuan.cn/server/', //API请求地址
     *   'debug' => false, //是否启用debug模式
     *  );
     */
    public function __construct(array $config) {
        $this->_config = $config;
        $this->_http = new \GuzzleHttp\Client();
    }

    /**
     * 获取参数值
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->_params[$name];
    }

    /**
     * 参数赋值
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        if ($this->_data) {
            $this->_params = array();
            $this->_data = null;
        }
        $this->_params[$name] = trim($value);
    }

    /**
     * 删除参数
     * @param $name
     */
    public function __unset($name) {
        unset($this->_params[$name]);
    }


    /**
     * 取得当前请求参数
     * @return array
     */
    public function getParams() {
        return $this->array_trim($this->_params);
    }


    /**
     * 批量设置请求参数 必须为关联数组
     * @param $params
     */
    public function setParams($params) {
        if (is_array($params)) {
            $this->_params = array_merge($this->_params, $params);
        }
    }


    /**
     * 获得当前请求的url (用于调试，适用于用GET方法的请求)
     * @return mixed
     */
    public function getUrl() {
        return $this->_url;
    }


    /**
     * 发送请求
     * @param string $mode
     * @return string
     */
    private function send($mode = 'get') {
        $this->_ts = time();
        $sysParams = array(
            'key' => $this->_config['key'],
            'sign' => $this->createSign(),
            'ts' => $this->_ts,
            'debug' => $this->_config['debug']
        );
        $this->_params = array_merge($this->getParams(), $sysParams);
        $this->_url = $this->_config['url'];

        if (strtolower($mode) == 'get') {
            $this->_url .= '?' . http_build_query($this->_params);
            $response = $this->_http->get($this->_url);
        } else {
            $response = $this->_http->post($this->_url, array('form_params' => $this->_params));
        }
        if ($response->getStatusCode() == 200) {
            $this->_data = $response->getBody()->getContents();
        }
        return $this->_data;
    }


    /**
     * 对请求签名
     * @return string
     */
    private function createSign() {
        $params = array();
        foreach ($this->getParams() as $key => $val) {
            if (!in_array($key, array('key', 'api', 'sign', 'ts', 'debug'))) {
                $params[$key] = $val;
            }
        }
        //参数排序 按key字母升序排序
        $params = $this->deepKsort($params);

        $paramsStr = md5(http_build_query($params));

        return md5($this->_config['prefix'] . $this->_config['key'] . $this->_config['secret'] . $this->_params['api'] . $this->_ts . $paramsStr);
    }


    /**
     * 多维按key排序
     * @param $array
     * @return mixed
     */
    private function deepKsort($array) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = deepKsort($v);
            }
        }
        ksort($array);
        return $array;
    }

    /**
     * 用GET方式请求
     * @return string
     */
    public function get() {
        return $this->send('get');
    }


    /**
     * 用POST方式请求
     * @return string
     */
    public function post() {
        return $this->send('post');
    }


    /**
     * 数组去空格
     * @param $array
     * @return array
     */
    private function array_trim($array) {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = trim($v);
            }
        }
        return $array;
    }
}
