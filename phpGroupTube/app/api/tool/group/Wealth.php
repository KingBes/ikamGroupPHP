<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupMember;

#[GroupConfig(
    title: "求  财  神",
    icon: "\\ue035",
    config: [
        "wealthCashMin" => [
            "val" => 100,
            "name" => "最小现金",
            "ver" => "/^([1-9]\d{2}|1000)$/",
            "msg" => "必须100~1000`之间"
        ], //最小现金
        "wealthCashMax" => [
            "val" => 300,
            "name" => "最大现金",
            "ver" => "/^([1-9]\d{2}|1000)$/",
            "msg" => "必须100~1000`之间"
        ], //最大现金
        "wealthDiamondMin" => [
            "val" => 0.01,
            "name" => "最小钻石",
            "ver" => "/^(0.\d{1,2}|[1-9]\d{0,1}(.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //最小钻石
        "wealthDiamondMax" => [
            "val" => 0.1,
            "name" => "最大钻石",
            "ver" => "/^(0.\d{1,2}|[1-9]\d{0,1}(.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //最大钻石
    ],
    switch: 1
)]
class Wealth
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
        if ($demo->info["switch"] != 0) {
            $demo->index();
        }
    }

    private function index()
    {
        $data = $this->data;
        // echo "求财神：" . trimall($data["msg"]) . "\n";
        if (trimall($data["msg"]) == "求财神") {
            if (date("Ymd") == date("Ymd", $data["member_info"]["wealth_time"])) {
                sendMsg("SendTextMsg", $data["final_from_name"] . "\n您今天已经求过财神了哦~");
            } else {
                $info = $this->info; //配置
                $arr = [
                    "id" => $data["member_info"]['id'],
                    "wealth_time" => time()
                ];
                $rand = rand(1, 100);
                if ($rand <= 50) {
                    $diamond = randomFloat($info["config"]["wealthDiamondMin"]["val"], $info["config"]["wealthDiamondMax"]["val"]);
                    $arr["diamond"] = (int)$data["member_info"]['diamond'] + $diamond;
                    $msg = "[@emoji=\ue035]钻石：" . $diamond . "\t克\n";
                } else {
                    $cash = rand($info["config"]["wealthCashMin"]["val"], $info["config"]["wealthCashMax"]["val"]);
                    $arr["cash"] = (int)$data["member_info"]['cash'] + $cash;
                    $msg = "[@emoji=\uD83D\uDCB5]银票：" . $cash . "\n";
                }
                $edit = GroupMember::update($arr);
                if ($edit) {
                    $wealthMsg = rand(0, count(wealth) - 1);
                    sendMsg(
                        "SendTextMsg",
                        $data["final_from_name"] . "\n" .
                            "╭┈┈┈┈[@emoji=\ue035]求财[@emoji=\ue035]┈┈┈┈╮\n" .
                            $msg .
                            "[@emoji=\uD83D\uDC8C]祝语：" . wealth[$wealthMsg] . "\n" .
                            "[@emoji=\u23F0]时间：" . date("H:i:s", time()) . "\n" .
                            "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
                    );
                } else {
                    sendMsg("SendTextMsg", "求财神接口失败");
                }
            }
        }
    }
}
