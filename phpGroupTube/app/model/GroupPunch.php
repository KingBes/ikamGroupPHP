<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property integer $total 总打卡次数
 * @property integer $ranking 当天排名
 * @property integer $series 连续打卡次数
 * @property string $diamond 获得钻石
 * @property integer $cash 获得现金
 * @property integer $update_time 打卡时间
 */
class GroupPunch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_group_punch';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'update_time';

    public static function onBeforeWrite(object $info)
    {
        $data = $info->toArray();
        GroupMember::inc('cash', $data["cash"])
            ->inc('diamond', $data["diamond"])
            ->update(['id' => $data['id']]);
    }
}
