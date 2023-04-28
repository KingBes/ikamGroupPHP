<?php

namespace app\api\event;

use app\api\tool\group\Member;

class EventGroupMemberAdd
{
    /**
     * 群成员增加事件 function
     *
     * @return void
     */
    public static function msg()
    {
        $data = task()->param;
        print_r($data);
        Member::entry();
    }
}
