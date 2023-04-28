<?php

namespace app\admin\controller;

use app\Request;
use app\common\Route;
use app\model\RobFriend;
use app\model\Group;

class IndexController
{
    #[Route(path: '/admin')]
    public function index(Request $request)
    {
        $api = sendEvent(["event" => "GetLoggedAccountList"]);
        if ($api["code"] == 0) {
            assign("data", $api["data"]);
        } else {
            return "可爱猫未开启~";
        }
        return view("index/index");
    }

    #[Route(path: '/admin/index/friends/{wxid}')]
    public function friends(Request $request, $wxid)
    {
        if ($request->method() == "POST") {
            $all = $request->all();
            $find = RobFriend::where("rob_wxid", $wxid)
                ->where("friend_wxid", $all["friend_wxid"])
                ->find();
            if ($find) {
                $arr = [
                    "id" => $find["id"]
                ];
                $res = RobFriend::update(
                    array_merge($all, $arr)
                );
                if ($res) {
                    return success("操作成功");
                } else {
                    return error("操作失败");
                }
            } else {
                return error("参数错误");
            }
        }

        $data = RobFriend::where("rob_wxid", $wxid)
            ->order("is_host", "desc")
            ->paginate(10);
        assign("rob_wxid", $wxid);
        assign("data", $data);
        return view("index/friends");
    }

    #[Route(path: '/admin/index/updateFriends')]
    public function updateFriends(Request $request)
    {
        if ($request->method() == "POST") {
            $all = $request->all();
            $res = sendEvent([
                "event" => "GetFriendList",
                "robot_wxid" => $all["rob_wxid"], //机器人
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
                    return success("操作成功");
                } else {
                    return error("操作失败");
                }
            }
        }
    }

    #[Route(path: '/admin/index/group/{wxid}')]
    public function group(Request $request, $wxid)
    {
        if ($request->method() == "POST") {
            $all = $request->all();
            if (isset($all["out_time"])) {
                $all["out_time"] = strtotime($all["out_time"]);
            }
            if (isset($all["pwd"]) && $all["pwd"] != "") {
                $all["pwd"] = password_hash($all["pwd"], PASSWORD_DEFAULT);
            }
            $res = Group::update($all);
            if ($res) {
                return success("操作成功");
            } else {
                return error("操作失败");
            }
        }
        assign("rob_wxid", $wxid);
        $data = Group::where("rob_wxid", $wxid)->paginate(10);
        assign("data", $data);

        return view("index/group");
    }

    #[Route(path: '/admin/index/updateGroup')]
    public function updateGroup(Request $request)
    {
        if ($request->method() == "POST") {
            $all = $request->all();
            $res = sendEvent([
                "event" => "GetGroupList",
                "robot_wxid" => $all["rob_wxid"],
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
                if ($state) {
                    return success("操作成功");
                } else {
                    return error("操作失败");
                }
            }
        }
    }
}
