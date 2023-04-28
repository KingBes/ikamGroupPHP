<?php

namespace app\index\controller;

use app\Request;
use app\common\Route;
use GuzzleHttp\Client;
use app\common\BaiDuApi;
// use support\Redis;
use app\model\Flag;


class IndexController
{
    #[Route(path: '/')]
    public function index(Request $request)
    {
        return "想干什么？吊毛~";
        /* $date = \datetime::createfromformat('Y-m-d\th:io', '2023-04-23t14:55+08:00');
        $formatteddate = $date->format('m-d h:i:s');
        echo $formatteddate; // 输出：2023-04-23 14:55:00 */

        // echo strtotime(date('c'));

        /* $cityList = cityFile(); //城市列表
        $Client = new Client([
            "base_uri" => "https://devapi.qweather.com/v7/weather/now",
            "query" => [
                "location" => $cityList["从化"],
                "key" => "c882b0095d4d4d2a9d39a9f4354e056d"
            ]
        ]);
        $api = $Client->request("GET")->getBody()->getContents();
        // $res = json_decode($api, true);
        return $api; */
        /* $Client = new Client([
            "base_uri" => "http://114.xixik.com/country-flag/",
            "timeout" => 30,
        ]);

        $api = $Client->request("GET")->getBody()->getContents();
        $outPageTxt = mb_convert_encoding($api, 'utf-8', 'GB2312');
        // return $outPageTxt;

        preg_match_all("/<td>(.*?)<\/td>/s", $outPageTxt, $matches);

        // 输出匹配到的内容
        // print_r($matches[0]);
        $state = true;
        foreach ($matches[1] as $k => $v) {

            preg_match_all("/(<img.*src=\"(.*\.gif)\".*\/>)/s", $v, $yes);
            if ($yes[0]) {
                // print_r($yes);
                $res = Flag::create([
                    "link" => $yes[2][0],
                    "name" => str_replace($yes[1], "", $v)
                ]);
                if (!$res) {
                    $state = false;
                }
            } else {
                continue;
            }
        }
        return $state; */


        /* $api = BaiDuApi::unit(
            user_id: "777",
            text: "爱我",
            session_id: "chat-session-id-1682224167426-a76cd7d21e5b41c4b50884fd2873ffc5",
            skill_ids: ["1337655"],
            service_id: ""
        );

        return json($api); */

        /* $redis = Redis::connection('baiduyun');
        
        var_dump($redis->get("Asd")); */

        // $bdy = ApiConfig::where("type","baiduyun")->select();
        /* $Client = new Client([
            "base_uri" => "https://aip.baidubce.com/oauth/2.0/token",
            "query" => [
                "grant_type" => "client_credentials",
                "client_id" => ApiConfig::val("baiduyun", "client_id"),
                "client_secret" => ApiConfig::val("baiduyun", "client_secret")
            ],
            "timeout" => 30,
        ]);
        $api = $Client->request("POST")->getBody()->getContents();

        return json($api); */

        // return substr("送西瓜x100", 3, 6);
        // return formatMoney(109900);
        // return randomFloat(0.1,0.6);
        /* $toolFile = get_file_php('\\api\\tool\\group');
        $msg = '';
        $i = 0;

        foreach ($toolFile as $k => $v) {
            $ReflectionClass = new \ReflectionClass("app\\api\\tool\\group\\" . $v);
            $info = $ReflectionClass->getAttributes()[0]->getArguments();
            print_r($info);
            if (!isset($info["deploy"]) || $info["deploy"] != 0) {
                if (($i + 1) % 2 == 0) {
                    $msg = $msg . $info['title'] . "[@emoji=" . strtolower($info['icon']) . "]\r";
                } else {
                    $msg = $msg . "[@emoji=" . strtolower($info['icon']) . "]" . $info['title'] . '[@emoji=\ue118]';
                }
                $i++;
            }
        }
        return $msg; */
        // return configInfo();
        /* $Client = new Client();
        $api = $Client->request("get", "https://ws.stream.qqmusic.qq.com/C600003VLsik0ztbIb.m4a?guid=0&vkey=AF6AE1E1A9251B367F41C489587DA19FAB7F20491F23204FBDE2F44EDAE4593AC23FE102038C211A00A3361BF27198C61011DC208EEEAF47&uin=0&fromtag=40")->getBody()->getContents();
        return trimall($api); */

        /* $res = musicSave("测试", "https://ws.stream.qqmusic.qq.com/C6000015zR8B3gjJLl.m4a?guid=0&vkey=522CA3A6CE513CE4110304224A1B6D3072DF14CD1EA8437D40FD5165427911604EF6437F33A89C11A64142889A1288B42762A4D687AA44B3&uin=0&fromtag=40");
        return $res; */

        /* $str = "Asd";
        @$str = "Asd1";
        return $str; */
        /* $Client = new Client;
        $res = $Client->request("get", "http://61.144.73.75:8787/api/music/index/1")->getBody()->getContents();
        return $res; */
        /* $str = '[[@at,nickname=at,nickname=山茶[@emoji=\u0ECA]花[@emoji=\u0ECA][@emoji=\uD83C\uDF38],wxid=] ..[@at,nickname=[@emoji=\u00A0],wxid=wxid_7815358134412] asd,wxid=wxid_q1m0kr3lxt9k22]';

        preg_match_all('/wxid=([^,\]]+)/', $str, $matches);
        $result = [];
        foreach ($matches[1] as $k => $match) {
            $str = str_replace($matches[1][$k], "", $str);
            $str = str_replace(",wxid=]", "", $str);
            $str = str_replace(",wxid=", "", $str);
            $str = str_replace("@at,", "", $str);
            $str = str_replace("nickname=at,", "", $str);
            $result[] = ['wxid' => $match];
        }
        $str = substr($str, 1);
        $str = substr($str, 0, -1);
        

        preg_match_all('/\[@emoji=(\\\\u[0-9A-Fa-f]{4}|\S+)\]/', $str, $matches);
        
        foreach ($matches[0] as $k => $v) {
            $str = str_replace($v, "{@emoji=" . $matches[1][$k] . "}", $str);
        }
        preg_match_all('/\b=(\S*)\s/', $str, $matches);
        
        foreach ($matches[1] as $k => $v) {
            $str = str_replace($v, "", $str);
            $v = str_replace("{", "[", $v);
            $v = str_replace("}", "]", $v);
            $result[$k]["nickname"] = $v;
        }
        $str = str_replace("[nickname=", "", $str);
        print_r($result);
        print_r($str); */
    }
}
