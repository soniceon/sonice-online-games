<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../api/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../api/PHPMailer/SMTP.php';
require_once __DIR__ . '/../api/PHPMailer/Exception.php';

class Mailer {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config.php';
    }
    
    public function sendActivationEmail($email, $activationCode) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['mail']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['mail']['username'];
            $mail->Password = $this->config['mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['mail']['port'];
            $mail->CharSet = 'UTF-8';
            
            // 启用调试
            $mail->SMTPDebug = 3;
            $debug_output = '';
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                error_log(date('Y-m-d H:i:s') . " [$level] $str");
                $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
            };
            
            $mail->setFrom($this->config['mail']['username'], $this->config['mail']['from_name']);
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = "激活您的 Sonice Games 账号";
            
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $base_url = $protocol . $_SERVER['HTTP_HOST'];
            if (dirname($_SERVER['PHP_SELF']) !== '/') {
                $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
            }
            
            $activationLink = $base_url . '/api/verify-email.php?email=' . urlencode($email) . '&token=' . $activationCode;
            
            $mail->Body = "
            <html>
            <head>
                <title>激活您的账号</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #3498DB; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { margin-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>欢迎加入 Sonice Games！</h2>
                    <p>感谢您注册我们的服务。请点击下面的按钮激活您的账号：</p>
                    <p><a href='{$activationLink}' class='button'>激活账号</a></p>
                    <p>如果按钮无法点击，请复制以下链接到浏览器地址栏：</p>
                    <p>{$activationLink}</p>
                    <div class='footer'>
                        <p>如果您没有注册账号，请忽略此邮件。</p>
                        <p>此链接将在24小时后失效。</p>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->send();
            error_log("Activation email sent successfully to: " . $email);
            return true;
        } catch (Exception $e) {
            error_log("Mail sending error: " . $e->getMessage());
            return false;
        }
    }
} 
 
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../api/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../api/PHPMailer/SMTP.php';
require_once __DIR__ . '/../api/PHPMailer/Exception.php';

class Mailer {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config.php';
    }
    
    public function sendActivationEmail($email, $activationCode) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['mail']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['mail']['username'];
            $mail->Password = $this->config['mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['mail']['port'];
            $mail->CharSet = 'UTF-8';
            
            // 启用调试
            $mail->SMTPDebug = 3;
            $debug_output = '';
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                error_log(date('Y-m-d H:i:s') . " [$level] $str");
                $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
            };
            
            $mail->setFrom($this->config['mail']['username'], $this->config['mail']['from_name']);
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = "激活您的 Sonice Games 账号";
            
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $base_url = $protocol . $_SERVER['HTTP_HOST'];
            if (dirname($_SERVER['PHP_SELF']) !== '/') {
                $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
            }
            
            $activationLink = $base_url . '/api/verify-email.php?email=' . urlencode($email) . '&token=' . $activationCode;
            
            $mail->Body = "
            <html>
            <head>
                <title>激活您的账号</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #3498DB; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { margin-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>欢迎加入 Sonice Games！</h2>
                    <p>感谢您注册我们的服务。请点击下面的按钮激活您的账号：</p>
                    <p><a href='{$activationLink}' class='button'>激活账号</a></p>
                    <p>如果按钮无法点击，请复制以下链接到浏览器地址栏：</p>
                    <p>{$activationLink}</p>
                    <div class='footer'>
                        <p>如果您没有注册账号，请忽略此邮件。</p>
                        <p>此链接将在24小时后失效。</p>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->send();
            error_log("Activation email sent successfully to: " . $email);
            return true;
        } catch (Exception $e) {
            error_log("Mail sending error: " . $e->getMessage());
            return false;
        }
    }
} 
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../api/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../api/PHPMailer/SMTP.php';
require_once __DIR__ . '/../api/PHPMailer/Exception.php';

class Mailer {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config.php';
    }
    
    public function sendActivationEmail($email, $activationCode) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['mail']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['mail']['username'];
            $mail->Password = $this->config['mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['mail']['port'];
            $mail->CharSet = 'UTF-8';
            
            // 启用调试
            $mail->SMTPDebug = 3;
            $debug_output = '';
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                error_log(date('Y-m-d H:i:s') . " [$level] $str");
                $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
            };
            
            $mail->setFrom($this->config['mail']['username'], $this->config['mail']['from_name']);
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = "激活您的 Sonice Games 账号";
            
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $base_url = $protocol . $_SERVER['HTTP_HOST'];
            if (dirname($_SERVER['PHP_SELF']) !== '/') {
                $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
            }
            
            $activationLink = $base_url . '/api/verify-email.php?email=' . urlencode($email) . '&token=' . $activationCode;
            
            $mail->Body = "
            <html>
            <head>
                <title>激活您的账号</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #3498DB; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { margin-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>欢迎加入 Sonice Games！</h2>
                    <p>感谢您注册我们的服务。请点击下面的按钮激活您的账号：</p>
                    <p><a href='{$activationLink}' class='button'>激活账号</a></p>
                    <p>如果按钮无法点击，请复制以下链接到浏览器地址栏：</p>
                    <p>{$activationLink}</p>
                    <div class='footer'>
                        <p>如果您没有注册账号，请忽略此邮件。</p>
                        <p>此链接将在24小时后失效。</p>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->send();
            error_log("Activation email sent successfully to: " . $email);
            return true;
        } catch (Exception $e) {
            error_log("Mail sending error: " . $e->getMessage());
            return false;
        }
    }
} 
 
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../api/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../api/PHPMailer/SMTP.php';
require_once __DIR__ . '/../api/PHPMailer/Exception.php';

class Mailer {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../config.php';
    }
    
    public function sendActivationEmail($email, $activationCode) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['mail']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['mail']['username'];
            $mail->Password = $this->config['mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['mail']['port'];
            $mail->CharSet = 'UTF-8';
            
            // 启用调试
            $mail->SMTPDebug = 3;
            $debug_output = '';
            $mail->Debugoutput = function($str, $level) use (&$debug_output) {
                error_log(date('Y-m-d H:i:s') . " [$level] $str");
                $debug_output .= date('Y-m-d H:i:s') . " [$level] $str\n";
            };
            
            $mail->setFrom($this->config['mail']['username'], $this->config['mail']['from_name']);
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = "激活您的 Sonice Games 账号";
            
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $base_url = $protocol . $_SERVER['HTTP_HOST'];
            if (dirname($_SERVER['PHP_SELF']) !== '/') {
                $base_url .= dirname(dirname($_SERVER['PHP_SELF']));
            }
            
            $activationLink = $base_url . '/api/verify-email.php?email=' . urlencode($email) . '&token=' . $activationCode;
            
            $mail->Body = "
            <html>
            <head>
                <title>激活您的账号</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #3498DB; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { margin-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>欢迎加入 Sonice Games！</h2>
                    <p>感谢您注册我们的服务。请点击下面的按钮激活您的账号：</p>
                    <p><a href='{$activationLink}' class='button'>激活账号</a></p>
                    <p>如果按钮无法点击，请复制以下链接到浏览器地址栏：</p>
                    <p>{$activationLink}</p>
                    <div class='footer'>
                        <p>如果您没有注册账号，请忽略此邮件。</p>
                        <p>此链接将在24小时后失效。</p>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->send();
            error_log("Activation email sent successfully to: " . $email);
            return true;
        } catch (Exception $e) {
            error_log("Mail sending error: " . $e->getMessage());
            return false;
        }
    }
} 
 