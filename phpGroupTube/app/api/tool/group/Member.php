<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupMember;
use app\model\Group;

#[GroupConfig(
    title: "成员事件",
    icon: "\uD83E\uDDD1\uD83C\uDFFB",
    config: [],
    switch: 1
)]
class Member
{
    private $data;
    private $info;

    public function __construct()
    {
        $this->data = task()->param;
        $this->info = configInfo(__CLASS__, $this->data["from_wxid"]);
    }

    /**
     * 入口 function
     *
     * @return void
     */
    public static function entry()
    {
        $demo = new self();
        if ($demo->info["switch"] != 0) {
            $demo->index();
            $demo->msg();
        }
    }

    /**
     * 事件 function
     *
     * @return void
     */
    private function index()
    {
        $data = $this->data;
        $Group = Group::where("group_wxid", $data["msg"]["group_wxid"])->find();
        if ($data["event"] == "EventGroupMemberDecrease") //成员减少
        {
            $find = GroupMember::where([
                ["group_wxid", "=", $data["msg"]["group_wxid"]],
                ["member_wxid", "=", $data["msg"]["member_wxid"]]
            ])->find();
            if ($find) {
                $res = GroupMember::update([
                    "id" => $find["id"],
                    "is_out_group" => 1
                ]);
                $pic =  $find["headimgurl"];
                if ($pic == "") {
                    $pic = $Group["headimgurl"];
                }
                if ($res) {
                    sendMsg("SendLinkMsg", [
                        "title" => "「" . $data["msg"]["group_name"] . "」消息", //链接标题
                        "text" => "成员退群通知\n" . "昵称：" . $data["msg"]["member_nickname"] . "\n" . "时间：" . date("H:i:s", $data["msg"]["timestamp"]), //链接简述
                        "target_url" => "https://" . MineIp . "/group/index/" . $Group['id'], //链接
                        "pic_url" => $pic, //链接图标
                        "icon_url" => $pic, //链接图标
                    ]);
                } else {
                    sendMsg("SendTextMsg", "成员退群信息报错");
                }
            } else {
                sendMsg("SendTextMsg", "请发：更新信息");
            }
        }


        if ($data["event"] == "EventGroupMemberAdd") {
            /* $find = GroupMember::where([
                ["group_wxid", "=", $data["msg"]["guest"]["group_wxid"]],
                ["member_wxid", "=", $data["msg"]["guest"]["member_wxid"]]
            ])->find(); */
            foreach ($data["msg"]["guest"] as $k => $v) {
                $find = GroupMember::where([
                    ["group_wxid", '=', $data["msg"]["group_wxid"]],
                    ["member_wxid", "=", $v["wxid"]]
                ])->value("id");
                if ($find) {
                    $res = GroupMember::update([
                        "id" => $find,
                        "group_nickname" => $v["nickname"],
                        "headimgurl" => $v["headimgurl"]
                    ]);
                } else {
                    $res = GroupMember::create([
                        "group_wxid" => $data["msg"]["group_wxid"],
                        "member_wxid" => $v["wxid"],
                        "group_nickname" => $v["nickname"],
                        "headimgurl" => $v["headimgurl"]
                    ]);
                }
                if ($res) {
                    $pic =  $v["headimgurl"];
                    if ($pic == "") {
                        $pic = $Group["headimgurl"];
                    }
                    sendMsg("SendLinkMsg", [
                        "title" => "「" . $data["msg"]["group_name"] . "」消息", //链接标题
                        "text" => "成员进群通知\n" .
                            "新人昵称：" . $v["nickname"] . "\n" .
                            "邀请人：" . $data["msg"]["inviter"]["nickname"], //链接简述
                        "target_url" => "http://" . MineIp . "/group/index/" . $Group['id'], //链接
                        "pic_url" => $pic, //链接图标
                        "icon_url" => $pic, //链接图标
                    ]);
                } else {
                    sendMsg("SendTextMsg", "成员进群信息报错");
                }
            }
        }
    }

    /**
     * 成员事件 function
     *
     * @return void
     */
    private function msg()
    {
        $data = $this->data;
        if ($data["msg"] == "成员事件") {
            sendMsg(
                "SendTextMsg",
                "群成员增加减少提示"
            );
        }
    }
}
