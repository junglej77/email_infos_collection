<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

function send_email($request)
{
    // 创建 PHPMailer 实例
    $phpmailer = new PHPMailer();

    $phpmailer->isSMTP();
    // SMTP 配置
    $phpmailer->Host       = get_option('jungle_email_host'); // SMTP server
    $phpmailer->SMTPAuth   = true; // Enable SMTP authentication
    $phpmailer->Username   = get_option('jungle_email_account'); // SMTP username
    $phpmailer->Password   = get_option('jungle_email_password'); // SMTP password
    $phpmailer->SMTPSecure =  get_option('jungle_email_encryption'); // Encryption type, tls or ssl
    $phpmailer->Port       = get_option('jungle_email_port'); // SMTP Port

    // 获取传参参数
    $from_email = get_option('jungle_email_account');
    $from_name  = 'jungle';
    $to_email   = $request->get_param('to_email');
    $to_name    = $request->get_param('to_name');
    $subject    = $request->get_param('subject');
    $body       = $request->get_param('body');
    $attachment_path       = $request->get_param('attachment_path');

    // 配置发件人和收件人
    $phpmailer->setFrom($from_email,  $from_name);
    $phpmailer->addAddress($to_email, $to_name);


    // 邮件内容为 HTML 格式
    $phpmailer->isHTML(true);
    $phpmailer->Subject = $subject;
    $phpmailer->Body    = $body;

    if (!empty($attachment_path)) {
        // Read the file contents and encode using base64
        $file_contents = file_get_contents($attachment_path);
        $file_data = chunk_split(base64_encode($file_contents));

        // Set the appropriate MIME type
        $mime_type = mime_content_type($attachment_path);

        // Add the attachment to the email
        $filename = basename($attachment_path);
        $phpmailer->addStringAttachment($file_data, $filename, 'base64', $mime_type);
    }

    try {
        // 发送邮件
        $phpmailer->send();
        // 发送成功时的处理逻辑
        return  array(
            'code' => 200,
            'result' => '邮件发送成功',
        );
    } catch (Exception $e) {
        // 发送失败时的处理逻辑
        return array(
            'code' => 500,
            'result' => '邮件发送失败: ' . $phpmailer->ErrorInfo,
        );
    }
    // 输出结果到页面
}
