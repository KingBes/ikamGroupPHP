<?php

namespace app\api\event;

use app\api\tool\group\Base;
use app\api\tool\group\Music;
use app\api\tool\group\Punch;
use app\api\tool\group\Info;
use app\api\tool\group\Wealth;
use app\api\tool\group\Gift;
use app\api\tool\group\Dialog;
use app\api\tool\group\Couplet;
use app\api\tool\group\Ranking;
use app\api\tool\group\Weather;

class GroupEvent
{
    /**
     * 群消息接收 function
     *
     * @return void
     */
    public static function msg()
    {
        $data = task()->param;
        switch ($data["type"]) {
            case 1:
                echo "刚进入\n";
                Base::entry(); //基础
                Punch::entry(); //打卡
                Music::entry(); //音乐
                Info::entry(); //我的信息
                Wealth::entry(); //求财神
                Gift::entry(); //礼物
                Dialog::entry(); //智能对话
                Couplet::entry(); //智能对联
                Ranking::entry(); //排行榜
                Weather::entry(); //天气
                break;
            // case 
        }
    }
}
