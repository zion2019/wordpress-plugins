<?php

/**
 * has schedule data
 */
function up_has_schedule()
{
    $upskill_schedule = upskill_schedule();
    return (!empty($upskill_schedule->current_schedule_data) || !empty($upskill_schedule->history_schedule_data));
}

/**
 * 我的进度
 */
function get_my_progress(){
    $upskill_schedule = upskill_schedule();
    $my_progress = $upskill_schedule -> group_progress[$upskill_schedule -> current_user_id];
    echo (ceil($my_progress['finished_num']/$my_progress['schedule_num']*100)).'%';
}


function get_all_progress(){
    $upskill_schedule = upskill_schedule();
    $group_progress = $upskill_schedule -> group_progress;

    $finished = 0;
    $schedule = 0;

    foreach ($group_progress as $unit_progress){
        $finished += $unit_progress['finished_num'];
        $schedule += $unit_progress['schedule_num'];
    }

    echo (ceil($finished/$schedule*100)).'%';
}

/**
 * @return UpskillSchedule
 */
function upskill_schedule()
{
    return UpskillSchedule::getInstance();
}

/**
 * build finished class
 * @param $status
 * schedule_status
 *  lock
 *  unfinished
 *  finished
 *  run_out
 */
function get_finished_class($status)
{
    switch ($status) {
        case 'lock':
            echo 'locked';
            break;
        case 'unfinished':
            echo 'status';
            break;
        case 'finished' :
            echo 'done';
            break;
        case 'run_out' :
            echo 'undone';
            break;
        default :
            echo 'locked';
    }
}

/**
 * Class UpskillSchedule
 * CRUD schedule data
 */
class UpskillSchedule
{

    private static $instance = null;

    /**
     * @var array
     *  group schedule content
     */
    public $schedule_data;

    public $current_schedule_data;

    public $history_schedule_data;

    public $group_begin_time;

    public $group_end_time;

    public $group_target;

    public $current_user_id;

    public $group_progress;

    public $today_users;

    public $group_users;

    public $user_practice_integral;

    public $user_learning_integral;

    /**
     * UpskillSchedule constructor.
     * singleton
     */
    private function __construct()
    {
        $this->current_user_id = get_current_user_id();

        $this->find_group_data();
        $this->init_schedule_progress();
        $this->get_all_user_integral();
    }

    /**
     * @return UpskillSchedule
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * 防止clone 多个实列
     */
    private function __clone()
    {
    }

    /**
     * 防止反序列化
     */
    private function __wakeup()
    {
    }

    /**
     * 获取当前训练营成员积分信息
     */
    function get_all_user_integral(){
        global $wpdb;
        if(empty($this->group_users)){
            error_log('current group users is empty');
            return ;
        }

        $user_ids = array();
        foreach ($this -> group_users as $user){
            $user_ids[] = $user['user_id'];
        }

        $sql = "select user_id,meta_key,meta_value from wp_usermeta 
                where  meta_key in ('_gamipress_learning-completion_points','_gamipress_learning-practice_points')
                and user_id  in (".implode(',',array_unique($user_ids)).")";
        $integral_result = $wpdb->get_results($sql, ARRAY_A);
        if(!is_array($integral_result) || empty($integral_result)){
            error_log('find user_integral empty sql :'.$sql);
           return ;
        }

        foreach ($integral_result as $user_integral){

            if('_gamipress_learning-completion_points' == $user_integral['meta_key']){
                $this -> user_learning_integral[$user_integral['user_id']] = $user_integral['meta_value'];
            }else{
                $this -> user_practice_integral[$user_integral['user_id']] = $user_integral['meta_value'];
            }
        }


        error_log('user_learning_integral ::>:'.json_encode($this -> user_learning_integral));
        error_log('user_learning_integral ::>:'.json_encode($this -> user_practice_integral));

    }

