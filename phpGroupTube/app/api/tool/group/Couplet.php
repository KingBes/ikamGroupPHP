<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use app\model\GroupMember;
use app\common\BaiDuApi;
use support\Redis;

#[GroupConfig(
    title: "智能对联",
    icon: "\uD83C\uDF80",
    config: [
        "coupletDiamond" => [
            "val" => 0.01,
            "name" => "消耗钻石",
            "ver" => "/^(0\.\d{1,2}|[1-9]\d{0,1}(\.\d{1,2})?|100)$/",
            "msg" => "必须0.01~100之间"
        ], //消耗钻石
    ],
    switch: 1
)]
class Couplet
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
        $couplet = new self();
        if ($couplet->info["switch"] != 0) {
            $couplet->index();
            $couplet->msg();
        }
    }

    private function index()
    {
        $data = $this->data;
        if (startWith(trimall($data['msg']), "对联")) {
            $name = str_replace("对联", "", trimall($data['msg']));
            if ($name != "") {
                $info = $this->info; // 配置信息
                if ($data["member_info"]["diamond"] < $info["config"]["coupletDiamond"]["val"]) {
                    sendMsg(
                        "SendTextMsg",
                        $data["final_from_name"] . "\n" .
                            "创造对联需要消耗[@emoji=\ue035]" . $info["config"]["coupletDiamond"]["val"] . "\t克"
                    );
                    return;
                }
                $res = GroupMember::dec("diamond", $info["config"]["coupletDiamond"]["val"])
                    ->update(['id' => $data["member_info"]['id']]);
                if ($res) {
                    $redis = Redis::connection('baiduyun');
                    $sessionId = $redis->get("Couplet_" . $data["member_info"]["id"]);
                    // echo "进入了对话0" . $sessionId;
                    if (!$sessionId) {
                        $sessionId = uniqid();
                    }
                    $api = BaiDuApi::unit($data["member_info"]["id"], $name, $sessionId, ["1271447"]);
                    if ($api["code"] == 1 && $api["data"]["error_code"] == 0) {
                        // echo "进入了对话2";

                        $redis->set("Dialog_" . $data["member_info"]["id"], $api["data"]["result"]["session_id"]);
                        $redis->expire("Dialog_" . $data["member_info"]["id"], 1800);

                        if ($api["data"]["result"]["responses"][0]["status"] == 0) {
                            // echo "进入了对话3";
                            sendMsg(
                                "SendTextMsg",
                                $data["final_from_name"] . "\n" .
                                    "╭┈┈┈┈[@emoji=\uD83C\uDF80]对联[@emoji=\uD83C\uDF80]┈┈┈┈╮\n" .
                                    $api["data"]["result"]["responses"][0]["actions"][0]["say"] . "\n" .
                                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯"
                            );
                        } else {
                            sendMsg(
                                "SendTextMsg",
                                "智能对联失败"
                            );
                        }
                    } else {
                        sendMsg(
                            "SendTextMsg",
                            "智能对联报错"
                        );
                    }
                } else {
                    sendMsg(
                        "SendTextMsg",
                        "智能对联错误"
                    );
                }
            }
        }
    }

    private function msg()
    {
        $data = $this->data;
        if (trimall($data["msg"]) == "智能对联") {
            sendMsg(
                "SendTextMsg",
                "===对联指令===\n" .
                    "对联 xxx\n" .
                    "例：对联 春天"
            );
        }
    }
}
