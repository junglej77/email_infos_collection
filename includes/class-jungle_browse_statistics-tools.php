<?php
class JungleBrowseStatisticsTools
{
	//获取用户IP
	public static function get_location_ip_address()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	// 获取用户ip信息
	public static function get_location_by_ip($ip)
	{
		$url = "http://ip-api.com/json/$ip?lang=zh-CN";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		curl_close($ch);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($ch);
			return null;
		}

		$data = json_decode($response, true);
		if ($data['status'] === 'fail') {
			return null;
		}

		return [
			'country' => isset($data['country']) ? $data['country'] : '',
			'countryCode' => isset($data['countryCode']) ? $data['countryCode'] : '',
			'regionName' => isset($data['regionName']) ? $data['regionName'] : '',
			'city' => isset($data['city']) ? $data['city'] : '',
		];
	}
	// 获取设备信息
	public static function get_device_name($user_agent)
	{
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $user_agent)) {
			return '平板';
		} elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $user_agent)) {
			return '手机';
		} elseif (preg_match('/(ipod|iphone|ipad)/i', $user_agent)) {
			return 'iOS设备';
		} elseif (preg_match('/android/i', $user_agent)) {
			return '安卓设备';
		} elseif (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobi))/i', $user_agent)) {
			return '平板';
		} elseif (preg_match('/(kindle|silk|kftt|kfot|kfjwi|kfjwa|kfote|kfsowi|kfthwi|kfthwa|kfapwi|kfapwa)/i', $user_agent)) {
			return 'Kindle';
		} else {
			return '电脑';
		}
	}
	// 获取浏览器信息
	public static function get_browser_name($user_agent)
	{
		if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
		elseif (strpos($user_agent, 'Edge')) return 'Edge';
		elseif (strpos($user_agent, 'Chrome')) return '谷歌';
		elseif (strpos($user_agent, 'Safari')) return 'Safari';
		elseif (strpos($user_agent, 'Firefox')) return '火狐';
		elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'IE';
		return 'Other';
	}
	// 设置前端cookie
	public static function set_frontend_cookie($cookie_name, $cookie_value, $expiration)
	{
		$expiration = time() + $expiration; // 30天的过期时间
		$path = '/';
		$domain = ''; // 设置为你的域名
		$secure = false; // 设置为 true，如果你的网站使用了 SSL
		$httponly = false; // 设置为 true，如果你只希望通过 HTTP 协议访问 Cookie

		setcookie($cookie_name, $cookie_value, $expiration, $path, $domain, $secure, $httponly);
	}
	//获取前端 Cookie
	public static function get_frontend_cookie($cookie_name)
	{
		if (isset($_COOKIE[$cookie_name])) {
			return $_COOKIE[$cookie_name];
			// 处理你的逻辑，使用获取到的 Cookie 值
		}
		return '';
	}
	// 在这里生成一个唯一的缓存值（ip+当前时间戳）
	public static function generate_cache_ip($ip)
	{
		return time() . '#' . $ip;
	}
	// 判断访客是否是新访客，24小时之后再次访问就算老访客
	public static function is_new_visitor($wpdb, $table_name, $cache_ip)
	{
		/**
		 *  1， 当前访问时间，current_time
		 *  2， 以 $cache_ip为桥梁获取相关数据
		 *         访客最开始访问时间，
		 * 			没登陆的话就是first_stored_time,
		 * 			登录的话，就是当前u_id相同的增长id正序的第一个的first_stored_time
		 *  3， current_time - first_stored_time  超过24小时就是老访客
		 */
		$is_new = 'new';
		$sql = "SELECT * FROM $table_name WHERE cache_ip =%s";
		$params = array($cache_ip);
		if (is_user_logged_in()) {
			$user_logged_id = get_current_user_id();
			$sql = "SELECT * FROM $table_name WHERE u_id =%d";
			$params = array($user_logged_id);
		}
		$sql .= " ORDER BY id ASC";
		$sql .= " LIMIT 0, 1";
		$query = $wpdb->prepare($sql, $params);
		$results = $wpdb->get_results($query);
		$first_stored_time = $results[0]->first_stored_time;
		$timezone = "Asia/Shanghai"; // 北京位于"Asia/Shanghai"时区
		$dt = new DateTime($first_stored_time, new DateTimeZone($timezone));
		$timestamp = $dt->getTimestamp(); // 最早访问的时间
		$current_time = time();
		$is_new = $current_time - $timestamp > 60 * 60 * 24 ? 'old' : 'new';
		return $is_new;
	}
	// 判断访客是否是在线访客， 根据暂缓的数据表
	public static function is_online_visitor($wpdb, $table_name, $cache_ip)
	{

		$sql = "SELECT * FROM $table_name WHERE cache_ip =%s";
		$params = array($cache_ip);
		$sql .= " ORDER BY id ASC";
		$sql .= " LIMIT 0, 1";
		$query = $wpdb->prepare($sql, $params);
		$results = $wpdb->get_results($query);
		return empty($results[0]);
	}
	// 启动一个定时任务，检查在线访客有多少
	public static function look_online_visitor_count()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'jungle_statistics_user_online';

		// 检查数据表是否有数据
		if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name") > 0) {
			// 如果有数据，则进行更新操作
			delete_old_records();
			// ... 执行更新操作
		}
	}
}

function delete_old_records()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'jungle_statistics_user_online';
	// 删除 now_time 和当前时间相差超过300秒的记录
	$wpdb->query("
	DELETE FROM $table_name
    WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(now_time) > 15
		");
}
