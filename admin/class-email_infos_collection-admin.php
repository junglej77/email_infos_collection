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
		global $pagenow;
		if (
			$pagenow == 'admin.php'
		) {
			// 加载 Vue.js 库
			// wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), '2.6.12',);
			wp_enqueue_script('vue', 'https://unpkg.com/vue@next');

			// 加载 ElementUI 的 CSS 样式文件
			// wp_enqueue_style('element-ui', 'https://unpkg.com/element-ui/lib/theme-chalk/index.css');
			wp_enqueue_style('elementPlus', 'https://unpkg.com/element-plus@latest/theme-chalk/index.css');



			// 引入图标库
			wp_enqueue_script('elementPlusIcons', 'https://unpkg.com/@element-plus/icons-vue', array('vue'),);

			// 加载 ElementUI 的 JavaScript 文件
			// wp_enqueue_script('element-ui', 'https://unpkg.com/element-ui/lib/index.js', array('vue'), '2.15.1',);
			wp_enqueue_script('elementPlus', 'https://unpkg.com/element-plus@latest', array('vue'),);

			wp_enqueue_script('Sortable', 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.8.3/Sortable.min.js', array(), '',);

			//引入axios 请求
			wp_enqueue_script('axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array(), '',);

			//引入自定义后台样式表
			wp_enqueue_style('admin-ui', get_stylesheet_directory_uri() . '/assets/css/admin-ui.css', array(), wp_get_theme()->get('Version'), 'all');
		}
		// wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/email_infos_collection-admin.js', array('jquery'), $this->version, false);
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
		register_rest_route('info/email', '/senda', array(
			'methods' => 'POST',
			'callback' => 'myplugin_send_email',
			'args' => array(
				'to_email' => array(
					'required' => true,
					'validate_callback' => function ($param, $request, $key) {
						if ($param === '') {
							return new WP_Error('invalid_email', 'Email cannot be empty');
						} else if (!is_email($param)) {
							return new WP_Error('invalid_email', 'Incorrect email');
						}
						return true;
					}
				),
				'to_name' => array(
					'required' => true,
					'validate_callback' => function ($param, $request, $key) {
						if ($param === '') {
							return new WP_Error('invalid_email', 'Full Name Cannot be empty');
						}
						return true;
					}
				),
				'message' => array(
					'required' => true,
					'validate_callback' => function ($param, $request, $key) {
						if ($param === '') {
							return new WP_Error('invalid_email', 'Content cannot be empty');
						}
						return true;
					}
				)
			),
		));

		function myplugin_send_email($request)
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
			// 后台设置邮件自动回复内容
			$email_auto_repaly = get_option('jungle_email_auto_repaly') ?: '我们将会和你联系！';
			// 获取传参参数
			$from_email = get_option('jungle_email_account');
			$from_name  = get_option('jungle_email_name'); // 发件人名称
			$subject    = $request['subject']; // 邮件主题
			$to_email   = $request['to_email']; // 收件人邮箱
			$to_name    = $request['to_name']; // 收件人名称
			$to_phone    = $request['phone']; // 收件人手机
			$message       = $request['message']; // 客户邮件内容邮件内容
			$attachment       = $request['attachment']; // 客户邮件附件
			// 邮件模板
			$email_template = file_get_contents(__DIR__ . '/email_template.html');
			$email_template = str_replace('{{email_auto_repaly}}', $email_auto_repaly, $email_template);
			$email_template = str_replace('{{body}}', $message, $email_template);
			// 如果上传文件时图片就将图片展示在邮件内容中
			if ($attachment['isImage']) {
				$email_template = str_replace('{{img}}', '<img src="' . $attachment['path'] . '">', $email_template);
			} else {
				$email_template = str_replace('{{img}}', '', $email_template);
			}
			// 配置发件人和收件人
			$phpmailer->setFrom($from_email,  $from_name);
			$phpmailer->addAddress($to_email, $to_name);

			// 邮件内容为 HTML 格式
			$phpmailer->CharSet = 'UTF-8';
			$phpmailer->isHTML(true);
			$phpmailer->Subject = $phpmailer->encodeHeader($subject);
			$phpmailer->Body    = $email_template;
			// 添加附件
			if (!empty($attachment)) {
				$phpmailer->addAttachment($attachment['path'], $attachment['fileName']);
			}

			storageContact($to_name, $to_email, $to_phone);
			try {
				// 发送邮件
				$phpmailer->send();
				// 发送成功时的处理逻辑
				return  array(
					'code' => 200,
					'result' => '邮件发送成功',
					'attachment' => $attachment,
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
		function storageContact($to_name, $to_email, $to_phone)
		{
			global $wpdb;
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			/***********************************************获取当前用户的IP地址 */
			// $ip_address = JungleBrowseStatisticsTools::get_location_ip_address();
			$ip_address = '116.25.106.143';

			/***********************************************获取设备*/
			$device = JungleBrowseStatisticsTools::get_device_name($user_agent);
			/***********************************************获取浏览器*/
			$browser = JungleBrowseStatisticsTools::get_browser_name($user_agent);
			/***********************************************获取IP地址的位置信息 */
			// 获取IP地址的位置信息，你需要使用你自己的函数替换下面的代码
			$location = JungleBrowseStatisticsTools::get_location_by_ip($ip_address);
			/***********************************************处理Ajax请求，例如保存数据到数据库 */
			$table_name = $wpdb->prefix . 'jungle_users_infos';

			$email_exists = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name WHERE email = %s",
					$to_email
				)
			);

			$data = array(
				'name' => $to_name,
				'email' => $to_email,
				'phone' => $to_phone,
				'device' => $device,
				'browser' => $browser,
				'ip_address' => $ip_address,
				'countryCode' => isset($location) ? $location['countryCode'] : '',
				'country' => isset($location) ? $location['country'] : '',
				'state' => isset($location) ? $location['regionName'] : '',
				'city' => isset($location) ? $location['city'] : '',
				'send_time' => current_time('Y-m-d H:i:s'),
			);
			if ($email_exists > 0) {
				//更新发送邮件次数和发送时间
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE $table_name SET send_time = %s, send_count = send_count + 1 WHERE email = %s",
						current_time('mysql'),
						$to_email
					)
				);
			} else {
				//添加数据
				$wpdb->insert($table_name, $data);
			}
		}
		// 查询收集数据表
		register_rest_route('get/infos', '/list', array(
			'methods' => 'POST',
			'callback' => 'get_email_infos_list',
		));
		function get_email_infos_list($request)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . 'jungle_users_infos';

			$items_per_page = isset($request['items_per_page']) ? intval($request['items_per_page']) : 10;
			$page_number = isset($request['page_number']) ? intval($request['page_number']) : 1;
			$order_by = isset($request['order_by']) ? $request['order_by'] : 'id';
			$order = isset($request['order']) ? $request['order'] : 'DESC';
			$offset = ($page_number - 1) * $items_per_page;
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY %s %s LIMIT %d OFFSET %d", $order_by, $order, $items_per_page, $offset));
			// 将结果返回给前端
			wp_send_json($results);
		}
	}
	// 上传文件
	public function handle_file_upload() // 上传文件wp_ajax_upload_file
	{
		// 检查是否有文件上传
		if (isset($_FILES['file'])) {
			// 获取上传文件信息
			$file = $_FILES['file'];
			$file_name = $file['name'];
			$file_tmp_name = $file['tmp_name'];
			$file_error = $file['error'];

			// 检查上传是否成功
			if ($file_error === 0) {
				// 修改文件名，加上一个固定字符串，确认文件时哪里上传来的。
				$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
				$file_base_name = pathinfo($file_name, PATHINFO_FILENAME);
				$fixed_string  = 'customer_upload';  // 创建一个随机字符串
				$new_file_name = $file_base_name . '_' . $fixed_string  . '.' . $file_ext;
				// 确定保存文件的路径
				$uploads_dir = wp_upload_dir()['path'];
				$file_path = $uploads_dir . '/' . $new_file_name;
				// 如果文件已存在，那么添加一个数字后缀
				$suffix = 1;
				while (file_exists($file_path)) {
					$new_file_name = $file_base_name . '_' . $fixed_string . '_' . $suffix . '.' . $file_ext;
					$file_path = $uploads_dir . '/' . $new_file_name;
					$suffix++;
				}
				// 移动文件到目标目录
				if (move_uploaded_file($file_tmp_name, $file_path)) {
					// 加入到媒体库
					$attachment = array(
						'guid'           => $file_path,
						'post_mime_type' => mime_content_type($file_path),
						'post_title'     => preg_replace('/\.[^.]+$/', '', $file_name),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attach_id = wp_insert_attachment($attachment, $file_path);
					// 返回文件路径
					wp_send_json_success(array(
						'path' => $file_path,
						'fileName' => $file_name,
						'isImage' => in_array(strtoupper($file_ext), array('JPG', 'PNG', 'GIF', 'SVG', 'WEBP', 'AVIF'))
					));
				} else {
					wp_send_json_error('Unable to move files,Please try again later');
				}
			} else {
				wp_send_json_error('Error uploading files,Please try again later');
			}
		} else {
			wp_send_json_error('No file upload');
		}
	}
}
