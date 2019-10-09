<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controller;


use fize\pay\Alipay;

/**
 * Description of Index
 *
 * @author Administrator
 */
class ControllerFizepay
{

    /**
     * 测试支付宝移动端支付
     */
    public function actionAlipayWapPay()
    {
        $app_id = '2016091300498247';
        $merchant_private_key = 'MIIEpAIBAAKCAQEAxpAEMVZ3y2vFlf9Culk5yTajeMqiv72glAcH/zi9Ki9FaGNcHXpYSS2VxGBMvR3gQvPYPpetxVUHP6EbTaZ2J3RMXTx0U3zLk+I2i/99HUP1fFCh893U4cj0RGlrt4LfYxUSWftHq6se/XuVNjcleSOz5P2UkmgKSuIs60PVhzqwb9pI35qMX6/s6nZkVztXuVUeuufFV4UBxDCbOsNdtjxzxw1ksZEPBFNJd9oLGCu/8Lfh4iK16U80ndrXocCVAtgpCgMZHdYznPxgwIbTXdfLEwkYorFieEM8CU9a42esZzabmaYgyzQ3lWkj12Z6XUCM5I9leRJSRehL1tueYQIDAQABAoIBAHBxs/4golxPHqhv0mDnEGNTDsXjssB0aVAKn0u33N/bvyl7Qvnqg76FExAPHMXn+kzP/ACOMrjSCvXMjUKu5rA3Gtud1Z5FyJ9pdkxXlYmSJ52Lp/sK/3gRLcrkDDzy3wAkOa09MHwVvsn7RydZmV39iu09cPpr8pAvfxKH6o7V/PQtMaihLBbPdmXMFZbSoYLmk3+oE4Xy9kNghra+dDwXhaN1LVpWWBPDDVbB8YzwFHTrVqA7lRbB+EWibt6sr/HzPbA5iLx6OqX8SR5j2WCImEbMD/M+2KgptnSjlXmVrUNS/u8KlWM4P1ZVsBul+Nx6C6pvJ6HSTIsg3dpR3+UCgYEA6jZZ6EMpHX7qoU3vBoL8ZzINif1W5Rh/PxBtnAlJjAliO9f2ocQYonOvpUQSNMbWOaYI15Fq7NXduBqmdYxwRgVFRNgZsfMP5wAv0QSbVKSz+mWRKbgmZjGn44UkwoOHQD6vKVAV/2hn9q21TEaEktrDhx/hE6c5GuIcJnlaaqcCgYEA2QivMbpFRKK8B7UKWOCHh78TY5zFAjmOWbGf9IN2ayYOZxEt6ziouhUTvNtD8reMi8hNTQU/QAp2PuWQKZiTdHLH49xUGyxhzbfWz5qAAePfTpabTqR4Enyg1QpwkPVKPJHtLltneLULIWmRdclci21cY0d3KDLAAJAQpg6kt7cCgYEAgxPkAW8E5bMQETKSoWxRYlfK5/1W0mSBYoQJNBimhq8BwUg+iY470z83gCC2p77YSe84Z5zE4MNYkR6pJoRwmV99wufGiabksX2TRUF0xUxgRbTuJxEevHbx5Q2w4wPFgBkU4uQlS4ndFVtmacfVjnLMlyUqTFt8RCVjZ8zm42UCgYEA1/KdJ1SZAY8OmniXoBFgqUAFEdN17x3HtxVW+9smo7yNDh542xhYQjcgmYRuWn4pmRgnWiCDa7w9JZ4TUGAhL/fZTq72/MavVhq64XxwK/FJSw/t3lUlp/dbrD6j/IgkYposLjkCfoddWNSKyHEf5RdemuYL+PJuOuEdv6zIVocCgYAF55VKNk+TM6Y7NzRLMig3kWKouyR/bAKTFe3UDiYWWy0AGw7CupQjne4ZcRqvb0moCj7STvGKfWESrdD5CtYqfx1lzQZq/9EkKQUYbtvQ8h3m9/EE1l9eON54QlyDD+W2v/KlQgrFbnTZTGWDP7bWbRfngw9fT4lSSPMeMDewDA==';
        $alipay_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtb3U/FPAUcgNokloIVtI9QEbecCFliK2ZGiXF0V38gbK0hB2WZhAht8QyysELzzKtmw0GNiegouc0HPh2csK2y8p/xFRw1QTYFciEXhOy9DbDdWwSdiSEiPlklp2zJ4H8gv9WNXVvHQnoy17v/BU3XXj/EDZwCHb2197eD1cy2ZBLDlWALDlJMUZCQNJ9OhPvk6rdUud5vGQLgCGAbtOe0JrGXA+9QBThRaAPv7hyvCAy1Aqz0x2dYmqjAv06BwwH9a49NpBubLaXkEEDBgQme0bbp+SuJ8ucpMsU2dpal3QL4Q4fhr7KDSIxrBWaGGQWcv1OPWEetUdTiRsGLPtWQIDAQAB';

        $alipay = new Alipay($app_id, $merchant_private_key, $alipay_public_key);
        $alipay->isTest();
        $alipay->setReturnUrl('http://www.testnow.loc/Test71/?c=Fizepay&a=AlipayReturn');
        $alipay->wap('测试商品2号', '201801161739', 200.05);
    }

