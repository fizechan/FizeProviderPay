<?php


namespace fize\provider\pay\handler;

use Exception;
use fize\xml\LibXml;
use fize\xml\SimpleXml;
use fize\crypt\Json;
use fize\net\Http;


/**
 * 微信支付
 */
class Wechat
{

    /**
     * @var string 公众账号ID
     */
    protected $appId;

    /**
     * @var string 商户号
     */
    protected $mchId;

    /**
     * @var string 密钥KEY
     */
    protected $key;

    /**
     * 统一URL前缀
     */
    const URL_PRE = "https://api.mch.weixin.qq.com";

    /**
     * 统一下单URL
     */
    const PAY_UNIFIEDORDER_URL = '/pay/unifiedorder';

    /**
     * 查询订单URL
     */
    const PAY_ORDERQUERY_URL = '/pay/orderquery';

    /**
     * 关闭订单URL
     */
    const PAY_CLOSEORDER_URL = '/pay/closeorder';

    /**
     * 申请退款URL
     */
    const SECAPI_PAY_REFUND_URL = '/secapi/pay/refund';

    /**
     * 查询退款URL
     */
    const PAY_REFUNDQUERY_URL = '/pay/refundquery';

    /**
     * 下载对账单URL
     */
    const PAY_DOWNLOADBILL_URL = '/pay/downloadbill';

    /**
     * 交易保障,上报接口URL
     */
    const PAYITIL_REPORT_URL = '/payitil/report';

    /**
     * 拉取订单评价数据URL
     */
    const BILLCOMMENTSP_BATCHQUERYCOMMENT_URL = '/billcommentsp/batchquerycomment';

    /**
     * @var string 当前URL前缀
     */
    protected $urlPre;

    /**
     * @var array 提交的所有参数
     */
    private $params;

    /**
     * 构造
     * @param string $appid 公众账号ID
     * @param string $mch_id 商户号
     * @param string $key 商户密钥
     */
    public function __construct($appid, $mch_id, $key)
    {
        $this->urlPre = self::URL_PRE;

        $this->appId = $appid;
        $this->mchId = $mch_id;
        $this->key = $key;
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    protected function getClientIp($type = 0, $adv = true)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }

    /**
     * 清理参数，防止影响下次响应
     */
    private function clearParam()
    {
        $this->params = [];
    }

