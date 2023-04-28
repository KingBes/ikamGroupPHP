<?php

namespace app\api\controller;

use app\Request;
use app\common\Route;
use support\Log;
use Webman\RedisQueue\Redis;

class MsgController
{
    #[Route(path: '/api/msg/index')]
    public function index(Request $request)
    {
        $param = $request->all();

        Log::channel('getMsg')->info("消息接收:", $param);

        // 队列名
        $queue = 'send-event';

        // 投递消息
        $send = Redis::send($queue, $param, 3);

        echo "投递结果：{$send}\n";




        // print_r($param);
        // 投递延迟消息，消息会在60秒后处理
        // Redis::send($queue, $data, 60);

        // if ($param['msg'] == "测试机器人") {

        // sendEvent("SendTextMsg", "ok我是"); //文本消息

        /* sendEvent("SendLinkMsg", [
                "title" => "快乐两小时", //链接标题
                "text" => "asdasd", //链接简述
                "target_url" => "https://www.kllxs.top", //链接
                "pic_url" => "https://www.kllxs.top/files/20230325/641e949fd47ea.jpg", //链接图标
                "icon_url" => "https://www.kllxs.top/files/20230325/641e949fd47ea.jpg", //链接图标
            ]); //链接消息 */

        /* sendEvent("SendMusicMsg", [
                "music_name" => "说谎",
                "type" => 0
            ]); */ //普通音乐消息 效果一般般

        /* sendEvent("SendDiyMusicMsg", [
                "name" => "歌名",
                "singer" => "作者",
                "home" => "https://y.qq.com/n/yqq/song/004egnEi07dM8f.html", //地址
                "url" => "https://sk-sycdn.kuwo.cn/37ec102e4a904588e7cfa850c4c9fc6f/64379e56/resource/n3/80/81/768996291.mp3", //播放链接
                "type" => "wx5aa333606550dfd5" //wx8dd6ecd81906fd84 未知
            ]); */ //发送自定义音乐

        /* sendEvent("SendImageMsg", [
                "name" => "唉声叹气1.png", //本地 会存到本地
                "url" => "https://www.kllxs.top/files/20230325/641e949fd47ea.jpg", //网络地址
            ]); */ // 图片消息



        // }
    }
}
