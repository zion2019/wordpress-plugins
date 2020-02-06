<?php
/**
 * plugins cycle function
 */

wp_register_script('upskill_schedule_js',PLUGIN_ROOT_PATH.'/js/schedule.js', '', BUDDYBOSS_UPSKILL_VERSION);


/**
 * activation
 */
function step_activation(){
    error_log('>>>>>>>>>>>runwise plugin install tables<<<<<<<<<<<');
    create_table();

}
/**
 * activation
 */
function step_deactivation(){
    error_log('>>>>>>>>>>>runwise plugin remove tables<<<<<<<<<<<');
    remove_table();
}


/**
 * install create tables
 */
function create_table()
{
    global $wpdb;

    // shortcuts for upskill DB tables
    $table_name = $wpdb->prefix.TABLE_UP_SCHEDULE;
    $db_version = get_option('sp_db_version', 0);

    // create tables on new installs
    if (empty($db_version)) {

        $charset_collate = '';

        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty( $wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql_schedule = "
		CREATE TABLE `" . $table_name . "` (
          `schedule_id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'key',
          `group_id` int(20) DEFAULT NULL,
          `begin_time` datetime NOT NULL,
          `end_time` datetime NOT NULL,
          `group_target` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
          `schedule_content` varchar(5000) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'content',
          `create_time` datetime DEFAULT NULL,
          `update_time` datetime DEFAULT NULL,
          `create_user` int(20) DEFAULT NULL,
          `update_user` int(20) DEFAULT NULL,
          PRIMARY KEY (`schedule_id`)
		) ".$charset_collate;

        $sql_squad = "
            CREATE TABLE `". $wpdb->prefix.TABLE_SQUAD."` (
              `squad_id` varchar(45) NOT NULL ,
              `group_id` int(20) DEFAULT NULL,
              `squad_name` varchar(255) DEFAULT NULL,
              `create_time` datetime DEFAULT NULL,
              `update_time` datetime DEFAULT NULL,
              `create_user` int(20) DEFAULT NULL,
              `update_user` int(20) DEFAULT NULL,
              PRIMARY KEY (`squad_id`)
            )
        ".$charset_collate;


        $sql_squad_detail = "
           CREATE TABLE `".$wpdb->prefix.TABLE_SQUAD_DETAIL."` (
              `squad_detail_id` int(20) NOT NULL AUTO_INCREMENT,
              `squad_id` varchar(45) DEFAULT NULL,
              `user_id` int(20) DEFAULT NULL,
              `last_login_time` datetime DEFAULT NULL,
              `create_user` int(20) DEFAULT NULL,
              `create_time` datetime DEFAULT NULL,
              `update_user` int(20) DEFAULT NULL,
              `update_time` datetime DEFAULT NULL,
              PRIMARY KEY (`squad_detail_id`)
            )
        ".$charset_collate;


        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_schedule);
        dbDelta($sql_squad);
        dbDelta($sql_squad_detail);
    }
}


/**
 * uninstall remove tables
 */
function remove_table(){
    global $wpdb;
    $table_name = $wpdb->prefix.TABLE_UP_SCHEDULE;
    if($wpdb->get_var("show tables like '$table_name'")== $table_name){
        $sql = 'DROP TABLE  `'.$table_name.'`';
        $wpdb->query($sql);
    }

    $table_name = $wpdb->prefix.TABLE_SQUAD;
    if($wpdb->get_var("show tables like '$table_name'")== $table_name){
        $sql = 'DROP TABLE  `'.$table_name.'`';
        $wpdb->query($sql);
    }

    $table_name = $wpdb->prefix.TABLE_SQUAD_DETAIL;
    if($wpdb->get_var("show tables like '$table_name'")== $table_name){
        $sql = 'DROP TABLE  `'.$table_name.'`';
        $wpdb->query($sql);
    }
}

?>
