<?php

namespace app\group\controller;

use app\Request;
use app\common\Route;
use app\model\Group;
use app\model\GroupMember;

class UserController
{
    #[Route(path: '/group/user/index/{id}')]
    public function index(Request $request, $id)
    {
        $group = Group::find($id);
        $outs = $request->get("outs");
        if (isset($outs)) {
            $outs = $outs;
        } else {
            $outs = 0;
        }
        $data = GroupMember::where("group_wxid", $group['group_wxid'])
            ->where("is_out_group", $outs)
            ->order("is_admin", 'desc')
            ->paginate(10);
        assign("data", $data);
        assign("outs", $outs);
        return view("user/index");
    }

    #[Route(path: '/group/user/edit/{id}')]
    public function edit(Request $request, $id)
    {
        if ($request->method() == "POST") {
            $all = $request->post();
            $res = GroupMember::update($all);
            if ($res) {
                return success("操作成功");
            } else {
                return error("操作失败");
            }
        }
    }
}
