<?php

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Admin implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        $adminUser = session("adminUser");
        if (!$adminUser && $request->action != "log") {
            return redirect(route("admin.login.log"));
        }
        return $handler($request);
    }
}
