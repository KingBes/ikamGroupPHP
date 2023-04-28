<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $name 名
 * @property string $val 值
 * @property integer $update_time 更新时间
 */
class GroupConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_group_config';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'update_time';

    /**
     * 编辑或添加 function
     *
     * @param array $data
     * @return boolean
     */
    public static function edit(array $data, string $group): bool
    {
        $state = true;
        foreach ($data as $k => $v) {
            $find = GroupConfig::where("name", $k)->where("group_wxid", $group)->find();
            if ($find) {
                $res = GroupConfig::update([
                    "id" => $find["id"],
                    "val" => $v
                ]);
                if (!$res) {
                    $state = false;
                }
            } else {
                $res = GroupConfig::create([
                    "group_wxid" => $group,
                    "name" => $k,
                    "val" => $v
                ]);
                if (!$res) {
                    $state = false;
                }
            }
        }
        return $state;
    }
}
