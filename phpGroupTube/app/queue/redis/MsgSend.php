<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;
use app\api\event\GroupEvent; //群消息
use app\api\event\EventGroupMemberAdd; //群成员增加消息
use app\api\event\EventGroupMemberDecrease; //群成员减少消息
use app\api\event\FriendEvent; //好友消息

class MsgSend implements Consumer
{
    // 要消费的队列名
    public $queue = 'send-event';

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($info)
    {
        task()->param = $info;
        switch ($info["event"]) {
            case "EventGroupMsg": //群消息
                echo "\n进入了\n";
                task()->param = dataMsg($info);
                GroupEvent::msg();
                echo "走完了\n";
                break;
            case "EventGroupMemberAdd": //群成员增加
                EventGroupMemberAdd::msg();
                break;
            case "EventGroupMemberDecrease": //群成员减少
                EventGroupMemberDecrease::msg();
                break;
            case "EventFriendMsg": //好友消息
                FriendEvent::msg();
                break;
        }
    }
}
