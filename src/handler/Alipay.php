<?php


namespace fize\provider\pay\handler;

use fize\crypt\Json;
use fize\crypt\Base64;
use fize\security\OpenSSL;

/**
 * 支付宝
 */
class Alipay
{
    /**
     * 支付宝网关
     */
    const GATEWAY_URL = "https://openapi.alipay.com/gateway.do";

    /**
     * 测试用沙箱支付宝网关
     */
    const DEV_GATEWAY_URL = "https://openapi.alipaydev.com/gateway.do";

    /**
     * @var string 当前使用的网关
     */
    private $gatewayUrl;

    /**
     * 所有提交的参数
     * @var array
     */
    private $params = [];

    /**
     * 商户私钥
     * @var
     */
    private $merchantPrivateKey;

    /*
     * 支付宝公钥
     */
    private $alipayPublicKey;

    /**
     * 业务参数
     * @var array
     */
    private $bizContent;

    /**
     * Alipay constructor.
     * @param string $app_id
     * @param string $merchant_private_key
     * @param string $alipay_public_key
     */
    public function __construct($app_id, $merchant_private_key, $alipay_public_key)
    {
        $this->gatewayUrl = self::GATEWAY_URL;

        $this->params['app_id'] = $app_id;
        $this->params['method'] = 'alipay.trade.wap.pay';
        $this->params['format'] = 'JSON';
        //额外设置return_url
        $this->params['charset'] = 'utf-8';
        $this->params['sign_type'] = 'RSA2';
        //计算所得sign
        $this->params['timestamp'] = date('Y-m-d H:i:s');
        $this->params['version'] = '1.0';
        //额外设置notify_url
        //代码设置biz_content
        $this->merchantPrivateKey = $merchant_private_key;
        $this->alipayPublicKey = $alipay_public_key;
    }

    /**
     * 设置是否在沙箱测试
     * @param bool $test
     */
    public function isTest($test = true)
    {
        if ($test) {
            $this->gatewayUrl = self::DEV_GATEWAY_URL;
        } else {
            $this->gatewayUrl = self::GATEWAY_URL;
        }
    }

    /**
     * 设置会跳URL
     * @param string $return_url
     */
    public function setReturnUrl($return_url)
    {
        $this->params['return_url'] = $return_url;
    }

    /**
     * 设置响应编码
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->params['charset'] = $charset;
    }

    /**
     * 设置使用的签名算法类型
     * @param string $sign_type
     */
    public function setSignType($sign_type)
    {
        $this->params['sign_type'] = $sign_type;
    }

    /**
     * 设置通知回调URL
     * @param string $notify_url
     */
    public function setNotifyUrl($notify_url)
    {
        $this->params['notify_url'] = $notify_url;
    }

    /**
     * 设置业务请求参数
     * @param string $key
     * @param mixed $val
     */
    public function setBizContent($key, $val)
    {
        $this->bizContent[$key] = $val;
    }

    /**
     * 设置业务扩展参数
     * @param string $key
     * @param mixed $val
     */
    public function setExtendParams($key, $val)
    {
        $this->bizContent['extend_params'][$key] = $val;
    }

    /**
     * 外部指定买家
     * @param string $key
     * @param mixed $val
     */
    public function setExtUserInfo($key, $val)
    {
        $this->bizContent['ext_user_info'][$key] = $val;
    }

    /**
     * 校验$value是否非空
     * @param $value
     * @return bool
     */
    protected function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }
        if ($value === null) {
            return true;
        }
        if (trim($value) === "") {
            return true;
        }
        return false;
    }

    /**
     * 根据传递的参数返回待签名内容
     * @param array $params
     * @return string
     */
    protected function getSignContent(array $params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {

            if (is_array($v)) {  //数组作为JSON字符串解析
                $v = Json::encode($v, JSON_UNESCAPED_UNICODE);
            }

            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                if ($i == 0) {
                    $stringToBeSigned .= "{$k}={$v}";
                } else {
                    $stringToBeSigned .= "&{$k}={$v}";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 取得要传递的参数组成的字符串
     * @param array $params
     * @return string
     */
    protected function getContent(array $params)
    {
        $i = 0;
        $stringToBeSigned = "";
        foreach ($params as $k => $v) {

            if (is_array($v)) {  //数组作为JSON字符串解析
                $v = json_encode($v, JSON_UNESCAPED_UNICODE);
            }

            if ($i == 0) {
                $stringToBeSigned .= "{$k}={$v}";
            } else {
                $stringToBeSigned .= "&{$k}={$v}";
            }
            $i++;
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 签名
     * @param $data
     * @param string $signType
     * @return string
     */
    protected function sign($data, $signType = "RSA2")
    {
        $priKey = $this->merchantPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        $openssl = new OpenSSL();
        $openssl->setPrivateKey($res, false);
        if ("RSA2" == $signType) {
            $sign = $openssl->sign($data, OPENSSL_ALGO_SHA256);
        } else {
            $sign = $openssl->sign($data);
        }
        $sign = Base64::encode($sign);
        return $sign;
    }

    /**
     * 验签
     * @param string $data 待验签字符串
     * @param string $sign 签名
     * @param string $signType 签名类型
     * @return bool
     */
    public function verify($data, $sign, $signType = 'RSA2')
    {
        $pubKey = $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }

    /**
     * 根据返回的参数检验是否签名正确
     * @param $params
     * @param string $signType
     * @return bool
     */
    public function rsaCheck($params, $signType = 'RSA2')
    {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param array $para_temp 请求参数数组
     * @return string 提交表单HTML文本
     */
    protected function buildRequestForm(array $para_temp)
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $this->gatewayUrl . "?charset=" . trim($this->params['charset']) . "' method='POST'>";
        foreach ($para_temp as $key => $val) {

            if (is_array($val)) {  //数组作为JSON字符串解析
                $val = Json::encode($val, JSON_UNESCAPED_UNICODE);
            }

            if (false === $this->checkEmpty($val)) {
                //$val = $this->characet($val, $this->postCharset);
                $val = str_replace("'", "&apos;", $val);
                //$val = str_replace("\"","&quot;",$val);
                $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }

    /**
     * 移动端发起在线支付请求
     * @param $subject
     * @param $out_trade_no
     * @param $total_amount
     */
    public function wap($subject, $out_trade_no, $total_amount)
    {
        $this->setBizContent('subject', $subject);
        $this->setBizContent('out_trade_no', $out_trade_no);
        $this->setBizContent('total_amount', $total_amount);
        $this->setBizContent('product_code', 'QUICK_WAP_WAY');
        $this->params['biz_content'] = $this->bizContent;
        unset($this->params['sign']);
        //echo "请求参数：\r\n";
        //echo http_build_query($this->params);
        //echo $this->getContent($this->params);
        //echo "\r\n";
        //echo "待签名内容：\r\n";
        //echo $this->getSignContent($this->params);
        $sign = $this->sign($this->getSignContent($this->params), $this->params['sign_type']);
        //echo "\r\n";
        //echo "签名：\r\n";
        //echo $sign;
        //echo "\r\n";
        $this->params['sign'] = $sign;
        $html = $this->buildRequestForm($this->params);
        echo $html;
    }

    public function tuikuan()
    {

    }
}
