<?php
global $wpdb;
$table_name = $wpdb->prefix . 'jungle_users_infos';
//检查数据表是否已经存在
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    // 创建数据表
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50),
        device varchar(255),
        browser varchar(255),
        ip_address varchar(45),
        country varchar(255),
        countryCode varchar(5),
        state varchar(255),
        city varchar(255),
        send_time datetime DEFAULT NULL,
        send_count mediumint(9) NOT NULL DEFAULT 1,
        remark TEXT,
        status mediumint(1)  NOT NULL DEFAULT 0,
        PRIMARY KEY  (id)
    ) $charset_collate;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