    /**
     * 获取进度信息
     *  任务数 = 问题+微课+案例
     */
    private function init_schedule_progress()
    {
        global $wpdb;

        // all members
        $members_sql = "select m.user_id,u.user_nicename from wp_bp_groups g , wp_bp_groups_members m ,wp_users u
                        where g.id = m.group_id
                        and m.user_id = u.id
                        and g.id = '" . bp_get_current_group_id() . "'";
        $members = $wpdb->get_results($members_sql, ARRAY_A);

        // set users
        $this -> group_users = $members;

        $question_array = array();

        foreach ($members as $member) {

            $schedule_num = 0;
            $finish_num = 0;
            $user_id = $member['user_id'];

            if(is_array($this->schedule_data)){
                foreach ($this->schedule_data as $unit_schedule) {

                    //get all question
                    if (!empty($unit_schedule['question']) || !empty($unit_schedule['question']['activity_id'])) {
                        $schedule_num++;
                        $question_array[] = $unit_schedule['question']['activity_id'];
                    } else {
                        error_log('schedule id :' . $unit_schedule['schedule_id'] . ' question_id is null');
                    }

                    //handle lesson
                    if (!empty($unit_schedule['lesson'])) {
                        foreach ($unit_schedule['lesson'] as $lesson) {
                            if (!empty($lesson)) {

                                $schedule_num++;
                                if ($this->is_finished_lesson($user_id, $lesson['course_id'], $lesson['lesson_id'])) {
                                    $finish_num++;
                                }

                            }
                        }
                    }// handle lesson

                    //handle case
                    if (!empty($unit_schedule['case'])) {
                        foreach ($unit_schedule['case'] as $case) {
                            if (!empty($case)) {

                                $schedule_num++;
                                if ($this->is_finished_case($user_id, $case['case_id'])) {
                                    $finish_num++;
                                }

                            }
                        }
                    } // handle case
                }
            }


            $this -> group_progress[$user_id] = array('finished_num'=>$finish_num,'schedule_num'=>$schedule_num);
        }

        // question finished
        if(is_array($question_array) && !empty($question_array)){

            $question_sql = "select  user_id,item_id from wp_bp_activity 
                         where component = 'activity' and type =  'activity_comment'
                         and item_id in (".implode(',',array_unique($question_array)).")
                         group by user_id , item_id";
            $question_result = $wpdb->get_results($question_sql, ARRAY_A);
        }


        if(empty($question_result)){
            return;
        }

        foreach ($members as $member){
            foreach ($question_result as $unit_question){
                if($unit_question['user_id'] == $member['user_id']){
                    $this->group_progress[$member['user_id']]['finished_num'] = $this -> group_progress[$member['user_id']]['finished_num']+1;
                }
            }
        }
    }

    /**
     * find data by current group_id
     */
    private function find_group_data()
    {
        global $wpdb;

        //get current day and year
        $today = getdate();
        $curr_day = $today['yday'];
        $curr_year = $today['year'];

        //get DB data
        $sql = 'SELECT schedule_id,begin_time,end_time,group_target,schedule_content FROM ' . $wpdb->prefix . TABLE_UP_SCHEDULE . ' WHERE group_id = ' . bp_get_current_group_id();
        $result = $wpdb->get_results($sql, ARRAY_A);
        $this->schedule_data = json_decode($result[0]['schedule_content'], true);

        $this->group_begin_time = substr($result[0]['begin_time'], 0, 10);
        $this->group_end_time = substr($result[0]['begin_time'], 0, 10);
        $this->group_target = $result[0]['group_target'];

        //handle finished and unfinished data and status
        if(is_array($this->schedule_data)){
            foreach ($this->schedule_data as $unit_data) {
                $schedule_date = $unit_data['date'];
                $schedule_year = getdate($schedule_date)['year'];
                $schedule_day = getdate($schedule_date)['yday'];

                if ($schedule_year != $curr_year) {
                    error_log('schedule date fail');
                }

                if ($schedule_day < $curr_day) {
                    $status_type = 'before';
                    $this->history_schedule_data[] = $this->handle_learning_status($status_type, $unit_data);
                } else {
                    //is current day
                    if ($curr_day === $schedule_day) {
                        $unit_data['current_day'] = true;
                        $status_type = 'current';
                        $this->current_schedule_data[] = $this->handle_learning_status($status_type, $unit_data);
                    } else {
                        $status_type = 'after';
                        $this->current_schedule_data[] = $this->handle_learning_status($status_type, $unit_data);
                    }
                }
            }
        }

        //find all today learning member
        $today_sql = "
            select user_id
             from wp_up_group_squad s ,wp_up_group_squad_detail sd
             where s.squad_id = sd.squad_id
             and s.group_id = '".bp_get_current_group_id()."'
             and sd.last_login_time > DATE_FORMAT(NOW(),'%Y-%m-%d')
        ";
        $today_user = $wpdb ->get_results($today_sql,ARRAY_A);

        if(is_array($today_user) && !empty($today_user)){
            foreach ($today_user as $user){

                $this -> today_users[] = array(
                    'avatar' => get_avatar($user['user_id']),
                    'nick_name' => get_user_meta($user['user_id'],'nickname',true)
                );
            }
        }


    }

