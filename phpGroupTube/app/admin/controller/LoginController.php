<?php

namespace app\admin\controller;

use app\Request;
use app\common\Route;

class LoginController
{
    #[Route(path: '/admin/login/log')]
    public function log(Request $request)
    {
        $username = "wechat";
        $pwd = password_hash("wechat", PASSWORD_DEFAULT);

        if ($request->method() == "POST") {
            $all = $request->all();
            if ($all["username"] === $username && password_verify($all["pwd"], $pwd)) {
                session(["adminUser" => true]);
                return success("登录成功", "admin");
            } else {
                return error("账号或密码错误");
            }
        }

        return view("login/log");
    }
}
