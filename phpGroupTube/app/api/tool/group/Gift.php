<?php

namespace app\api\tool\group;

use app\common\GroupConfig;
use think\facade\Db;
use app\model\GroupMember;

#[GroupConfig(
    title: "礼       物",
    icon: "\\ue034",
    config: [],
    switch: 1
)]
class Gift
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
        $gift = new self();
        if($gift->info["switch"] != 0){
            $gift->giftOne();
            $gift->msg();
        }
    }

    /**
     * 送礼物 function
     *
     * @return void
     */
    private function giftOne()
    {
        $data = $this->data;
        if (startWith(trimall($data['msg']), "送") && count($data["at_list"]) == 1) {
            $name = substr(trimall($data['msg']), 3, 6); //关键词
            $gift = gift[$name];
            if (isset($gift)) {
                if (preg_match_all('/\d+/', trimall($data['msg']), $matches)) {
                    if ($matches[0][0] > 0) {
                        $num = $matches[0][0];
                    }
                } else {
                    $num = 1;
                }
                if ($data["member_info"]["cash"] < ($gift["cash"] * $num)) {
                    sendMsg(
                        "SendTextMsg",
                        $data["final_from_name"] . "\n" .
                            "银票不足!\n" .
                            "您[@emoji=\uD83D\uDCB5]" . formatMoney($data["member_info"]["cash"]) . "\n" .
                            "所需[@emoji=\uD83D\uDCB5]" . formatMoney($gift["cash"] * $num)
                    );
                } else {
                    Db::startTrans();
                    try {
                        GroupMember::dec('cash', ((int)$gift["cash"] * $num))
                            ->where('id', $data["member_info"]['id'])
                            ->update();
                        GroupMember::inc('charm', ((int)$gift["charm"] * $num))
                            ->where([
                                ["group_wxid", '=', $data["from_wxid"]],
                                ["member_wxid", '=', $data["at_list"][0]["wxid"]]
                            ])
                            ->update();
                        // 提交事务
                        Db::commit();
                        $at_user = GroupMember::where([
                            ["group_wxid", '=', $data["from_wxid"]],
                            ["member_wxid", '=', $data["at_list"][0]["wxid"]]
                        ])->find()->toArray();
                        sendMsg(
                            "SendTextMsg",
                            $data["at_list"][0]["nickname"] . "\n" .
                                "[礼物]收到礼物" . $gift['icon'] . "\n" .
                                "[@emoji=\uD83D\uDC96]魅力提升：" . (int)$gift["charm"] * $num . "\n" .
                                "[@emoji=\uD83D\uDC96]总魅力：" . $at_user["charm"] . "\n" .
                                "-------------------\n" .
                                "批量：送" . $name . "x10@对方"
                        );
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                        sendMsg(
                            "SendTextMsg",
                            $e->getMessage()
                        );
                    }
                }
            }
        }
    }

    /**
     * 消息 function
     *
     * @return void
     */
    private function msg()
    {
        $data = $this->data;
        if (trimall($data['msg']) == "礼物") {
            $gift = gift;
            $list = '';
            foreach ($gift as $k => $v) {
                $list = $list . $v['icon'] . $k . "▼" . $v['cash'] . "银票▼" . $v['charm'] . "\r";
            }
            sendMsg(
                "SendTextMsg",
                "╭┈┈┈┈[@emoji=\ue034]礼物[@emoji=\ue034]┈┈┈┈╮\n" .
                    "礼物 ▼      价格      ▼\t魅力\r" .
                    $list .
                    "╰┈┈┈┈┈┈┈┈┈┈┈┈┈╯\r" .
                    "指令：送西瓜@对方"
            );
        }
    }
}
