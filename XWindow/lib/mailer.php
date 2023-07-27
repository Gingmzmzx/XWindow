<?php
class mailer {
    private $mail;

    static function load($subject=null, $content=null, $typeContent=null) {
        if (!class_exists('PHPMailer\PHPMailer\Exception')) {
            require 'PHPMailer/src/Exception.php';
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';
        }
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->CharSet = $GLOBALS['config']("mailer_charset"); //设定邮件编码
        $mail->SMTPDebug = $GLOBALS['config']("mailer_smtpdebug"); // 调试模式输出
        $mail->isSMTP(); // 使用SMTP
        $mail->Timeout = $GLOBALS['config']("mailer_timeout");
        $mail->Host = $GLOBALS['config']("mailer_host"); // SMTP服务器
        $mail->SMTPAuth = $GLOBALS['config']("mailer_smtpauth"); // 允许 SMTP 认证
        $mail->Username = $GLOBALS['config']("mailer_username"); // SMTP 用户名  即邮箱的用户名
        $mail->Password = $GLOBALS['config']("mailer_password"); // SMTP 密码
        $mail->SMTPSecure = $GLOBALS['config']("mailer_smtpsecure"); // 允许 TLS 或者ssl协议
        $mail->Port = $GLOBALS['config']("mailer_port"); // 服务器端口 25 或者465 具体要看邮箱服务器支持
        $mail->SMTPOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
                'verify_host' => false
            ),
        );
        $mail->setFrom($GLOBALS['config']("mailer_sendermail"), $GLOBALS['config']("mailer_sendername")); //发件人
        $mail->addReplyTo($GLOBALS['config']("mailer_replymail"), $GLOBALS['config']("mailer_replyname")); //回复的时候回复给哪个邮箱
        //Content
        $mail->isHTML($GLOBALS['config']("mailer_ishtml")); // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        if ($subject) $mail->Subject = $subject;
        if ($content) {
            $user = $_COOKIE['user'];
            if ($user == null) $user = '用户';
            $website = $GLOBALS['config']("site_domain");
            $sitename = $GLOBALS['config']("site_name");
            $mailContent = "<!DOCTYPE html>
<html lang='zh-CN'>
<head>
    <meta charset='UTF-8'>
    <TITLE>{$sitename} - Mail</TITLE>
</HEAD>
<BODY style='text-align:left;padding:0 0 10px 0;margin:0;'>
    <PRE style='font:normal 12px arial;border:1px solid #e6e7e9;margin:0 10px 10px 10px;padding:12px;'>
        <div>
            <div style='background: white;width: 95%; max-width: 600px;margin: auto auto; border-radius: 5px;border:orange 1px solid;overflow: auto;-webkit-box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.12);box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.18);'>
                <div style='padding: 5px 20px;'>
                    <p style='position: relative;color: white;float: left;z-index: 999;background: orange;padding: 5px 30px;margin: -25px auto 0;box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.30);'>
                        Dear {$user},
                    </p><br />
                    <div>
                        {$content}
                    </div><br/>
                    <p style='font-size: 12px;text-align: center;color: #999;'>本邮件为<a href='{$website}' style='text-decoration: none;color: orange;' target='_blank'>{$sitename}</a>自动发出，请勿直接回复<br  /> ©2023 {$sitename}</p>
                </div>
            </div>
        </div>
    </PRE>
</BODY>
</HTML>";
            $mail->Body = $mailContent;
        }
        if ($typeContent) $mail->AltBody = $typeContent;
        return $mail;
    }
}