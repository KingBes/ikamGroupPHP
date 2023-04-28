<?php

namespace app\group\controller;

use app\Request;
use app\common\Route;
use app\model\GroupConfig;
use app\model\Group;

class ToolController
{
    #[Route(path: '/group/tool/{id}')]
    public function edit(Request $request, $id)
    {
        $group = Group::find($id);
        $path = app_path('api/tool/group');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $data = [];
        foreach ($iterator as $file) {
            /** 忽略目录和非php文件 */
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }
            $file_path = str_replace('\\', '/', $file->getPathname());
            $class_name = str_replace('/', '\\', substr(substr($file_path, strlen(base_path())), 0, -4));
            $data[] = configInfo($class_name, $group["group_wxid"]);
        }
        assign("data", $data);

        if ($request->method() == "POST") {
            $all = $request->post();
            // print_r($all);
            $res = GroupConfig::edit($all, $group["group_wxid"]);
            if ($res) {
                return success("操作成功");
            } else {
                return error("操作失败");
            }
        }

        return view("tool/index");
    }
}
