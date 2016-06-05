<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {

    public function index() {
        //获得参数 signature nonce token timestamp echostr
        $nonce = $_GET['nonce'];
        $token = 'imooc';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            //第一次接入weixin api接口的时候
            echo $echostr;
            exit;
        } else {
            $this->reponseMsg();
        }
    }

    // 接收事件推送并回复
    public function reponseMsg()
    {
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {
                //回复用户消息(单图文格式)
                $arr = array(
                    array(
                        'title' => 'imooc,程序猿的天堂',
                        'description' => "欢迎来到这里学习PHP开发微信公众号",
                        'picUrl' => 'http://www.imooc.com/static/img/common/logo.png',
                        'url' => 'http://www.imooc.com',
                    )
                );
                //实例化模型
                $indexModel = new IndexModel();
                $indexModel->responseSubscribe($postObj, $arr);
            }
        }
        //用户发送tuwen2关键字的时候，回复一个单图文
        if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == '图文') {
            //一般为从数据库中查到的数组
            $arr = array(
                array(
                    'title' => 'imooc',
                    'description' => "imooc is very cool",
                    'picUrl' => 'http://www.imooc.com/static/img/common/logo.png',
                    'url' => 'http://www.imooc.com',
                ),
                array(
                    'title' => 'hao123',
                    'description' => "hao123 is very cool",
                    'picUrl' => 'https://www.baidu.com/img/bdlogo.png',
                    'url' => 'http://www.hao123.com',
                ),
                array(
                    'title' => 'qq',
                    'description' => "qq is very cool",
                    'picUrl' => 'http://www.imooc.com/static/img/common/logo.png',
                    'url' => 'http://www.qq.com',
                ),
            );
            //实例化模型
            $indexModel = new IndexModel();
            $indexModel->responseNews($postObj, $arr);
            //注意：进行多图文发送时，子图文个数不能超过10个
        } else {
            switch (trim($postObj->Content)) {
                case 1:
                    $content = '您输入的数字是1';
                    break;
                case 2:
                    $content = '您输入的数字是2';
                    break;
                case 3:
                    $content = '您输入的数字是3';
                    break;
                case 4:
                    $content = "<a href='http://www.imooc.com'>慕课</a>";
                    break;
                case 5:
                    $content = '微信SDK is very good!';
                    break;
                case 'tel':
                    $content = '15858198820';
                    break;
                case '顾秀华':
                    $content = '宝宝';
                    break;
                default:
                    $content = '没有找到相关信息！';
                    break;
            }
            //实例化模型
            $indexModel = new IndexModel();
            $indexModel->responseText($postObj, $content);
        }
    }
/*
        if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == '北京') {
            //天气查询接口
            $ch = curl_init();
            $url = 'http://apis.baidu.com/apistore/weatherservice/cityname?cityname=' . urlencode($postObj->Content);
            $header = array(
                'apikey: 7c47c790991521ad6c97c67e6ae719ad',
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch, CURLOPT_URL, $url);
            $res = curl_exec($ch);

            $arr = json_decode($res, true);
            $content = $arr['retData']['city'] . "\n" . $arr['retData']['date'] . "\n" . $arr['retData']['weather'] . "\n" . $arr['retData']['l_tmp'] . '~' . $arr['retData']['h_tmp'] . '度';

            //实例化模型
            $indexModel = new IndexModel();
            $indexModel->responseText($postObj, $content);
        }

    }




    //天气查询
    function responseWeather() {


    }

*/
    //爬取网页信息
    function http_curl(){
        //获取imooc
        //1.初始化curl
        $ch = curl_init();
        $url = 'http://www.baidu.com';
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        var_dump($output);
    }
    //获取access_token
    function getWxAccessToken(){
        //1.请求url地址
        $appid = 'wx465c33e2b3ed2b38';
        $appsecret =  '45106d6410a1c479d1878cf8cae7fde3';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        //2初始化
        $ch = curl_init();
        //3.设置参数
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch , CURLOPT_URL, $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        //4.调用接口
        $res = curl_exec($ch);
        //5.关闭curl
        curl_close( $ch );
        if( curl_errno($ch) ){
            var_dump( curl_error($ch) );
        }
        $arr = json_decode($res, true);
        var_dump( $arr );
    }
    //获取微信服务器IP地址
    function getWxServerIp(){
        $accessToken = "et3hIecWAWmDDaH48i_62ltrXqgpWherc5Qug-DBF0mWrUjC4-KAl-A3ewOg0OQUbrzA0e1oWPoeI8F3gKa3GIFnQEJqvqEs72HRM2yh58htxg0R797wNvISkOBOC70ZHOXhAEAWDM";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$accessToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $res = curl_exec($ch);
        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        $arr = json_decode($res,true);
        echo "<pre>";
        var_dump( $arr );
        echo "</pre>";
    }
}