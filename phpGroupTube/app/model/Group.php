<?php

namespace app\model;

use think\Model;

/**
 * @property integer $id 主键(主键)
 * @property string $rob_wxid 机器人wxid
 * @property string $group_wxid 群wxid
 * @property string $headimgurl 头像
 * @property string $nickname 群名
 * @property integer $member_count 群人数
 * @property integer $state 0关群1开群
 * @property integer $out_time 到期时间
 * @property integer $update_time 更新时间
 */
class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kllxs_group';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    // 定义时间戳字段名
    protected $createTime = 'update_time';
    protected $updateTime = 'update_time';


    public static function edit($rob_wxid)
    {
        $res = sendEvent([
            "event" => "GetGroupList",
            "robot_wxid" => $rob_wxid,
            "msg" => "1"
        ]);
        if ($res["code"] == 0) {
            $state = true;
            foreach ($res["data"] as $k => $v) {
                $find = Group::where([
                    ["rob_wxid", '=', $v["robot_wxid"]],
                    ["group_wxid", '=', $v["wxid"]]
                ])->find();
                if ($find) {
                    $update = Group::update([
                        "id" => $find,
                        "headimgurl" => $v["headimgurl"],
                        "nickname" => $v["nickname"],
                        "member_count" => $v["memberCount"],
                        "isManager" => $v["isManager"]
                    ]);
                    if (!$update) {
                        $state = false;
                    }
                } else {
                    $add = Group::create([
                        "rob_wxid" => $v["robot_wxid"],
                        "headimgurl" => $v["headimgurl"],
                        "nickname" => $v["nickname"],
                        "member_count" => $v["memberCount"],
                        "group_wxid" => $v["wxid"],
                        "isManager" => $v["isManager"]
                    ]);
                    if (!$add) {
                        $state = false;
                    }
                }
            }
            return $state;
        }
    }
}
