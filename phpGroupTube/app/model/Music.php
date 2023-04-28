<?php

namespace app\model;

use think\Model;

/**
 * @property integer $add_date 添加时间
 * @property string $author 作者
 * @property integer $id 主键(主键)
 * @property string $img 图片
 * @property mixed $is_like 0不喜欢1我
 * @property string $link 链接
 * @property string $lyric 歌词
 * @property string $mp3 播放地址
 * @property string $name 关键词
 * @property integer $rep_date 更新时间
 * @property string $title 标题
 * @property string $url 路径
 */
class Music extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_music';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'add_date';
    protected $updateTime = 'rep_date';
}
