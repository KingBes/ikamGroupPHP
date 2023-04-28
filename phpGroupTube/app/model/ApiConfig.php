<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $key key
 * @property string $type api类型
 * @property integer $update_time 更新时间
 * @property string $val val
 */
class ApiConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_api_config';

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
     * 获取val function
     *
     * @param string $type
     * @param string $key
     * @return string
     */
    public static function val(string $type, string $key): string
    {
        $find = ApiConfig::where([
            ["type", '=', $type],
            ["key", '=', $key]
        ])->find();

        return $find->val;
    }
}
