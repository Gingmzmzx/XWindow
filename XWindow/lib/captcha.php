<?php
class captcha{
    static function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
                'verify_host' => false
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
     
        return $result;
    }
    
    static function hcaptcha($res){
        return json_decode(self::send_post(
            $GLOBALS['config']("hCaptcha_verify"),
            array(
                "response" => $res,
                "secret" => $GLOBALS['config']("hCaptcha_secret")
            )
        ), true)["success"];
    }

    static function getResult($res) {
        return self::hcaptcha($res);
    }
}