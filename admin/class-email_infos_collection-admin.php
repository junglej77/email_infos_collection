<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
class Email_infos_collection_Admin
{
	private $plugin_name;
	private $version;
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/email_infos_collection-admin.css', array(), $this->version, 'all');
	}
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/email_infos_collection-admin.js', array('jquery'), $this->version, false);
	}
	public function add_plugin_admin_menu() // 主菜单
	{
		add_menu_page(
			'Email Infos',    // 主菜单名称
			'收集邮箱',    // 菜单标题
			'manage_options', // 用户权限
			'email_infos',    // 菜单标识
			array($this, 'email_infos_list_page') // 回调函数
		);
	}
	public function add_plugin_admin_submenu() // 子菜单
	{
		add_submenu_page(
			'email_infos',     // 父菜单标识
			'Info List',       // 子菜单页面标题
			'邮箱列表',       // 子菜单标题
			'manage_options',  // 用户权限
			'email_infos',   // 子菜单标识
			array($this, 'email_infos_list_page') // 回调函数
		);
		add_submenu_page(
			'email_infos',     // 父菜单标识
			'form setting',           // 子菜单页面标题
			'收集表单设置',           // 子菜单标题
			'manage_options',  // 用户权限
			'form_setting',  // 子菜单标识
			array($this, 'email_form_setting_page') // 回调函数
		);
		add_submenu_page(
			'email_infos',     // 父菜单标识
			'Setup',           // 子菜单页面标题
			'设置',           // 子菜单标题
			'manage_options',  // 用户权限
			'email_infos_setup',  // 子菜单标识
			array($this, 'email_infos_setup_page') // 回调函数
		);
	}
	public function email_infos_setup_page() // 设置页面
	{
		include_once('partials/email_infos_setup_page.php');
	}
	public function email_infos_list_page() // 列表页面
	{
		include_once('partials/email_infos_list_page.php');
	}
	public function email_form_setting_page() // 表单样式设计
	{
		include_once('partials/email_form_setting_page.php');
	}
	public function ajax_rest_api_init() // ajax生成
	{

		// 增加选项
		register_rest_route('myplugin/v1', '/addOption', array(
			'methods' => 'POST',
			'callback' => 'myplugin_add_option',
			'args' => array(
				'name' => array(
					'required' => true
				),
				'value' => array(
					'required' => true
				)
			)
		));
		function myplugin_add_option($request)
		{
			add_option($request['name'], $request['value']);
			return get_option($request['name']);
		}
		// 获取选项
		register_rest_route('myplugin/v1', '/option/(?P<name>[a-zA-Z0-9_-]+)', array(
			'methods' => 'GET',
			'callback' => 'myplugin_get_option',
			'args' => array(
				'name' => array(
					'required' => true
				)
			)
		));
		function myplugin_get_option($request)
		{
			return get_option($request['name']);
		}
		// 更新选项
		register_rest_route('myplugin/v1', '/option', array(
			'methods' => 'POST',
			'callback' => 'myplugin_update_option',
			'args' => array(
				'name' => array(
					'required' => true
				),
				'value' => array(
					'required' => true
				)
			)
		));
		function myplugin_update_option($request)
		{
			update_option($request['name'], $request['value']);
			return get_option($request['name']);
		}
		// 发送邮件
		register_rest_route('info/email', '/send', array(
			'methods' => 'POST',
			'callback' => 'send_email',
			'args' => array(
				'to_email' => array(
					'required' => true
				),
				'to_name' => array(
					'required' => true
				),
				'subject' => array(
					'required' => true
				),
				'body' => array(
					'required' => true
				),
				'attachment_path' => array(
					'required' => true
				),
			),
		));
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
			$to_email   = $request['to_email'];
			$to_name    = $request['to_name'];
			$subject    = $request['subject'];
			$body       = $request['body'];
			$attachment_path       = $request['attachment_path'];

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
	}
}
