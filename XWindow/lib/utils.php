<?php
function exceptionHandler($e){
    return array("err"=>$e);
}

class utils {
    private $arrContextOptions;

    function __construct() {
        $this->arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
                'verify_host' => false
            ),
            'http' => array(
                'timeout' => 60, // 单位：秒
            ),
        );
    }

    function request($url, $exception="exceptionHandler"){
        set_error_handler(function($err_severity, $err_msg, $err_file, $err_line, array$err_context){
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);
        try{
            $response = file_get_contents($url, false, stream_context_create($this->arrContextOptions));
        }catch(Exception $e){
            if ($exception) return $exception($e);
            return $e;
        }
        # restore the previous error handler 还原以前的错误处理程序
        restore_error_handler();
        // return $response;
        return json_decode($response, true);
    }

    function decode($str, $prefix = "&#") {
        $str = str_replace($prefix, "", $str);
        $a = explode(";", $str);
        foreach ($a as $dec) {
            if ($dec < 128) {
                $utf .= chr($dec);
            } else if ($dec < 2048) {
                $utf .= chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            } else {
                $utf .= chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
        }
        return $utf;
    }

    function getPswd() {
        $len = 10; //密阴长度
        $i = 0;
        $ascii = [];
        $nr = [];
        while ($i < $len) {
            $air = rand(65, 90);
            $ascii[$i] = $this->decode($air);
            $nr[$i] = rand(0, 9);
            $i += 2;
        }
        $i = 0;
        $randn = [];
        while ($i < $len) {
            $randn[$i] = $ascii[$i];
            $randn[$i+1] = $nr[$i];
            $i += 2;
        }
        $rand = implode($randn);
        return $rand;
    }

    function endOf($s1, $s3) {
        return substr($s3, strpos($s3, $s1)) === $s1;
    }
    
    function startOf($s1, $s2) {
        return substr($s2, 0, strlen($s1)) === $s1;
    }
}