<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupMember;

#[GroupConfig(
    title: "排  行  榜",
    icon: "\uD83D\uDCCA",
    config: [],
    switch: 1
)]
class Ranking
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
        $demo = new self();
        if($demo->info["switch"] != 0){
            $demo->cash();
            $demo->diamond();
            $demo->charm();
            $demo->dayLiveness();
            $demo->liveness();
            $demo->msg();
        }
        
    }

    /**
     * 银票排行 function
     *
     * @return void
     */
    private function cash()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "银票排行") {
            $list = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]]
            ])->order("cash", "desc")->limit(0, 10)->select();
            $msg = "";
            foreach ($list as $k => $v) {
                $msg = $msg . ($k + 1) . ".「" . $v["group_nickname"] . "」[@emoji=\uD83D\uDCB5]" . formatMoney($v["cash"]) . "\n";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈┈[@emoji=\uD83D\uDCCA]银票排行[@emoji=\uD83D\uDCCA]┈┈┈╮\n" .
                    $msg .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    /**
     * 钻石排行 function
     *
     * @return void
     */
    private function diamond()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "钻石排行") {
            $list = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]]
            ])->order("diamond", "desc")->limit(0, 10)->select();
            $msg = "";
            foreach ($list as $k => $v) {
                $msg = $msg . ($k + 1) . ".「" . $v["group_nickname"] . "」[@emoji=\ue035]" . $v["diamond"] . "\t克\n";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈┈[@emoji=\uD83D\uDCCA]钻石排行[@emoji=\uD83D\uDCCA]┈┈┈╮\n" .
                    $msg .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    /**
     * 魅力排行 function
     *
     * @return void
     */
    private function charm()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "魅力排行") {
            $list = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]]
            ])->order("charm", "desc")->limit(0, 10)->select();
            $msg = "";
            foreach ($list as $k => $v) {
                $msg = $msg . ($k + 1) . ".「" . $v["group_nickname"] . "」[@emoji=\uD83D\uDC96]" . formatMoney($v["charm"]) . "\n";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈┈[@emoji=\uD83D\uDCCA]魅力排行[@emoji=\uD83D\uDCCA]┈┈┈╮\n" .
                    $msg .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    /**
     * 今日-活跃排行 function
     *
     * @return void
     */
    private function dayLiveness()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "活跃排行") {
            $list = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]]
            ])->order("msg_day_num", "desc")->limit(0, 10)->select();
            $msg = "";
            foreach ($list as $k => $v) {
                $msg = $msg . ($k + 1) . ".「" . $v["group_nickname"] . "」[@emoji=\uD83D\uDD25]" . $v["msg_day_num"] . "\n";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈┈[@emoji=\uD83D\uDCCA]活跃排行[@emoji=\uD83D\uDCCA]┈┈┈╮\n" .
                    $msg .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    /**
     * 总活跃度排行 function
     *
     * @return void
     */
    private function liveness()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "总活跃排行") {
            $list = GroupMember::where([
                ["group_wxid", '=', $data["from_wxid"]]
            ])->order("msg_num", "desc")->limit(0, 10)->select();
            $msg = "";
            foreach ($list as $k => $v) {
                $msg = $msg . ($k + 1) . ".「" . $v["group_nickname"] . "」[@emoji=\uD83D\uDD25]" . formatMoney($v["msg_num"]) . "\n";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈[@emoji=\uD83D\uDCCA]总活跃\t\t排行[@emoji=\uD83D\uDCCA]┈┈╮\n" .
                    $msg .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }

    private function msg()
    {
        $data = $this->data;
        if ($data["msg"] == "排行榜") {
            sendMsg(
                "SendTextMsg",
                "===查询指令===\n" .
                    "银票排行\n" .
                    "钻石排行\n" .
                    "魅力排行\n" .
                    "活跃排行\n" .
                    "总活跃排行"
            );
        }
    }
}
