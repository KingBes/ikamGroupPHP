<?php

namespace app\group\controller;

use app\Request;
use app\common\Route;
use app\model\Group;

class IndexController
{
    #[Route(path: '/group/index/{id}')]
    public function index(Request $request, $id)
    {
        return view("index/index");
    }

    #[Route(path: '/group/log/{id}')]
    public function log(Request $request, $id)
    {
        $group = Group::find($id);
        if ($request->method() == "POST") {
            $pwd = $request->post('pwd');
            if ($group["pwd"] == "") {
                return error("该群还没设密码，请联系客服");
            }
            if (password_verify($pwd, $group["pwd"])) {
                session(["groupAdmin" => $group]);
                return success("登录成功", "/group/index/{$id}");
            } else {
                return error("密码错误");
            }
        }
        return view("index/log");
    }
}
