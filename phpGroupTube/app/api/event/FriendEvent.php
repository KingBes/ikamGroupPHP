<?php

namespace app\api\event;

use app\api\tool\friend\UpdateFriend; //更新好友

class FriendEvent
{
    /**
     * 好友消息接收 function
     *
     * @return void
     */
    public static function msg()
    {
        UpdateFriend::update(); //更新好友
    }
}
