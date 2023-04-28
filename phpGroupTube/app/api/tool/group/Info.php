<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupMember;

#[GroupConfig(
    title: "信息查询",
    icon: "\uD83C\uDFAB",
    config: [],
    switch: 1
)]
class Info
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
        $mine = new self();
        if ($mine->info["switch"] != 0) {
            $mine->mine();
            $mine->you();
            $mine->msg();
        }
    }

    /**
     * 我的信息 function
     *
     * @return void
     */
    private function mine()
    {
        $data = $this->data;
        if (in_array(trimall($data['msg']), ["我的名片", "个人信息", "我的信息", "名片"]) && count($data["at_list"]) == 0) {
            $find = $data["member_info"];
            switch ($find["sex"]) {
                case 0:
                    $find["sex"] = "未知";
                    break;
                case 1:
                    $find["sex"] = "男";
                    break;
                default:
                    $find["sex"] = "女";
            }
            $is_admin = $find["is_admin"] == 1 ? "管理" : "成员";
            sendMsg(
                "SendTextMsg",
                $data["final_from_name"] . "\n" .
                    "╭┈┈┈┈[@emoji=\uD83C\uDFAB]名片[@emoji=\uD83C\uDFAB]┈┈┈┈╮\n" .
                    "[@emoji=\ue151]性别：" . $find["sex"] . "\n" .
                    "[@emoji=\uD83D\uDCB5]银票：" . formatMoney($find["cash"]) . "\n" .
                    "[@emoji=\ue035]钻石：" . $find["diamond"] . " 克\n" .
                    "[@emoji=\uD83D\uDC96]魅力：" . $find["charm"] . "\n" .
                    "[@emoji=\ue152]身份：" . $is_admin . "\n" .
                    "[@emoji=\ue32e]称号：无\n" .
                    "[@emoji=\ue428]婚姻：未婚\n" .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    /**
     * 查信息 function
     *
     * @return void
     */
    private function you()
    {
        $data = $this->data;
        if (in_array(
            trimall($data['msg']),
            ["查信息", "名片"]
        ) && count($data["at_list"]) == 1) {
            $find = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]],
                ["member_wxid", '=', $data["at_list"][0]["wxid"]]
            ])->find();
            if ($find) {
                switch ($find["sex"]) {
                    case 0:
                        $find["sex"] = "未知";
                        break;
                    case 1:
                        $find["sex"] = "男";
                        break;
                    default:
                        $find["sex"] = "女";
                }
                $is_admin = $find["is_admin"] == 1 ? "管理" : "成员";
                sendMsg(
                    "SendTextMsg",
                    $find["group_nickname"] . "\n" .
                        "╭┈┈┈┈[@emoji=\uD83C\uDFAB]名片[@emoji=\uD83C\uDFAB]┈┈┈┈╮\n" .
                        "[@emoji=\ue151]性别：" . $find["sex"] . "\n" .
                        "[@emoji=\uD83D\uDCB5]银票：" . formatMoney($find["cash"]) . "\n" .
                        "[@emoji=\ue035]钻石：" . $find["diamond"] . " 克\n" .
                        "[@emoji=\uD83D\uDC96]魅力：" . $find["charm"] . "\n" .
                        "[@emoji=\ue152]身份：" . $is_admin . "\n" .
                        "[@emoji=\ue32e]称号：无\n" .
                        "[@emoji=\ue428]婚姻：未婚\n" .
                        "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                        "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
                );
            } else {
                sendMsg("SendTextMsg", $data["final_from_name"] . "\n无法接收该成员的信息~\n请发送：更新信息");
            }
        }
    }

    /**
     * 提示 function
     *
     * @return void
     */
    private function msg()
    {
        $data = $this->data;
        if ($data["msg"] == "信息查询") {
            sendMsg(
                "SendTextMsg",
                "===查询指令===\n" .
                    "我的信息\n" .
                    "@对方\t查信息"
            );
        }
    }
}