    /**
     * handle case/question/lesson status
     *  完成question逻辑分析:
     *      需求是需要参与当天的问题讨论，在当天发布的问题下回复则算完成
     *
     *      训练营发布的每一条feedback都在 wp_bp_activity表中记录  component=group，type=activity_update则为训练营feedback  item_id则为group_id
     *      回复也是在wp_bp_activity中 component=activity，type=activity_comment 则为针对问题的回复 item_id 为 activity_id
     *
     *      so 设计为 在当天的schedule['question']中增加activity_id,查看当前用户是否又在当前feedback中回复为准
     *      则需要在后台将指定问题关联到当天的question中
     *
     *
     * schedule_status
     *  lock
     *  unfinished
     *  finished
     *  run_out
     *
     * @param $type
     * @param $unit_schedule
     */
    private function handle_learning_status($type, $unit_schedule)
    {
        global $wpdb;

        //the schedule is after
        if (!empty($type) && $type === 'after') {
            $unit_schedule['question']['status'] = 'lock';

            foreach ($unit_schedule['lesson'] as $lesson) {
                $lesson['status'] = 'lock';
            }

            foreach ($unit_schedule['case'] as $case) {
                $lesson['case'] = 'lock';
            }

            return $unit_schedule;
        }
        // default status settings
        $default_status = '';
        if ($type === 'current') {
            $default_status = 'unfinished';
        }

        if ($type === 'before') {
            $default_status = 'run_out';
        }

        //current user_id
        if (empty($this->current_user_id)) {
            error_log('runwise group schedule handle status user_id is empty');
            return;
        }

        //question status
        $question_id = $unit_schedule['question']['activity_id'];
        $question_status = $default_status;
        if (!empty($question_id)) {
            $question_sql = "select count(1) from wp_bp_activity 
                     where component = 'activity' and type =  'activity_comment'
                     and item_id = '" . $question_id . "'
                     and user_id = '" . $this->current_user_id . "'";
            $question_result = $wpdb->get_var($question_sql);
            if ($question_result > 0) {
                $question_status = 'finished';
            }
        } else {
            error_log('question 《' . $unit_schedule['question']['question_title'] . '》' . ' no find');
        }
        $unit_schedule['question']['status'] = $question_status;

        //lesson status  When building data in the admin manager page,  need the less_ID and course_ID
        foreach ($unit_schedule['lesson'] as &$lesson) {
            $lesson['status'] = $default_status;
            if (empty($lesson['course_id']) || empty($lesson['lesson_id'])) {
                continue;
            }

            if ($this->is_finished_lesson($this->current_user_id, $lesson['course_id'], $lesson['lesson_id'])) {
                $lesson['status'] = 'finished';
            }

        }
        unset($lesson);

        // case status
        foreach ($unit_schedule['case'] as &$case) {
            $case['status'] = $default_status;

            if ($this -> is_finished_case($this->current_user_id, $case['case_id'])) {
                $case['status'] = 'finished';
            }
        }

        unset($case);

        return $unit_schedule;
    }

    /**
     * @param $user_id
     * @param $case_id
     * @return bool
     */
    private function is_finished_case($user_id, $case_id)
    {
        $post_views = get_user_meta($user_id, '_up_post_views', true);
        if (!empty($post_views) && in_array($case_id, $post_views)) {
            return true;
        }
        return false;
    }


    /**
     * @param $user_id
     * @param $course_id
     * @param $lesson_id
     * @return bool
     */
    private function is_finished_lesson($user_id, $course_id, $lesson_id)
    {
        $user_meta = get_user_meta($user_id, '_sfwd-course_progress', true);
        $progress_status = $user_meta[$course_id]['lessons'][$lesson_id];
        if (!empty($progress_status) && $progress_status == '1') {
            return true;
        }
        return false;
    }
}