<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property mixed $json_val json内容
 * @property mixed $str_val 字符串内容
 * @property string $type 类型
 * @property integer $update_time 更新时间
 */
class Token extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_token';

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
