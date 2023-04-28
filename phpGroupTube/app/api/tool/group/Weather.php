<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use GuzzleHttp\Client;
use app\model\GroupMember;
use app\model\ApiConfig;

#[GroupConfig(
    title: "天       气",
    icon: "\ue049",
    config: [
        "weatherDiamond" => [
            "val" => 0.01,
            "name" => "消耗钻石",
            "ver" => "/^(0.\d{1,2}|[1-9]\d{0,1}(.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //消耗钻石
    ],
    switch: 1
)]
class Weather
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
            $demo->real_time();
            $demo->msg();
        }
    }

    /**
     * 实时天气 function
     *
     * @return void
     */
    private function real_time()
    {
        $data = $this->data;
        if (endWith(trimall($data['msg']), "天气")) {
            $name = str_replace("天气", "", trimall($data['msg']));
            if ($name != "") {
                $cityList = cityFile(); //城市列表
                // print_r($cityList);
                if (!isset($cityList[$name])) {
                    sendMsg("SendTextMsg", "没有找到" . $name . "这座城市");
                    return;
                }
                // echo "进入天气";
                $info = $this->info; // 配置信息
                if ($data["member_info"]["diamond"] < $info["config"]["weatherDiamond"]["val"]) {
                    sendMsg(
                        "SendTextMsg",
                        $data["final_from_name"] . "\n" .
                            "查天气需要消耗[@emoji=\ue035]" . $info["config"]["weatherDiamond"]["val"] . "\t克"
                    );
                    return;
                }
                GroupMember::dec("diamond", $info["config"]["weatherDiamond"]["val"])
                    ->update(['id' => $data["member_info"]['id']]);
                $Client = new Client([
                    "base_uri" => "https://devapi.qweather.com/v7/weather/now",
                    "query" => [
                        "location" => $cityList[$name],
                        "key" => ApiConfig::val("weather", "key")
                    ]
                ]);
                $api = $Client->request("GET")->getBody()->getContents();
                $res = json_decode($api, true);
                if ($res["code"] == 200) {
                    $date = strtotime($res['now']['obsTime']);
                    $formatteddate = date("m-d H:i", $date);
                    sendMsg(
                        "SendTextMsg",
                        "╭┈┈┈[@emoji=\ue049]实时天气[@emoji=\ue049]┈┈┈╮\n" .
                            $name . "实时天气\n" .
                            "[@emoji=\uD83C\uDF21]温度：" . $res['now']['temp'] . "°\n" .
                            "[@emoji=\u2728]体感：" . $res['now']['feelsLike'] . "°\n" .
                            "[@emoji=\uD83E\uDDED]状况：" . $res['now']['text'] . "\n" .
                            "[@emoji=\uD83D\uDCA7]湿度：" . $res['now']['humidity'] . "%\n" .
                            "[@emoji=\uD83C\uDF27]降水量：" . $res['now']['precip'] . "mm\n" .
                            "[@emoji=\uD83D\uDCA8]大气压强：" . $res['now']['pressure'] . "HPa\n" .
                            "[@emoji=\ue419]可见度：" . $res['now']['vis'] . "Km\n" .
                            "[@emoji=\uD83C\uDF2C]风：" . $res['now']['wind360'] . "° " . $res['now']['windDir'] . " " .
                            $res['now']['windScale'] . "级 " . $res['now']['windSpeed'] . "Km/h\n" .
                            "[@emoji=\u23F0]时间：" . $formatteddate . "\n" .
                            "╰┈┈┈┈┈┈┈┈┈┈┈┈╯"
                    );
                } else {
                    sendMsg("SendTextMsg", "实时天气接口失败");
                }
            }
        }
    }

    private function msg()
    {
        $data = $this->data;
        if ($data["msg"] == "天气") {
            sendMsg(
                "SendTextMsg",
                "===查询指令===\n" .
                    "某某 天气\n" .
                    "例：北京天气"
            );
        }
    }
}
