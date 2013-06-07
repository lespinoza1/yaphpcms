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
     * @var object $_model 实例
     */
    private $_model = null;
    /**
     * @var object $_db 数据库实例
     */
    private $_db = null;
    /**
     * @var object $_view_template 数据库实例
     */
    private $_view_template = null;
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
     * 留言评论有回复，发邮件通知
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-07 17:36:25
     *
     * @param array $info 邮件信息,array('email' => $email, 'subject' => $subject, 'content' => $content)
     *
     * @return true|string true发送成功，否则错误信息
     */
    private function _comments_at_email($info) {
        $info['content'] = $this->_view_template->fetch('Mail', 'comments_at_email');
        var_dump($info);exit;
    }

    /**
     * 执行发送邮件
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-07 17:05:04
     *
     * @param array $info 邮件信息,array('email' => $email, 'subject' => $subject, 'content' => $content)
     *
     * @return true|string true发送成功，否则错误信息
     */
    private function _doMail($info) {
        $this->subject = $info['subject'];
        $this->MsgHTML($info['content']);
        $this->AddAddress($info['email']);

        if ($this->Send()) {
            return true;
        }
        else {
            $this->_model->addLog(L('SEND,CN_YOUJIAN') . $info['email'] . "({$info['subject']})" . L('FAILURE'), LOG_TYPE_EMAIL);
            return $this->ErrorInfo;
        }
    }

    /**
     * 重新发送邮件
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-07 16:50:57
     *
     * @param int $history_id 邮件历史id
     *
     * @return true|string true发送成功，否则错误信息
     */
    private function _reMail($history_id) {
        $info = $this->_model
        ->field('email,subject,content')
        ->table(TB_MAIL_HISTORY)
        ->where('history_id=' . $history_id)
        ->find();

        if ($info) {
            return $this->_doMail($info);
        }
        else {
            $error  = L('MAIL_TEMPLATE,INFO') . "({$history_id})" . L('NOT_EXIST');
            $log    = __METHOD__ . ': ' . __LINE__ . ',' . $error;
            C('TRIGGER_ERROR', array($log, E_USER_ERROR, 'mail.error'));
            $this->_model->addLog($log, LOG_TYPE_INVALID_PARAM);

            return $error;
        }
    }

    /**
     * 构造函数
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-05 17:42:41
     *
     * @param object $_model            数据库实例
     * @param object $view_template
     * @param bool   $exceptions true可捕获发送异常。默认false
     *
     * @return void 无返回值
     */
    public function __construct($_model, $view_template,$exceptions = false) {
        parent::__construct($exceptions);
        $this->SetLanguage('zh_cn', PHPMAILER_PATH . 'language/');
        $this->setConfig();
        $this->_model = $_model;
        $this->_view_template = $view_template;
    }

    /**
     * 发送邮件
     *
     * @author      mrmsl <msl-138@163.com>
     * @date        2013-06-07 14:17:32
     *
     * @param mixed $mail_info 邮件模板名称或邮件历史id
     * @param string $email 要发送的邮箱
     *
     * @return bool true ntud
     */
    public function mail($mail_info = null, $email = null) {

        if (is_numeric($mail_info)) {
           return $this->_reMail($mail_info);
        }
        elseif (is_array($mail_info)) {
            return $this->_doMail($mail_info);
        }
        else {
            static $mail_template_info = array();

            if (!isset($mail_template_info[$mail_info])) {
                $info = $this->_model
                ->table(TB_MAIL_TEMPLATE)
                ->where(array('template_name' => $mail_info))
                ->field('subject,template_name')
                ->find();

                if (!$info) {
                    $error  = L('MAIL_TEMPLATE,INFO') . "({$mail_info})" . L('NOT_EXIST');
                    $log    = __METHOD__ . ': ' . __LINE__ . ',' . $error;
                    C('TRIGGER_ERROR', array($log, E_USER_ERROR, 'mail.error'));
                    $this->_model->addLog($log, LOG_TYPE_INVALID_PARAM);
                    $mail_template_info[$mail_info] = false;

                    return $error;
                }

                $info['email'] = $email;
                $mail_template_info[$mail_info] = $info;
            }

            if ($info = $mail_template_info[$mail_info]) {
                if (method_exists($this, $method = '_' . $mail_info)) {//_+邮件模板名即为发送方法
                    return $this->$method($info);
                }
                else {
                    $error  = L('MAIL_TEMPLATE,METHOD') . $method . L('NOT_EXIST');
                    $log    = __METHOD__ . ': ' . __LINE__ . ',' . $error;
                    C('TRIGGER_ERROR', array($log, E_USER_ERROR, 'mail.error'));
                    $this->_model->addLog($log, LOG_TYPE_INVALID_PARAM);

                    return $error;
                }
            }
        }//end if
    }//end mail

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