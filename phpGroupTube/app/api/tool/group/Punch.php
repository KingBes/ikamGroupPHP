<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupPunch;

#[GroupConfig(
    title: "签       到",
    icon: "\\ue110",
    config: [
        "punchDiamond" => [
            "val" => 0.01,
            "name" => "获得钻石",
            "ver" => "/^(0.\d{1,2}|[1-9]\d{0,1}(.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //获得钻石
        "punchCashMin" => [
            "val" => 100,
            "name" => "最少银票",
            "ver" => "/^([1-9]\d{2}|1000)$/",
            "msg" => "必须100~1000`之间"
        ], //获得最少银票
        "punchCashMax" => [
            "val" => 300,
            "name" => "最大银票",
            "ver" => "/^([1-9]\d{2}|1000)$/",
            "msg" => "必须100~1000`之间"
        ], //获得最大银票
    ],
    switch: 1
)]
class Punch
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
        $puch = new self();
        if($puch->info["switch"] != 0){
            $puch->index();
        }
    }

    private function index()
    {
        $data = $this->data;
        if (trimall($data['msg']) == "签到" || trimall($data['msg']) == "打卡") {
            $info = $this->info; //配置
            $find =  GroupPunch::where("id", $data["member_info"]['id'])->find();
            $cash = rand(
                $info["config"]["punchCashMin"]["val"],
                $info["config"]["punchCashMax"]["val"]
            ); //银票
            $rankind = GroupPunch::where("group_wxid", $data["member_info"]['group_wxid'])
                ->whereDay("update_time")
                ->count() + 1;
            if ($find) {
                if (date("Ymd") != date("Ymd", timeConvert($find["update_time"]))) {
                    $arr = [
                        'id' => $find['id'],
                        "total" => $find['total'] + 1,
                        "ranking" => $rankind,
                        "cash" => $cash
                    ];
                    if (date("Ymd", strtotime('-1 day')) == date("Ymd", timeConvert($find["update_time"]))) {
                        $arr["series"] = $find['series'] + 1;
                    } else {
                        $arr["series"] = 1;
                    }
                    $arr["diamond"] = $arr["series"] * $info["config"]["punchDiamond"]["val"];
                    $find = GroupPunch::update($arr);
                }
            } else {
                $find = GroupPunch::create([
                    "id" => $data["member_info"]['id'],
                    "group_wxid" => $data["member_info"]['group_wxid'],
                    "total" => 1,
                    "ranking" => $rankind,
                    "series" => 1,
                    "diamond" => $info["config"]["punchDiamond"]["val"],
                    "cash" => $cash
                ]);
            }
            sendMsg(
                "SendTextMsg",
                $data["final_from_name"] . "\n" .
                    "╭┈┈┈┈[@emoji=\ue110]签到[@emoji=\ue110]┈┈┈┈╮\n" .
                    "[太阳]排名：" . $find["ranking"] . "名\n" .
                    "[@emoji=\uD83D\uDCB5]银票：" . $find["cash"] . "\n" .
                    "[@emoji=\ue035]钻石：" . $find["diamond"] . " 克\n" .
                    "[@emoji=\ue02f]连续次数：" . $find["series"] . "\n" .
                    "[@emoji=\ue029]总次数：" . $find["total"] . "\n" .
                    "[@emoji=\u23F0]时间：" . date("H:i:s", timeConvert($find["update_time"])) . "\n" .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
            );
        }
    }
}
