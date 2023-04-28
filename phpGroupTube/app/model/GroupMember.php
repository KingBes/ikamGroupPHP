<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $group_wxid 群id
 * @property string $member_wxid 成员wxid
 * @property string $wx_num 微信号
 * @property string $group_nickname 群昵称
 * @property integer $sex 0未知1男2女
 * @property string $diamond 钻石
 * @property integer $cash 现金
 * @property integer $is_black 0正常1小黑屋
 * @property integer $is_owner 0不是群主1是群主
 * @property integer $is_admin 0普通1管理
 * @property integer $is_out_group 0正常1退群
 * @property integer $out_group_time 退群时间
 * @property integer $come_group_time 进群时间
 */
class GroupMember extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_group_member';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'come_group_time';
    protected $updateTime = 'out_group_time';
}
