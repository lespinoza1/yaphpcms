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
     * @var object $_db 数据库实例
     */
    private $_db = null;
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
    public $thiser = 'smtp';

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
     * @param object $db         数据库实例
     * @param bool   $exceptions true可捕获发送异常。默认true
     *
     * @return void 无返回值
     */
    public function __construct($db, $exceptions = true) {
        parent::__construct($exceptions);
        $this->SetLanguage('zh_cn', PHPMAILER_PATH . 'language/');
        $this->setConfig();
        $this->_db = $db;
    }

    /**
     * 设置邮箱配置
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-06 09:22:33
     *
     * @param array $config 配置信息,默认null,通过sys_config()获取
     *
     * @return void 无返回值
     */
    public function setConfig($config = null) {
        $config = null === $config ? sys_config() : $config;
        $this->Host       = $config['sys_mail_smtp'];
        $this->Port       = $config['sys_mail_smtp_port'];
        $this->Username   = $config['sys_mail_email'];
        $this->Password   = $config['sys_mail_password'];
        $this->SetFrom($config['sys_mail_email'], $config['sys_mail_from_name']);
    }
}