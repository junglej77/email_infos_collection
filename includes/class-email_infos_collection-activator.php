<?php
class Email_infos_collection_Activator
{
	public static function activate()
	{
		// 插件激活时， 注册所有没有的数据表
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/create_table/index.php';
	}
}