    /**
     * 测试支付宝移动端支付回跳
     */
    public function actionAlipayReturn()
    {
        $app_id = '2016091300498247';
        $merchant_private_key = 'MIIEpAIBAAKCAQEAxpAEMVZ3y2vFlf9Culk5yTajeMqiv72glAcH/zi9Ki9FaGNcHXpYSS2VxGBMvR3gQvPYPpetxVUHP6EbTaZ2J3RMXTx0U3zLk+I2i/99HUP1fFCh893U4cj0RGlrt4LfYxUSWftHq6se/XuVNjcleSOz5P2UkmgKSuIs60PVhzqwb9pI35qMX6/s6nZkVztXuVUeuufFV4UBxDCbOsNdtjxzxw1ksZEPBFNJd9oLGCu/8Lfh4iK16U80ndrXocCVAtgpCgMZHdYznPxgwIbTXdfLEwkYorFieEM8CU9a42esZzabmaYgyzQ3lWkj12Z6XUCM5I9leRJSRehL1tueYQIDAQABAoIBAHBxs/4golxPHqhv0mDnEGNTDsXjssB0aVAKn0u33N/bvyl7Qvnqg76FExAPHMXn+kzP/ACOMrjSCvXMjUKu5rA3Gtud1Z5FyJ9pdkxXlYmSJ52Lp/sK/3gRLcrkDDzy3wAkOa09MHwVvsn7RydZmV39iu09cPpr8pAvfxKH6o7V/PQtMaihLBbPdmXMFZbSoYLmk3+oE4Xy9kNghra+dDwXhaN1LVpWWBPDDVbB8YzwFHTrVqA7lRbB+EWibt6sr/HzPbA5iLx6OqX8SR5j2WCImEbMD/M+2KgptnSjlXmVrUNS/u8KlWM4P1ZVsBul+Nx6C6pvJ6HSTIsg3dpR3+UCgYEA6jZZ6EMpHX7qoU3vBoL8ZzINif1W5Rh/PxBtnAlJjAliO9f2ocQYonOvpUQSNMbWOaYI15Fq7NXduBqmdYxwRgVFRNgZsfMP5wAv0QSbVKSz+mWRKbgmZjGn44UkwoOHQD6vKVAV/2hn9q21TEaEktrDhx/hE6c5GuIcJnlaaqcCgYEA2QivMbpFRKK8B7UKWOCHh78TY5zFAjmOWbGf9IN2ayYOZxEt6ziouhUTvNtD8reMi8hNTQU/QAp2PuWQKZiTdHLH49xUGyxhzbfWz5qAAePfTpabTqR4Enyg1QpwkPVKPJHtLltneLULIWmRdclci21cY0d3KDLAAJAQpg6kt7cCgYEAgxPkAW8E5bMQETKSoWxRYlfK5/1W0mSBYoQJNBimhq8BwUg+iY470z83gCC2p77YSe84Z5zE4MNYkR6pJoRwmV99wufGiabksX2TRUF0xUxgRbTuJxEevHbx5Q2w4wPFgBkU4uQlS4ndFVtmacfVjnLMlyUqTFt8RCVjZ8zm42UCgYEA1/KdJ1SZAY8OmniXoBFgqUAFEdN17x3HtxVW+9smo7yNDh542xhYQjcgmYRuWn4pmRgnWiCDa7w9JZ4TUGAhL/fZTq72/MavVhq64XxwK/FJSw/t3lUlp/dbrD6j/IgkYposLjkCfoddWNSKyHEf5RdemuYL+PJuOuEdv6zIVocCgYAF55VKNk+TM6Y7NzRLMig3kWKouyR/bAKTFe3UDiYWWy0AGw7CupQjne4ZcRqvb0moCj7STvGKfWESrdD5CtYqfx1lzQZq/9EkKQUYbtvQ8h3m9/EE1l9eON54QlyDD+W2v/KlQgrFbnTZTGWDP7bWbRfngw9fT4lSSPMeMDewDA==';
        $alipay_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtb3U/FPAUcgNokloIVtI9QEbecCFliK2ZGiXF0V38gbK0hB2WZhAht8QyysELzzKtmw0GNiegouc0HPh2csK2y8p/xFRw1QTYFciEXhOy9DbDdWwSdiSEiPlklp2zJ4H8gv9WNXVvHQnoy17v/BU3XXj/EDZwCHb2197eD1cy2ZBLDlWALDlJMUZCQNJ9OhPvk6rdUud5vGQLgCGAbtOe0JrGXA+9QBThRaAPv7hyvCAy1Aqz0x2dYmqjAv06BwwH9a49NpBubLaXkEEDBgQme0bbp+SuJ8ucpMsU2dpal3QL4Q4fhr7KDSIxrBWaGGQWcv1OPWEetUdTiRsGLPtWQIDAQAB';

        $alipay = new Alipay($app_id, $merchant_private_key, $alipay_public_key);

        $arr = $_GET;

        //如果本URL本身已带了部分GET参数，则需要先剔除这些参数,否则会导致验签失败
        unset($arr['c']);
        unset($arr['a']);

        if($alipay->rsaCheck($arr)){
            echo "验证成功<br />";

            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            echo "<br />外部订单号：".$out_trade_no;

            $trade_no = htmlspecialchars($_GET['trade_no']);
            echo "<br />支付宝交易号：".$trade_no;

            $total_amount = htmlspecialchars($_GET['total_amount']);
            echo "<br />交易金额：".$total_amount;

            $seller_id = htmlspecialchars($_GET['seller_id']);
            echo "<br />收款支付宝账户ID：".$seller_id;

        }else{
            echo "验证失败";
        }
    }

