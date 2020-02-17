<?php

namespace handler;

use fize\net\Http;
use fize\provider\pay\handler\Wechat;
use PHPUnit\Framework\TestCase;

class TestWechat extends TestCase
{

    public function testSecapiPayRefund()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        //$pay->isTest();
        $transaction_id = 'HZK201801191706';
        $out_refund_no = 'HZK201801191706';
        $total_fee = 30000;
        $refund_fee = 10000;
        $result = $pay->secapiPayRefund($transaction_id, $out_refund_no, $total_fee, $refund_fee);
        var_dump($result);
    }

    public function testPayitilReport()
    {

    }

    public function testIsTest()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        $pay->isTest();
        $pay->setParam('device_info', 'WEB');
        $result = $pay->unifiedorder('测试商品1号', '201801191706', 100, '127.0.0.1', 'http://www.g-medal.com', 'JSAPI');
        var_dump($result);
    }

    public function test__construct()
    {

    }

    public function testCloseorder()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        //$pay->isTest();
        $result = $pay->closeorder('HZK201801191706');
        var_dump($result);
    }

    public function testUnifiedorder()
    {

    }

    public function testPayNotify()
    {
        header("Content-type: text/xml");
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        $xml = $pay->payNotify(function(array $param){
            //业务处理
            return ['SUCCESS', ''];
        });
        echo $xml;
    }

    public function testSetParam()
    {

    }

    public function testGetBrandWCPayRequest()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $jsapi = new JsApi($appid, $mch_id, $key);
        $jsapi->isTest();
        $openid = '123456789';
        $body = '帝豪租车款';
        $out_trade_no = 'HZK201801191706';
        $total_fee = 30000;
        //使用try catch语句处理
        try{
            $result = $jsapi->getBrandWCPayRequest($openid, $body, $out_trade_no, $total_fee);
            var_dump($result);
        }catch (Exception $e){
            echo '发生错误啦！！！';
            var_dump($e);
        }
    }

    public function testDownloadbill()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        //$pay->isTest();
        $result = $pay->downloadbill('20140603', 'ALL');
        echo $result;
        //var_dump($result);
    }

    public function testOrderquery()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        //$pay->isTest();
        $pay->setParam('device_info', 'WEB');
        $result = $pay->orderquery('HZK201801191706');
        var_dump($result);
    }

    public function testRefundquery()
    {
        $appid = 'wxd930ea5d5a258f4f';
        $mch_id = '10000100';
        $key = 'test_key_123456';
        $pay = new Wechat($appid, $mch_id, $key);
        //$pay->isTest();
        $result = $pay->refundquery('HZK201801191706', 'transaction_id');
        var_dump($result);
    }

    public function testBillcommentspBatchquerycomment()
    {

    }

    public function testRefundNotify()
    {
        $xml = "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[sign error]]></return_msg><error_code><![CDATA[20001]]></error_code></xml>";
        $url = "http://www.testnow.loc:81/Test71/?c=FizePayWechat&a=PayNotify";
        $response = Http::post($url, $xml);
        //var_dump($http);
        echo $response;
    }

    public function testGetH5MWebUrl()
    {

    }
}
