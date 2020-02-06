<?php
/**
 * schedule template
 */

//build upskill schedule
require_once(WP_CONTENT_DIR. '/plugins/buddyboss-upskill/classes/upskill-shcedule.php');
$upskill_schedule = upskill_schedule();

wp_enqueue_style('upskill_schedule_style', PLUGIN_ROOT_PATH . '/css/schedule.css', array(), BUDDYBOSS_UPSKILL_VERSION, 'screen');
?>

<?php if (up_has_schedule()) { ?><!-- has schedule data -->
<body>
<div class="schedule-flex-wrap">
    <div class="step-wrap">
        <div class="unfinished-wrap">
            <?php if (!empty($upskill_schedule->current_schedule_data)) { ?><!-- has current_schedule_data data -->


            <?php foreach ($upskill_schedule->current_schedule_data as $curr_schedule_item) { ?>    <!--while unfinished schedule -->


                <div class="step-item">
                    <div class="item-head">
                        <p>DAY <?= $curr_schedule_item['day_num'] ?></p>
                        <span><?= date('m-d', $curr_schedule_item['date']) . ($curr_schedule_item['current_day'] ? '，今天' : '') ?></span>

                        <?php if ($curr_schedule_item['current_day']) { ?>

                            <div class="learning-wrap">
                                <ul>
                                <?php  foreach ($upskill_schedule -> today_users as $user) {?>
                                    <li>
                                        <a href="javascript:void(0)" target="_blank" class="bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="<?= $user['nick_name'] ?>">
                                            <?= $user['avatar'] ?>
                                        </a>
                                    </li>
                            <?php }?>
                                </ul>
                            </div>
                            
                        <?php } ?>

                    </div>
                    <div class="item-content item-full-content">
                        <div class="title">问题</div>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($curr_schedule_item['question']['status']) ?>"></span>
                                <a href="<?= $curr_schedule_item['question']['question_link'] ?>"
                                   title=""><?= $curr_schedule_item['question']['question_text'] ?></a>
                            </div>
                        </div>
                    </div>


                    <?php foreach ($curr_schedule_item['lesson'] as $less_item) { ?><!--lesson foreach-->
                    <div class="item-content item-half-content">
                        <div class="title">微课</div>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($less_item['status']) ?>"></span>
                                <a href="<?= $less_item['link'] ?>" title=""><?= $less_item['title'] ?></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?><!--lesson foreach-->

                    <?php foreach ($curr_schedule_item['case'] as $case_item) { ?><!--case foreach-->
                    <div class="item-content item-half-content">
                        <div class="title">案例</div>
                        <span hidden="hidden" ><?= $case_item['case_id']?></span>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($case_item['status']) ?> "></span>
                                <a href="<?= $case_item['link'] ?>" title=""><?= $case_item['title'] ?></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?><!--case foreach-->
                </div>


            <?php } ?> <!--while unfinished schedule -->

            <?php } ?><!-- has current_schedule_data data -->
        </div>

        <?php if (!empty($upskill_schedule->history_schedule_data)) { ?><!-- has history_schedule_data data -->
        <!-- unfinished schedule -->
        <div class="finished-wrap">
            <div class="title">历史日程</div>
            <?php foreach ($upskill_schedule->history_schedule_data as $history_schedule_item) { ?>    <!--while history schedule -->
                <div class="step-item">
                    <div class="item-head">
                        <p>DAY <?= $history_schedule_item['day_num'] ?></p>
                        <span><?= date('m-d', $history_schedule_item['date']) . ($history_schedule_item['current_day'] ? '，今天' : '') ?></span>
                    </div>
                    <div class="item-content item-full-content">
                        <div class="title">问题</div>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($history_schedule_item['question']['status']) ?>"></span>
                                <a href="<?= $history_schedule_item['question']['question_link'] ?>"
                                   title=""><?= $history_schedule_item['question']['question_text'] ?></a>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($history_schedule_item['lesson'] as $less_item) { ?><!--lesson foreach-->
                    <div class="item-content item-half-content">
                        <div class="title">微课</div>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($less_item['status']) ?>"></span>
                                <a href="<?= $less_item['link'] ?>" title=""><?= $less_item['title'] ?></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?><!--lesson foreach-->

                    <?php foreach ($history_schedule_item['case'] as $case_item) { ?><!--case foreach-->
                    <div class="item-content item-half-content">
                        <div class="title">案例</div>
                        <span hidden="hidden" ><?= $case_item['case_id']?></span>
                        <div class="content">
                            <div class="list-one">
                                <span class="<?= get_finished_class($case_item['status']) ?>"></span>
                                <a href="<?= $case_item['link'] ?>" title=""><?= $case_item['title'] ?></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?><!--case foreach-->
                </div>
            <?php } ?> <!--while history schedule -->

        </div>

        <?php } ?><!-- has history_schedule_data data -->


    </div>

    <div class="team-aside">

        <!-- 训练营看板 start-->
        <div class="team-board">
            <div class="board-head">
                <p>训练营看板</p>
            </div>
            <div class="board-content">
                <div class="title">
                    <span class="titlespan"></span>
                    <p><?php echo $upskill_schedule->group_target ?></p>
                </div>
                <span>举办时间：<?php echo $upskill_schedule->group_begin_time ?> 至 <?php echo $upskill_schedule->group_end_time ?></span>
                <span>参与人数：共24人</span>
            </div>
        </div>
        <!-- 训练营看板 end-->

        <!-- 我的看板 start-->
        <div class="my-board">
            <div class="board-head">
                <p>我的看板</p>
            </div>
            <div class="board-content">
                <div class="my-information">
                    <div class="my-title">
                        <?php echo get_avatar($upskill_schedule->current_user_id, 50);?>
                        <p><?php echo  get_user_meta($upskill_schedule->current_user_id,'nickname',true)?></p>
                        <span>B组</span>
                    </div>
                    <div class="my-score">
                        <div class="total-score">
                            <div class="name">
                                <p>总积分</p>
                                <img id="rules" src="<?php echo PLUGIN_ROOT_PATH.'/images/jifen.png'?>" alt="积分规则">
                                <!-- 积分规则S -->
                                <div class="integral-wrap">
                                    <div class="integral-mask"></div>
                                    <div class="title">
                                        <span>刷分指南</span>
                                    </div>
                                    <div class="content">
                                        <div class="part">
                                            <div class="part-name">
                                                <p>1. 积分计划简介</p>
                                            </div>
                                            <div class="part-desc">
                                                <p>用户在使用即能训练营时将根据各类行为获得相应的积分奖励，可用于体现你的学习进度和掌握情况，以及对所属小组的贡献情况，以得到更好的排名，最终获得相应对的奖励。</p>
                                            </div>
                                        </div>
                                        <div class="part">
                                            <div class="part-name">
                                                <p>2. 积分开通规则</p>
                                            </div>
                                            <div class="part-desc">
                                                <p>可通过完成训练营内的下列行为，获取相应的积分：</p>
                                                <div class="desc-table">
                                                    <span>完成课节</span>
                                                    <span class="font-orange">10学习分</span>
                                                </div>
                                                <div class="desc-table">
                                                    <span>完成练习</span>
                                                    <span class="font-orange">5学习分</span>
                                                </div>
                                                <div class="desc-table">
                                                    <span>完成课后测练</span>
                                                    <span class="font-orange">30学习分</span>
                                                </div>
                                                <div class="desc-table">
                                                    <span>回复动态信息(问题)</span>
                                                    <span class="font-orange">10互动分</span>
                                                </div>
                                                <div class="desc-table">
                                                    <span>获得点赞</span>
                                                    <span class="font-orange">1互动分</span>
                                                </div>
                                                <div class="desc-table">
                                                    <span>评论案例/博文</span>
                                                    <span class="font-orange">3互动分</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 积分规则E -->
                            </div>
                            <div class="values">
                                <p><?php echo ($upskill_schedule -> user_learning_integral[$upskill_schedule->current_user_id] + $upskill_schedule -> user_practice_integral[$upskill_schedule->current_user_id])?></p>
                            </div>
                        </div>
                        <div class="branch-score">
                            <div class="learn-score">
                                <p>学习分：<span><?php echo $upskill_schedule -> user_learning_integral[$upskill_schedule->current_user_id]?></span>/85</p>
                            </div>
                            <div class="interact-score">
                                <p>互动分：<span><?php echo $upskill_schedule -> user_practice_integral[$upskill_schedule->current_user_id]?></span>/64</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group-progress">
                    <div class="my-percent progress-bar">
                        <div class="label">我的进度：</div>
                        <div class="progress">
                            <div class="base-progress"></div>
                            <div class="active-progress" style="width: <?php get_my_progress()?>"></div>
                        </div>
                        <div class="percent"><?php get_my_progress()?></div>
                    </div>
                    <div class="total-percent progress-bar">
                        <div class="label">全员进度：</div>
                        <div class="progress">
                            <div class="base-progress"></div>
                            <div class="active-progress" style="width: <?php get_all_progress()?>"></div>
                        </div>
                        <div class="percent"><?php get_all_progress();?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 我的看板 end-->

        <!-- 小组排行榜 start -->
        <div class="list-board">
            <div class="board-head">
                <p>小组排行榜</p>
            </div>
            <div class="board-content">
                <div class="list-table-head">
                    <span>排名</span>
                    <span>组/成员</span>
                    <span>学习分</span>
                    <span>互动分</span>
                    <span>总分</span>
                </div>
                <div class="list-table-body">
                    <div class="list-icon table-item">
                        <span></span>
                    </div>
                    <div class="list-number table-item">
                        <span>1</span>
                    </div>
                    <div class="list-members table-item">
                        <span>A组</span>
                    </div>
                    <div class="list-learn-score table-item">
                        <span>188</span>
                    </div>
                    <div class="list-interact-score table-item">
                        <span>97</span>
                    </div>
                    <div class="list-total-score table-item">
                        <span>285</span>
                    </div>
                    <div class="members-list">
                        <div class="list-item">
                            <span>1</span>
                            <span><img src="user.png" alt="头像"></span>
                            <span>28</span>
                            <span>20</span>
                            <span>48</span>
                        </div>
                        <div class="list-item">
                            <span>2</span>
                            <span><img src="user.png" alt="头像"></span>
                            <span>28</span>
                            <span>20</span>
                            <span>48</span>
                        </div>
                        <div class="list-item">
                            <span>3</span>
                            <span><img src="user.png" alt="头像"></span>
                            <span>28</span>
                            <span>20</span>
                            <span>48</span>
                        </div>
                        <div class="list-item">
                            <span>4</span>
                            <span><img src="user.png" alt="头像"></span>
                            <span>28</span>
                            <span>20</span>
                            <span>48</span>
                        </div>

                    </div>
                </div>
                <div class="list-table-body">
                    <div class="list-icon table-item">
                        <span></span>
                    </div>
                    <div class="list-number table-item">
                        <span>2</span>
                    </div>
                    <div class="list-members table-item">
                        <span>B组</span>
                    </div>
                    <div class="list-learn-score table-item">
                        <span>188</span>
                    </div>
                    <div class="list-interact-score table-item">
                        <span>97</span>
                    </div>
                    <div class="list-total-score table-item">
                        <span>285</span>
                    </div>
                </div>
                <div class="list-table-body">
                    <div class="list-icon table-item">
                        <span></span>
                    </div>
                    <div class="list-number table-item">
                        <span>3</span>
                    </div>
                    <div class="list-members table-item">
                        <span>C组</span>
                    </div>
                    <div class="list-learn-score table-item">
                        <span>188</span>
                    </div>
                    <div class="list-interact-score table-item">
                        <span>97</span>
                    </div>
                    <div class="list-total-score table-item">
                        <span>285</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- 小组排行榜 end -->
    </div>
    <!-- 个人积分记录S -->
    <div class="integral-record">
        <div class="record-mask"></div>
        <div class="record-content">
            <div class="title">
                <span>积分记录</span>
                <span class="close-btn"></span>
            </div>
            <div class="content">
                <div class="total">
                    <span class="font-normal">总积分</span>
                    <p>10</p>
                </div>
                <div class="list">
                    <div class="list-head">
                        <span>时间</span>
                        <span>操作</span>
                        <span>积分</span>
                    </div>
                    <div class="list-ul">
                        <!-- <ul>
                            <li>
                                <span class="font-normal">2020-01-10 18:30</span>
                                <span class="font-black">完成课节</span>
                                <span class="font-orange">+10 学习分</span>
                            </li>
                            <li>
                                <span class="font-normal">2020-01-10 18:30</span>
                                <span class="font-black">完成课节</span>
                                <span class="font-orange">+10 学习分</span>
                            </li>
                            
                        </ul> -->
                        <div class="empty-list">
                            <span class="font-normal">暂无积分记录</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 个人积分记录E -->
</div>
</body>
<?php }else{ ?><!-- has schedule data -->
<h1>暂无日程安排，请与联系</h1>
<?php } ?><!-- has schedule data -->
<script src="https://cdn.bootcss.com/jquery/1.12.0/jquery.js"></script>
<?php wp_enqueue_script('upskill_schedule_js');;?>