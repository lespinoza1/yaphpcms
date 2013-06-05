<?php
/**
 * 邮件发送类
 *
 * @file            Mailer.class.php
 * @package         Yap\Module\Admin
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-06-05 17:19:40
 * @lastmodify      $Date$ $Author$
 */

define('PHPMAILER_PATH'         , Yaf_Loader::getInstance()->getLibraryPath(true) . 'PHPMailer/');
define('PHPMAILER_SMTP_PATH'    , PHPMAILER_PATH);
require(PHPMAILER_PATH . 'class.phpmailer.php');

class Mailer extends PHPMailer {
    /**
     * @var string $CharSet 邮件内容编码，默认utf-8
     */
    public $CharSet = 'utf-8';

    /**
     * @var string $ContentType 邮件内容mime类型，默认text/html
     */
    public $ContentType = 'text/html';

    /**
     * @var string $PluginDir smtp类所在路径，默认PHPMAILER_SMTP_PATH
     */
    public $PluginDir = PHPMAILER_SMTP_PATH;

    /**
     * @var string 发送邮件方法。默认smtp，可用mail, sendmail, smtp
     */
    public $Mailer = 'smtp';

    /**
     * @var bool true smtp发送需要验证用户。默认true
     */
    public $SMTPAuth = true;

    /**
     * 构造函数
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-05 17:42:41
     *
     * @param bool $exceptions true可捕获发送异常。默认true
     *
     * @return void 无返回值
     */
    public function __construct($exceptions = true) {
        parent::__construct($exceptions);
        $this->SetLanguage('zh_cn', PHPMAILER_PATH . 'language/');
    }
}
/*
$mail = new Mailer();
$mail->Host       = "smtp.163.com";
$mail->Port       = 25;
$mail->Username   = "yablog@163.com";
$mail->Password   = "mrmsl170066918";
$mail->SetFrom('yablog@163.com', 'yablog');
$mail->AddAddress('mrmsl@qq.com', 'mrmsl@qq.com');
$mail->Subject = 'PHPMailer SMTP test';
$mail->MsgHTML(__FILE__);
if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}*/