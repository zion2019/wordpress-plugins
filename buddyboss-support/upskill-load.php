<?php
/**
 * Plugin Name: BuddyBoss Runwise
 * Plugin URI:  https://upskill.pro
 * Description: Runwise针对buddypress拓展
 * Author:      Runwise Dev
 * Author URI:  https://runwise.co
 * Version:     1.0.0
 * License:     GPLv2
 */




/**
 * constant param
 */
define('BUDDYBOSS_UPSKILL_VERSION','1.0.0');
define('PLUGIN_ROOT_PATH',WP_PLUGIN_URL."/".dirname(plugin_basename(__FILE__)));
define('UP_ENABLE_GROUP_ARRAY','up_enable_group_array');

define('TABLE_UP_SCHEDULE','up_group_schedule');
define('TABLE_SQUAD','up_group_squad');
define('TABLE_SQUAD_DETAIL','up_group_squad_detail');

require_once 'includes/upskill-step.php';
require_once 'ajax/schedule/schedule-ajax.php';


/**
 * register install and uninstall
 */
register_activation_hook(__FILE__, 'step_activation');
register_deactivation_hook(__FILE__, 'step_deactivation');


/**
 * ----------------------------------------------------------------------------------
 * add schedule tab to group  start
 * ----------------------------------------------------------------------------------
 */
function schedule_groups_individual_steps_page() {
    if(!current_group_enable()){
        return;
    }

    global $bp;
    if (isset($bp->groups->current_group->slug)) {
        bp_core_new_subnav_item(array(
            'name' => '训练营日程',
            'slug' => 'schedule',
            'parent_slug' => $bp->groups->current_group->slug,
            'parent_url' => bp_get_group_permalink($bp->groups->current_group),
            'screen_function' => 'schedule_group_show_screen',
            'position' => 1));
    }
}

add_action('wp', 'schedule_groups_individual_steps_page');

function schedule_group_show_screen() {

    add_action('bp_template_title', 'schedule_group_show_screen_title');
    add_action('bp_template_content', 'schedule_group_show_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }
}

function schedule_group_show_screen_title() {
    echo '训练营日程';
}

function schedule_group_show_screen_content() {
    include 'pages/schedule/schedule.php';
}


/**
 * 获取当前训练营是否开启日程tab
 */
function current_group_enable(){
    $group_id = bp_get_current_group_id();
    $up_enable_group_array = get_option(UP_ENABLE_GROUP_ARRAY);

    if(empty($up_enable_group_array)){
        update_option(UP_ENABLE_GROUP_ARRAY,array(18));
    }

    if(!empty($up_enable_group_array) && in_array($group_id,$up_enable_group_array,true)){
        return true;
    }
    return false;
}


/**
 * change group default sub nav
 * @return string
 */
function change_selected_default_sub_nav($home){
    if(!current_group_enable()){
        return $home;
    }
    return 'schedule';
}
add_filter('bp_groups_default_extension','change_selected_default_sub_nav');

/**
 * ----------------------------------------------------------------------------------
 * add schedule tab to group  end
 * ----------------------------------------------------------------------------------
 */

/**
 * ----------------------------------------------------------------------------------
 * add tab to admin menu  start
 * ----------------------------------------------------------------------------------
 */
function sp_admin_menu() {
    add_menu_page(
        'BudddyBoss Runwise',
        'BudddyBoss Runwise',
        'manage_options',
        'br-admin',
        'br_admin_page'
    );
}
add_action( 'admin_menu', 'sp_admin_menu' );

function br_admin_page(){
    require_once dirname( __FILE__ ) . "/pages/admin/admin.php";
}

/**
 * ----------------------------------------------------------------------------------
 * add tab to admin menu  end
 * ----------------------------------------------------------------------------------
 */