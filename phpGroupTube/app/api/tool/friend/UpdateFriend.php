<?php

namespace app\api\tool\friend;

use app\common\BaseMsg;
use app\model\RobFriend;

class UpdateFriend
{
    /**
     * 更新好友 function
     *
     * @return void
     */
    public static function update()
    {
        $data = task()->param;
        if ($data['msg'] == "更新好友") {
            $res = sendEvent([
                "event" => "GetFriendList",
                "robot_wxid" => $data["robot_wxid"], //机器人
                "msg" => "1,0"
            ]);
            if ($res["code"] == 0) {
                $state = true;
                foreach ($res["data"] as $k => $v) {
                    $find = RobFriend::where([
                        ["rob_wxid", '=', $v["robot_wxid"]],
                        ["friend_wxid", '=', $v["wxid"]]
                    ])->find();
                    if ($find) {
                        $update = RobFriend::update([
                            "id" => $find,
                            "headimgurl" => $v["headimgurl"],
                            "note" => $v["note"],
                            "sex" => $v['sex']
                        ]);
                        if (!$update) {
                            $state = false;
                        }
                    } else {
                        $add = RobFriend::create([
                            "rob_wxid" => $v["robot_wxid"],
                            "headimgurl" => $v["headimgurl"],
                            "nickname" => $v["nickname"],
                            "sex" => $v["sex"],
                            "wx_num" => $v["wx_num"],
                            "note" => $v["note"],
                            "friend_wxid" => $v["wxid"]
                        ]);
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

        if (trimall($data["msg"]) == "国旗") {
            sendMsg("SendImageMsg", [
                "name" => "国旗测试.png", //本地 会存到本地
                "url" => "http://img2.xixik.net/custom/section/country-flag/xixik-60b3466fac6a8109.gif", //网络地址
            ]);
        }
    }
}
