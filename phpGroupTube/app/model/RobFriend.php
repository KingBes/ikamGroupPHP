<?php

namespace app\model;

use think\Model;

/**
 * @property string $friend_wxid 好友id
 * @property string $headimgurl 头像
 * @property integer $id 主键(主键)
 * @property integer $is_host 0正常1主人
 * @property string $nickname 昵称
 * @property string $note 备注
 * @property string $rob_wxid 机器人id
 * @property integer $sex 0未知1男2女
 * @property integer $update_time 更新时间
 * @property string $wx_num 微信号
 */
class RobFriend extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_rob_friend';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'update_time';

    
}
