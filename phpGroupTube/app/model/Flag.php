<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $link 链接
 * @property string $name 名称
 * @property integer $update_time 更新时间
 */
class Flag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_flag';

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
