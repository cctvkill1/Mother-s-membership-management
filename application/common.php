<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(E_ERROR | E_PARSE);

// 成功返回
function success($data = [], $message = '操作成功', $code = 0)
{
    return json(['code' => $code, 'message' => $message, 'data' => $data]);
}

// 失败返回
function error($message = 'error', $data = [], $code = 10000)
{
    return success($data, $message, $code);
}

// post数据到指定url,并返回结果
function httpPost($url, $postVars, $headers = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 35);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Length:'.strlen($postVars));
        curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //echo $headers;
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);

    $r = curl_exec($ch);
    if (curl_errno($ch)) {
        echo curl_error($ch);

        return -1;
    }
    curl_close($ch);

    return $r;
}

 //post数据到指定url,并返回结果
function post($url, $data, $type = 'json')
{
    if ('json' == $type) {
        $headers = array('Content-type: application/json;charset=UTF-8', 'Accept: application/json', 'Cache-Control: no-cache', 'Pragma: no-cache');
        $data = json_encode($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($curl);
    curl_close($curl);

    return $output;
}

 //从指定url get数据
 function httpGet($url, $userpwd = null, $headers = null)
 {
     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_HEADER, false);
     curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
     if ($userpwd) {
         curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
     }
     if ($headers) {
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
     }
     $r = curl_exec($ch);
     if (curl_errno($ch)) {
         return -1;
     }
     curl_close($ch);

     return $r;
 }

 //获取远程ip地址
function getRemoteIp()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }

    return null;
}

//将时间转化为几分几秒前
function beforeTime($sTime)
{
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date('Ymd', $cTime)) - intval(date('Ymd', $sTime));
    $dYear = intval(date('Y', $cTime)) - intval(date('Y', $sTime));
    if ($dTime <= 10) {
        $dTime = '刚刚';
    } elseif ($dTime < 60) {
        $dTime = $dTime.'秒前';
    } elseif ($dTime < 3600) {
        $dTime = intval($dTime / 60).'分钟前';
    } elseif ($dTime >= 3600 && 0 == $dDay) {
        $dTime = intval($dTime / 3600).'小时前';
    } elseif (1 == $dDay) {
        $dTime = '昨天'.date('H:i', $sTime);
    } elseif (2 == $dDay) {
        $dTime = '前天'.date('H:i', $sTime);
    } elseif ($dDay < 4) {
        $dTime = $dDay.'天前';
    } elseif ($dDay >= 4 && 0 == $dYear) {
        $dTime = date('m-d H:i', $sTime);
    } else {
        $dTime = date('Y-m-d H:i', $sTime);
    }

    return $dTime;
}

//将时间转化为今天 昨天 几天前 y-m-d…
function dayTime($sTime)
{
    $cTime = time();
    $dDay = intval(date('Ymd', $cTime)) - intval(date('Ymd', $sTime));
    $dYear = intval(date('Y', $cTime)) - intval(date('Y', $sTime));
    if (1 > $dDay) {
        $dTime = '今天';
    } elseif (1 == $dDay) {
        $dTime = '昨天';
    } elseif (2 == $dDay) {
        $dTime = '2天前';
    } elseif ($dDay >= 3 && 0 == $dYear) {
        $dTime = date('m-d', $sTime);
    } else {
        $dTime = date('Y-m-d', $sTime);
    }

    return $dTime;
}

//获取显示长度,2英文算一个长度 $s必须为utf-8编码
function getShowLen($s)
{
    $al = mb_strlen($s);

    $el = 0;
    foreach (unpack('C*', $s) as $e) {
        if ($e <= 0x7F) { //<=0x7F的是英文字符
            ++$el;
        }
    }

    return ($al - $el) + intval($el / 2) + $el % 2;
}

