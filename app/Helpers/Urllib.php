<?php

namespace App\Helpers;

use Log;

Class Urllib
{

    public static function request($url, $params, $method = 'post', $cookie = '')
    {
        $url = "{$url}/CliendID/1000";
        if (strtolower($method) == 'post') {
            $params = is_string($params) && strlen(trim($params)) == 0 ? [] : $params;
            return self::post($url, $params, $cookie);
        } else {
            return self::get($url, $params, $cookie);
        }
    }

    public static function response($code = 0, $data = '')
    {

        if ($code == 0) {
            $str = json_encode(['code' => 0, 'body' => $data]);
        } else {
            $str = json_encode(['code' => $code]);
        }
        die($str);
        exit;
    }

    public static function parse($res)
    {
        if (substr($res, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $res = substr($res, 3);
        return json_decode($res, true);
    }

    /*
     * get 方式获取访问指定地址
     * @param  string url 要访问的地址
     * @param  string $headers
     * @return string curl_exec()获取的信息
     * @author andy
     **/
    public static function get($url, $headers = null)
    {
        // 初始化一个cURL会话
        $curl = curl_init($url);
        // 如果有则使用header
        if ($headers) {
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 使用自动跳转
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $cookie = '';
        if (!empty($cookie)) {
            // 包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
            curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
        }
        // 自动设置Referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        // 执行一个curl会话
        $tmp = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        log::info('             Urllib.php      $statusCode = '.$statusCode);
//        return $statusCode;
        // 关闭curl会话
        curl_close($curl);
        return [$statusCode, $tmp];
    }

    /*
     * post 方式模拟请求指定地址
     * @param  string url   请求的指定地址
     * @param  array  params 请求所带的
     * #patam  string cookie cookie存放地址
     * @return string curl_exec()获取的信息
     * @author andy
     **/
    public static function post($url, $params = [], $headers = null)
    {
        $curl = curl_init($url);
        // 如果有则使用header
        if ($headers) {
//            curl_setopt($curl, CURLOPT_HEADER,true);              // 启用这一行，服务器的headers会变成数据流跟着结果一起返回
            //   postman 可以过滤但是程序代码无法过滤
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($curl, CURLOPT_HEADER, 0);
        }
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        //模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。
        curl_setopt($curl, CURLOPT_USERAGENT, env('HTTP_USER_AGENT'));
        //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt($curl, CURLOPT_POST, 1);
        // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 使用自动跳转
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        // 自动设置Referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        // Cookie地址
        $cookie = '';
        if (!empty($cookie)) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
        }
        // 全部数据使用HTTP协议中的"POST"操作来发送。要发送文件，
        // 在文件名前面加上@前缀并使用完整路径。这个参数可以通过urlencoded后的字符串
        // 类似'para1=val1¶2=val2&...'或使用一个以字段名为键值，字段数据为值的数组
        // 如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        $result = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // 关闭curl会话
        curl_close($curl);
        return [$statusCode, $result];

    }
}