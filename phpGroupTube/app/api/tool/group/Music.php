<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\Music as mod;
use GuzzleHttp\Client; //请求
use app\model\GroupMember;

#[GroupConfig(
    title: "音       乐",
    icon: "\\ue03e",
    config: [
        "musicDiamond" => [
            "val" => 0.01,
            "name" => "消耗钻石",
            "ver" => "/^(0.\d{1,2}|[1-9]\d{0,1}(.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //消耗钻石
    ],
    switch: 1
)]
class Music
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
        $Music = new self();
        if ($Music->info["switch"] != 0) {
            $Music->song();
            $Music->msg();
        }
    }

    /**
     * 点歌 function
     *
     * @return void
     */
    private function song()
    {
        // 此处不分享
    }

    /**
     * 消息 function
     *
     * @return void
     */
    private function msg()
    {
        $data = $this->data;
        if (trimall($data['msg']) == "音乐") {
            sendMsg(
                "SendTextMsg",
                "===音乐指令===\n" .
                    "点歌\t关键词\n" .
                    "例如：点歌\t青花瓷"
            );
        }
    }
}