    /**
     * 测试支付宝移动端通知回调
     */
    public function actionAlipayNotify()
    {
        $app_id = '2016091300498247';
        $merchant_private_key = 'MIIEpAIBAAKCAQEAxpAEMVZ3y2vFlf9Culk5yTajeMqiv72glAcH/zi9Ki9FaGNcHXpYSS2VxGBMvR3gQvPYPpetxVUHP6EbTaZ2J3RMXTx0U3zLk+I2i/99HUP1fFCh893U4cj0RGlrt4LfYxUSWftHq6se/XuVNjcleSOz5P2UkmgKSuIs60PVhzqwb9pI35qMX6/s6nZkVztXuVUeuufFV4UBxDCbOsNdtjxzxw1ksZEPBFNJd9oLGCu/8Lfh4iK16U80ndrXocCVAtgpCgMZHdYznPxgwIbTXdfLEwkYorFieEM8CU9a42esZzabmaYgyzQ3lWkj12Z6XUCM5I9leRJSRehL1tueYQIDAQABAoIBAHBxs/4golxPHqhv0mDnEGNTDsXjssB0aVAKn0u33N/bvyl7Qvnqg76FExAPHMXn+kzP/ACOMrjSCvXMjUKu5rA3Gtud1Z5FyJ9pdkxXlYmSJ52Lp/sK/3gRLcrkDDzy3wAkOa09MHwVvsn7RydZmV39iu09cPpr8pAvfxKH6o7V/PQtMaihLBbPdmXMFZbSoYLmk3+oE4Xy9kNghra+dDwXhaN1LVpWWBPDDVbB8YzwFHTrVqA7lRbB+EWibt6sr/HzPbA5iLx6OqX8SR5j2WCImEbMD/M+2KgptnSjlXmVrUNS/u8KlWM4P1ZVsBul+Nx6C6pvJ6HSTIsg3dpR3+UCgYEA6jZZ6EMpHX7qoU3vBoL8ZzINif1W5Rh/PxBtnAlJjAliO9f2ocQYonOvpUQSNMbWOaYI15Fq7NXduBqmdYxwRgVFRNgZsfMP5wAv0QSbVKSz+mWRKbgmZjGn44UkwoOHQD6vKVAV/2hn9q21TEaEktrDhx/hE6c5GuIcJnlaaqcCgYEA2QivMbpFRKK8B7UKWOCHh78TY5zFAjmOWbGf9IN2ayYOZxEt6ziouhUTvNtD8reMi8hNTQU/QAp2PuWQKZiTdHLH49xUGyxhzbfWz5qAAePfTpabTqR4Enyg1QpwkPVKPJHtLltneLULIWmRdclci21cY0d3KDLAAJAQpg6kt7cCgYEAgxPkAW8E5bMQETKSoWxRYlfK5/1W0mSBYoQJNBimhq8BwUg+iY470z83gCC2p77YSe84Z5zE4MNYkR6pJoRwmV99wufGiabksX2TRUF0xUxgRbTuJxEevHbx5Q2w4wPFgBkU4uQlS4ndFVtmacfVjnLMlyUqTFt8RCVjZ8zm42UCgYEA1/KdJ1SZAY8OmniXoBFgqUAFEdN17x3HtxVW+9smo7yNDh542xhYQjcgmYRuWn4pmRgnWiCDa7w9JZ4TUGAhL/fZTq72/MavVhq64XxwK/FJSw/t3lUlp/dbrD6j/IgkYposLjkCfoddWNSKyHEf5RdemuYL+PJuOuEdv6zIVocCgYAF55VKNk+TM6Y7NzRLMig3kWKouyR/bAKTFe3UDiYWWy0AGw7CupQjne4ZcRqvb0moCj7STvGKfWESrdD5CtYqfx1lzQZq/9EkKQUYbtvQ8h3m9/EE1l9eON54QlyDD+W2v/KlQgrFbnTZTGWDP7bWbRfngw9fT4lSSPMeMDewDA==';
        $alipay_public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtb3U/FPAUcgNokloIVtI9QEbecCFliK2ZGiXF0V38gbK0hB2WZhAht8QyysELzzKtmw0GNiegouc0HPh2csK2y8p/xFRw1QTYFciEXhOy9DbDdWwSdiSEiPlklp2zJ4H8gv9WNXVvHQnoy17v/BU3XXj/EDZwCHb2197eD1cy2ZBLDlWALDlJMUZCQNJ9OhPvk6rdUud5vGQLgCGAbtOe0JrGXA+9QBThRaAPv7hyvCAy1Aqz0x2dYmqjAv06BwwH9a49NpBubLaXkEEDBgQme0bbp+SuJ8ucpMsU2dpal3QL4Q4fhr7KDSIxrBWaGGQWcv1OPWEetUdTiRsGLPtWQIDAQAB';

        $alipay = new Alipay($app_id, $merchant_private_key, $alipay_public_key);

        $arr = $_POST;

        if($alipay->rsaCheck($arr)){
            //处理业务逻辑

            //输出相应支付宝
            echo "success";
        }else{
            //验证失败输出fail
            echo "fail";
        }
    }
}
