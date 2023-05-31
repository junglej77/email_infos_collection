<?php

class Email_infos_collection_Public
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
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/email_info_form_pop.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/email_infos_collection-public.css', array(), $this->version, 'all');
	}
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/email_infos_collection-public.js', array(), $this->version, true);
	}
	public function emailFormSet()
	{
		$emailForm_template = file_get_contents(__DIR__ . '/partials/email_form.php'); // 初始化模板
		/**在这里调整手机表单的样式和需要的表单 */
		echo $emailForm_template;
	}
}
