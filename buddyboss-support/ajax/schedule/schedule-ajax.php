<?php

/**
 * learning login
 *
 */
function learning_view(){
    $result = array(
        'code' => 0,
        'message' => 'success'
    );

    $group_id = absint( $_REQUEST['group_id'] );
    if(empty($group_id)){
        $result['code'] = 1;
        $result['message'] = 'group_id is null';
        wp_send_json_error($result);
        die();
    }

    global $wpdb;

    $table_name_squad = $wpdb->prefix.TABLE_SQUAD;
    $table_name_squad_detail = $wpdb->prefix.TABLE_SQUAD_DETAIL;

    //获取当前训练营的分组信息
    $find_squad_sql = " select squad_id from ".$table_name_squad." where group_id = ".$group_id." ";
    $squad_id = $wpdb->get_var($find_squad_sql);

    //无分组，写入默认分组信息
    $user_id = get_current_user_id();

    if(empty($squad_id)){
        $squad_id = 'squad'.uniqid();
        $wpdb -> insert($table_name_squad,array(
            'squad_id' => $squad_id,
            'group_id' => $group_id,
            'squad_name' => 'DEFAULT',
            'create_time' => date('Y-m-d H:i:s',time()),
            'update_time' => date('Y-m-d H:i:s',time()),
            'create_user' => $user_id,
            'update_user' => $user_id
        ));

        $wpdb -> insert($table_name_squad_detail,array(
            'squad_id' => $squad_id,
            'user_id' => $user_id,
            'last_login_time' => date('Y-m-d H:i:s',time()),
            'create_user' => $user_id,
            'create_time' => date('Y-m-d H:i:s',time()),
            'update_user' => $user_id,
            'update_time' => date('Y-m-d H:i:s',time())
        ));

        wp_send_json_success($result);
        die();
    }

    //更新登陆时间
    $find_squad_user = " select squad_detail_id from ".$table_name_squad_detail." where squad_id = '".$squad_id."' and user_id = '".$user_id."' ";
    $squad_detail_id = $wpdb->get_var($find_squad_user);
    if(empty($squad_detail_id)){
        $wpdb -> insert($table_name_squad_detail,array(
            'squad_id' => $squad_id,
            'user_id' => $user_id,
            'last_login_time' => date('Y-m-d H:i:s',time()),
            'create_user' => $user_id,
            'create_time' => date('Y-m-d H:i:s',time()),
            'update_user' => $user_id,
            'update_time' => date('Y-m-d H:i:s',time())
        ));
        wp_send_json_success($result);
        die();
    }else{
        $wpdb -> update($table_name_squad_detail
            ,array('last_login_time' => date('Y-m-d H:i:s',time()))
            ,array('squad_detail_id' => $squad_detail_id)
        );
        wp_send_json_success($result);
        die();
    }


    $result['code'] = 1;
    $result['message'] = 'undefined';
    wp_send_json_error($result);
    die();

}

add_action( 'wp_ajax_learning_view', 'learning_view' );
add_action( 'wp_ajax_nopriv_learning_view', 'learning_view' );

/**
 * look case
 */
function case_view(){
    $result = array(
        'code' => 0,
        'message' => 'success'
    );

    $case_id = absint( $_REQUEST['case_id'] );


    if(empty($case_id)){
        $result['code'] = 1;
        $result['message'] = 'case_id is null';
        wp_send_json_error($result);
        die();
    }

    $user_id = get_current_user_id();
    $post_views = get_user_meta( $user_id, '_up_post_views', true );

    if(empty($post_views)){
        $post_views = array($case_id);
        add_user_meta($user_id,'_up_post_views' ,$post_views);
    }else{

        if(!in_array($case_id,$post_views)){
            $post_views[] = $case_id;
            update_user_meta($user_id,'_up_post_views' ,$post_views);
        }
    }
    wp_send_json_success($result);
    die();
}

add_action( 'wp_ajax_case_view', 'case_view' );
add_action( 'wp_ajax_nopriv_case_view', 'case_view' );
