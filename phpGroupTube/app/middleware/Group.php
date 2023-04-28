<?php

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use app\model\Group as mod;

class Group implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $param = $request->route->param();
        $Group = mod::find($param['id']);
        assign("group", $Group);
        $groupAdmin = session("groupAdmin");
        if (!$groupAdmin) {
            if (in_array($request->action, ["edit"])) {
                return error("请先登录", "/group/log/" . $Group["id"]);
            }
        } else {
            if (in_array($request->action, ["edit"]) && ($Group['id'] != $groupAdmin["id"])) {
                return error("请先登录", "/group/log/" . $Group["id"]);
            }
        }
        return $handler($request);
    }
}