//截取字符串
function subString($str, $len, $dot = true)
{
    $temp = getShowLen($str);
    if ($temp == $len) {
        return $str;
    } else {
        $len = $len - 1;
    }
    $chars = $str;
    $start = 0;
    $i = 0;
    do {
        if (preg_match("/[0-9a-zA-Z ,:.;?!'\"]/", $chars[$i])) {
            ++$m;
        } else {
            ++$n;
        }
        $k = $n / 3 + $m / 2;
        $l = $n / 3 + $m;
        ++$i;
    } while ($k < $len);
    $str1 = mb_substr($str, $start, $l, 'utf-8');
    if (strlen($str) == strlen($str1)) {
        return $str;
    } elseif ($dot) {
        return $str1;
    } else {
        return $str1.'...';
    }
}

//获取签名
function getSign($obj, $key = '')
{
    if (!$key) {
        $key = config('sign_key');
    }
    $sign = $obj['sign'];
    unset($obj['sign']);
    ksort($obj);
    $string = '';
    foreach ($obj as $k => $v) {
        if (is_array($v) || '[]' == $v) {
            continue;
        }
        $string .= $k.urlencode(strval($v));
    }
    $string = str_replace('%28', '(', $string);
    $string = str_replace('%29', ')', $string);
    // echo $string.$key;
    $string = md5($string.$key);
    $result = strtolower($string);

    return $result;
}

//获取token
function getToken($str, $secret = '')
{
    if (!$secret) {
        $secret = config('api_secret');
    }
    $result = hash('sha256', $str.$secret);

    return $result;
}

//获取毫秒时间戳
function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());

    return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

//验证手机号是否正确
function checkPhone($phonenumber)
{
    if (!$phonenumber) {
        return false;
    }
    if (preg_match("/^1[34578]{1}\d{9}$/", $phonenumber)) {
        return true;
    } else {
        return false;
    }
}

//下载图片
function download($url, $save_path = './images/test.jpg')
{
    $state = @file_get_contents($url, 0, null, 0, 1);
    if ($state) {
        ob_start();
        readfile($url);
        $img_ob = ob_get_contents();
        ob_end_clean();
        $fp2 = @fopen($save_path, 'a');
        fwrite($fp2, $img_ob);
        fclose($fp2);
    }
}

// 查询物流信息
function getExpressDetail($expressCode = 'shentong', $number = '3372399903807')
{
    $key = '6b6a48d8d3f44626aa47899a0eeb6fac';
    $baseUrl = 'http://www.aikuaidi.cn/rest/?key=%s&order=%s&id=%s&ord=desc&show=json';
    $service_url = sprintf($baseUrl, $key, $number, $expressCode);
    $statusArr = array('查询出错', '暂无记录', '在途中', '派送中', '已签收', '拒收', '疑难件', '退回');
    $result = httpGet($service_url);
    $result = json_decode($result);
    $result->status_str = $statusArr[$result->status];

    return $result;
}

// 生成随机字符串
function strRand($length = 6, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    if (!is_int($length) || $length < 0) {
        return false;
    }

    $string = '';
    for ($i = $length; $i > 0; --$i) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }

    return $string;
}

function calcAge($birthday)
{
    $age = 0;
    if (!empty($birthday)) {
        $age = strtotime($birthday);
        if ($age === false) {
            return 0;
        }
        
        list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
        
        list($y2, $m2, $d2) = explode("-", date("Y-m-d"), time());
        
        $age = $y2 - $y1;
        if ((int)($m2.$d2) < (int)($m1.$d1)) {
            $age -= 1;
        }
    }
    return $age;
}

function base64_upload($base64)
{
    $base64_image = str_replace(' ', '+', $base64);
    //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
        //匹配成功
        if ($result[2] == 'jpeg') {
            $image_name = uniqid().'.jpg';
        } else {
            $image_name = uniqid().'.'.$result[2];
        } 

        $image_file = ROOT_PATH ."/public/signature/{$image_name}";
        //服务器文件存储路径
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))) {
            return '/signature/'.$image_name;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