    /**
     * 设置最终提交的参数
     * @param $key
     * @param $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * 获取最终提交的参数
     * @param $key
     * @return mixed
     */
    protected function getParam($key)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            return null;
        }
    }

    /**
     * 设置是否在沙箱测试
     * @param bool $test
     */
    public function isTest($test = true)
    {
        $this->urlPre = self::URL_PRE;
        if ($test) {
            $this->urlPre .= "/sandbox";
        }
    }

    /**
     * 随机生成指定位字符串
     * @param int $length 长度
     * @return string 生成的字符串
     */
    protected function getRandomStr($length)
    {
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

    /**
     * 格式化参数格式化成url参数
     * @param array $values
     * @return string
     */
    private function toUrlParams(array $values)
    {
        $buff = "";
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v, JSON_UNESCAPED_UNICODE);
            }
            if ($k != "sign" && $v != "") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 对要提交的参数进行签名，取得签名字符串
     * @param array $values
     * @return string
     */
    protected function makeSign(array $values)
    {
        unset($values['sign']);
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->key;
        //签名步骤三：
        if ($this->params['sign_type'] == 'MD5') {
            $string = md5($string);
        } elseif ($this->params['sign_type'] == 'HMAC-SHA256') {
            $string = hash_hmac("sha256", $string, $this->key);
        }
        //签名步骤四：所有字符转为大写
        return strtoupper($string);
    }

    /**
     * 数组转XML
     * @param array $values
     * @return string
     */
    protected function toXml(array $values)
    {
        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return array
     * @throws Exception
     */
    protected function fromXml($xml)
    {
        //将XML转为array
        //禁止引用外部xml实体
        LibXml::disableEntityLoader(true);
        $values = Json::decode(Json::encode(SimpleXml::loadString($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($values === false) {
            throw new Exception("将xml转为array时发生错误！");
        }
        return $values;
    }

    /**
     * 检测签名
     * @param array $values
     * @return bool
     * @throws Exception
     */
    protected function checkSign(array $values)
    {
        $sign = $this->makeSign($values);
        if ($values['sign'] == $sign) {
            return true;
        }
        throw new Exception("签名错误！");
    }

    /**
     * 统一的HTTP POST请求
     * @param string $url 请求URL
     * @param bool $to_array 是否将预期得到的XML结果转化为数组形式，默认true
     * @return mixed 返回的结果
     * @throws Exception
     */
    protected function httpPost($url, $to_array = true)
    {
        //必需参数
        $this->params['appid'] = $this->appId;
        $this->params['mch_id'] = $this->mchId;
        $this->params['nonce_str'] = $this->getRandomStr(16);
        $this->params['sign_type'] = isset($this->params['sign_type']) ? $this->params['sign_type'] : 'MD5';

        //签名
        $this->params['sign'] = $this->makeSign($this->params);

        //转XML
        $xml = $this->toXml($this->params);

        //POST
        $response = Http::post($url, $xml);
        if ($response === false) {
            throw new Exception(Http::getLastErrMsg(), Http::getLastErrCode());
        }

        //验证
        if ($to_array) {
            $returns = $this->fromXml($response);
            if (isset($returns['sign'])) {
                $this->checkSign($returns);
            }
        } else {
            $returns = $response;
        }

        //清理
        $this->clearParam();

        return $returns;
    }

    /**
     * 统一下单
     * @param string $body 商品描述
     * @param string $out_trade_no 商户订单号
     * @param int $total_fee 标价金额，以分为单位
     * @param string $spbill_create_ip 终端IP
     * @param string $notify_url 通知地址
     * @param string $trade_type 交易类型
     * @return array
     * @throws Exception
     */
    public function unifiedorder($body, $out_trade_no, $total_fee, $spbill_create_ip, $notify_url, $trade_type)
    {
        //所需参数
        //device_info(可选参数)
        //body(调用指定)
        //detail(可选参数)
        //attach(可选参数)
        //out_trade_no(调用指定)
        //$this->params['fee_type'] = 'CNY';
        //total_fee(调用指定)
        //spbill_create_ip(必填参数，由系统取得)
        //time_start(可选参数)
        //time_expire(可选参数)
        //goods_tag(可选参数)
        //notify_url(必填参数)
        //product_id(可选参数)
        //limit_pay(可选参数)
        //openid(trade_type=JSAPI时（即公众号支付），此参数必传)
        //scene_info(可选参数)
        //id(可选参数)
        //name(可选参数)
        //area_code(可选参数)
        //address(可选参数)
        $this->params['body'] = $body;
        $this->params['out_trade_no'] = $out_trade_no;
        $this->params['total_fee'] = $total_fee;
        $this->params['spbill_create_ip'] = $spbill_create_ip;
        $this->params['notify_url'] = $notify_url;
        $this->params['trade_type'] = $trade_type;

        return $this->httpPost($this->urlPre . self::PAY_UNIFIEDORDER_URL);
    }

    /**
     * 查询订单
     * @param string $dist_code 订单号标识，可以是微信订单号或者商户订单号
     * @param bool $is_out_trade_no 指定是否为商户订单号，默认false
     * @return array
     * @throws Exception
     */
    public function orderquery($dist_code, $is_out_trade_no = false)
    {
        if ($is_out_trade_no) {
            $this->params['out_trade_no'] = $dist_code;
        } else {
            $this->params['transaction_id'] = $dist_code;
        }

        return $this->httpPost($this->urlPre . self::PAY_ORDERQUERY_URL);
    }

    /**
     * 关闭订单
     * @param string $out_trade_no 商户订单号
     * @return array
     * @throws Exception
     */
    public function closeorder($out_trade_no)
    {
        $this->params['out_trade_no'] = $out_trade_no;
        return $this->httpPost($this->urlPre . self::PAY_CLOSEORDER_URL);
    }

    /**
     * 申请退款
     * 请求需要HTTPS双向证书
     * @param string $dist_code 订单号标识，可以是微信订单号或者商户订单号
     * @param string $out_refund_no 商户退款单号
     * @param int $total_fee 订单金额
     * @param int $refund_fee 退款金额
     * @param bool $is_out_trade_no 指定$dist_code是否为商户订单号，默认false
     * @return array
     * @throws Exception
     */
    public function secapiPayRefund($dist_code, $out_refund_no, $total_fee, $refund_fee, $is_out_trade_no = false)
    {
        if ($is_out_trade_no) {
            $this->params['out_trade_no'] = $dist_code;
        } else {
            $this->params['transaction_id'] = $dist_code;
        }
        $this->params['out_refund_no'] = $out_refund_no;
        $this->params['total_fee'] = $total_fee;
        $this->params['refund_fee'] = $refund_fee;

        return $this->httpPost($this->urlPre . self::SECAPI_PAY_REFUND_URL);
    }

    /**
     * 查询退款
     * @param string $dist_code 单号[微信订单号\商户订单号\商户退款单号\微信退款单号]
     * @param string $dist_code_type 单号类型[transaction_id\out_trade_no\out_refund_no\refund_id]
     * @return array
     * @throws Exception
     */
    public function refundquery($dist_code, $dist_code_type)
    {
        $this->params[$dist_code_type] = $dist_code;
        return $this->httpPost($this->urlPre . self::PAY_REFUNDQUERY_URL);
    }

    /**
     * 下载对账单,正确时返回账单字符串
     * @param $bill_date
     * @param $bill_type
     * @param bool $gzip
     * @return string
     * @throws Exception
     */
    public function downloadbill($bill_date, $bill_type, $gzip = false)
    {
        $this->params['bill_date'] = $bill_date;
        $this->params['bill_type'] = $bill_type;
        if ($gzip) {
            $this->params['tar_type'] = 'GZIP';
        }
        $response = $this->httpPost($this->urlPre . self::PAY_DOWNLOADBILL_URL, false);
        if (stripos($response, '<xml>') === 0) {  //返回XML格式则是出现错误
            $xml = $this->fromXml($response);
            if (isset($xml['sign'])) {
                $this->checkSign($xml);
            }
            throw new Exception("出现错误：{$xml['return_msg']}");
        }
        return $response;
    }

    /**
     * 支付结果通知
     * @param callable $handle 操作方法，参数为所得的xml数组，返回为[return_code, return_msg]
     * @return string 最终输出的xml
     * @throws Exception
     */
    public function payNotify(callable $handle)
    {
        $request = file_get_contents("php://input");
        if (stripos($request, '<xml>') !== 0) {
            return $this->toXml(['return_code' => 'FAIL', 'return_msg' => 'POST参数格式错误']);
        }
        $xml = $this->fromXml($request);
        if (!isset($xml['sign'])) {
            return $this->toXml(['return_code' => 'FAIL', 'return_msg' => '缺少参数sign']);
        }
        $this->checkSign($xml);
        list($return_code, $return_msg) = $handle($xml);
        return $this->toXml(['return_code' => $return_code, 'return_msg' => $return_msg]);
    }

    /**
     * 交易保障主动上报
     * @param string $interface_url 接口URL
     * @param int $execute_time 接口耗时
     * @param string $return_code 返回状态码
     * @param string $result_code 业务结果
     * @return array
     * @throws Exception
     */
    public function payitilReport($interface_url, $execute_time, $return_code, $result_code)
    {
        $this->params['interface_url'] = $interface_url;
        $this->params['execute_time'] = $execute_time;
        $this->params['return_code'] = $return_code;
        $this->params['result_code'] = $result_code;
        $this->params['user_ip'] = $this->$this->getClientIp();
        return $this->httpPost($this->urlPre . self::PAYITIL_REPORT_URL);
    }

    /**
     * 退款结果通知
     * @param callable $handle 操作方法，参数为所得的xml数组，返回为[return_code, return_msg]
     * @return string 最终输出的xml
     * @throws Exception
     */
    public function refundNotify(callable $handle)
    {
        return $this->payNotify($handle);
    }

    /**
     * 拉取订单评价数据,正确时返回账单字符串
     * @param string $begin_time 开始时间,格式为yyyyMMddHHmmss
     * @param string $end_time 结束时间,格式为yyyyMMddHHmmss
     * @param int $offset 位移
     * @return string
     * @throws Exception
     */
    public function billcommentspBatchquerycomment($begin_time, $end_time, $offset = 0)
    {
        $this->params['begin_time'] = $begin_time;
        $this->params['end_time'] = $end_time;
        $this->params['offset'] = $offset;
        $response = $this->httpPost($this->urlPre . self::BILLCOMMENTSP_BATCHQUERYCOMMENT_URL, false);
        if (stripos($response, '<xml>') === 0) {  //返回XML格式则是出现错误
            $xml = $this->fromXml($response);
            if (isset($xml['sign'])) {
                $this->checkSign($xml);
            }
            throw new Exception("出现错误：{$xml['return_msg']}");
        }
        return $response;
    }

    /**
     * 微信JSAPI发起订单支付，并返回前端JS所需要的参数数组
     * @param string $openid 微信用户OPENID
     * @param string $body 商品描述
     * @param string $out_trade_no 商户订单号
     * @param int $total_fee 标价金额，以分为单位
     * @param string $notify_url 通知地址
     * @return array 返回前端使用的JSAPI配置数组
     * @throws Exception
     */
    public function getBrandWCPayRequest($openid, $body, $out_trade_no, $total_fee, $notify_url)
    {
        $this->setParam('device_info', 'WEB');
        $this->setParam('openid', $openid);

        $result = $this->unifiedorder($body, $out_trade_no, $total_fee, $this->getClientIp(), $notify_url, 'JSAPI');

        if (!isset($result['prepay_id'])) {
            throw new Exception("微信未返回参数prepay_id");
        }

        $params = [
            'appId'     => $this->appId,
            'timeStamp' => time(),
            'nonceStr'  => $this->getRandomStr(16),
            'package'   => "prepay_id={$result['prepay_id']}",
            'signType'  => $this->getParam('sign_type'),
        ];

        $sign = $this->makeSign($params);
        $params['paySign'] = $sign;

        return $params;
    }

    /**
     * 通用H5发起订单支付，返回一个跳转URL
     * @param string $body 商品描述
     * @param string $out_trade_no 商户订单号
     * @param int $total_fee 标价金额，以分为单位
     * @param string $notify_url 通知地址
     * @return string
     * @throws Exception
     */
    public function getH5MWebUrl($body, $out_trade_no, $total_fee, $notify_url)
    {
        $this->setParam('device_info', 'WEB');
        $result = $this->unifiedorder($body, $out_trade_no, $total_fee, $this->getClientIp(), $notify_url, 'MWEB');
        //var_dump($result);
        //die();
        if (!isset($result['mweb_url'])) {
            throw new Exception("微信未返回参数mweb_url");
        }
        return $result['mweb_url'];
    }
}
