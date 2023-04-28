<?php

namespace app\api\tool\group;

use app\model\GroupMember;
use app\common\GroupConfig;
use app\model\RobFriend;
use app\model\Group;

#[GroupConfig(deploy: 0)]
class Base
{
    private $data;

    public function __construct()
    {
        $this->data = task()->param;
    }

    /**
     * 入口 function
     *
     * @return void
     */
    public static function entry()
    {
        $base = new self();
        $base->open();
        $base->isState();
        $base->isBlack();
        $base->upData();
        $base->fun();
        $base->group();
        $base->msg();
    }

    /**
     * 开启群 function
     *
     * @return void
     */
    private  function open()
    {
        $data = $this->data;
        if (in_array(trimall($data["msg"]), ["开鸡", "关鸡"])) {
            $state = trimall($data["msg"]) == "开鸡" ? 1 : 0;
            $find = RobFriend::where("friend_wxid", $data["final_from_wxid"])->find();
            if ($find && $find["is_host"] == 1) {
                $Group = Group::edit($data["robot_wxid"]);
                if ($Group) {
                    $res = Group::where("group_wxid", $data["from_wxid"])->update([
                        "state" => $state
                    ]);
                    if ($res) {
                        sendMsg("SendTextMsg", "操作成功");
                    } else {
                        sendMsg("SendTextMsg", "操作失败");
                    }
                } else {
                    sendMsg("SendTextMsg", "接口失败");
                }
            }
        }
    }

    /**
     * 判断是否开启群 function
     *
     * @return boolean
     */
    private  function isState()
    {
        $data = $this->data;
        $res = Group::where("group_wxid", $data["from_wxid"])->value("state");
        if ($res != 1) {
            echo $res . "该群已关闭~\n";
            throw new \Exception('该群已关闭');
        }
    }

    /**
     * 更新信息 function
     *
     * @return void
     */
    private  function upData()
    {
        $data = $this->data;
        if ($data["msg"] == "更新信息") {
            $api = sendEvent([
                "event" => "GetGroupMemberList",
                "robot_wxid" => $data["robot_wxid"],
                "group_wxid" => $data["from_wxid"],
                "msg" => "1"
            ]);
            if ($api["code"] == 0) {
                $state = true;
                foreach ($api["data"] as $k => $v) {
                    $find = GroupMember::where([
                        ["group_wxid", '=', $data["from_wxid"]],
                        ["member_wxid", '=', $v["wxid"]]
                    ])->find();
                    if ($find) {
                        $arr = [
                            'id' => $find['id'],
                            "group_nickname" => $v["nickname"],
                            "sex" => $v["sex"],
                            "headimgurl" => $v["headimgurl"]
                        ];
                        if ($v["wx_num"] != "") {
                            $arr["wx_num"] = $v["wx_num"];
                        }
                        if (isset($v["identity"])) {
                            $arr["is_owner"] = $v["identity"];
                        }
                        $edit = GroupMember::update($arr);
                        if (!$edit) {
                            $state = false;
                        }
                    } else {
                        $arr = [
                            "group_wxid" => $data["from_wxid"],
                            "member_wxid" => $v["wxid"],
                            "group_nickname" => $v["nickname"],
                            "sex" => $v["sex"],
                            "headimgurl" => $v["headimgurl"]
                        ];
                        if ($v["wx_num"] != "") {
                            $arr["wx_num"] = $v["wx_num"];
                        }
                        if (isset($v["identity"])) {
                            $arr["is_owner"] = $v["identity"];
                        }
                        $add = GroupMember::create($arr);
                        if (!$add) {
                            $state = false;
                        }
                    }
                }
                if ($state) {
                    sendMsg("SendTextMsg", "更新成功");
                } else {
                    sendMsg("SendTextMsg", "更新失败");
                }
            } else {
                sendMsg("SendTextMsg", "接口失败");
            }
        }
    }

    /**
     * 小黑屋 function
     *
     * @return boolean
     */
    private  function isBlack()
    {
        $data = $this->data;
        if ($data["member_info"]["is_black"] == 1) {
            throw new \Exception('此人在小黑屋');
        }
    }

    /**
     * 功能 function
     *
     * @return void
     */
    private  function fun()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "功能") {
            $toolFile = get_file_php('\\api\\tool\\group');
            $msg = '';
            $i = 0;

            foreach ($toolFile as $k => $v) {
                $ReflectionClass = new \ReflectionClass("app\\api\\tool\\group\\" . $v);
                $info = $ReflectionClass->getAttributes()[0]->getArguments();
                // print_r($info);
                if (!isset($info["deploy"]) || $info["deploy"] != 0) {
                    if (isset($info["switch"]) && $info["switch"] == 1) {
                        if (($i + 1) % 2 == 0) {
                            $msg = $msg . $info['title'] . "[@emoji=" . strtolower($info['icon']) . "]\r";
                        } else {
                            $msg = $msg . "[@emoji=" . strtolower($info['icon']) . "]" . $info['title'] . '[@emoji=\ue118]';
                        }
                        $i++;
                    }
                }
            }

            sendMsg(
                "SendTextMsg",
                "======功能菜单======\r" .
                    trim($msg) .
                    "\r==================\r" .
                    '[@emoji=\ue443]' . "刷新信息：更新信息\r" .
                    '[@emoji=\ue443]' . "群信息指令：社  区\r" .
                    '[@emoji=\ue443]' . "公众号搜索：AI乐情"
            );
        }
    }

    /**
     * 社区 function
     *
     * @return void
     */
    private function group()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "社区") {
            $Group = Group::where("group_wxid", $data["from_wxid"])->find();
            $pic = $Group["headimgurl"];
            if ($pic == "") {
                $pic = "https://www.kllxs.top/files/20230325/641e949fd47ea.jpg";
            }
            sendMsg("SendLinkMsg", [
                "title" => "「" . $Group["nickname"] . "」社区", //链接标题
                "text" => "关注公众号：AI乐情\n博客：https://www.kllxs.top/\n时间：" . date("H:i:s"), //链接简述
                "target_url" => "http://" . MineIp . "/group/index/" . $Group['id'], //链接
                "pic_url" => $pic, //链接图标
                "icon_url" => $pic, //链接图标
            ]);
        }
    }

    /**
     * 发表次数 function
     *
     * @return void
     */
    private  function msg()
    {
        $data = $this->data;
        $find = GroupMember::find($data["member_info"]["id"]);
        if ($find) {
            $arr = [
                "id" => $find["id"]
            ];
            if (date("Ymd") == date("Ymd", timeConvert($find["out_group_time"]))) {
                $arr["msg_day_num"] = $find["msg_day_num"] + 1;
            } else {
                $arr["msg_day_num"] = 1;
            }
            GroupMember::inc("msg_num")->update($arr);
        }
    }
}
