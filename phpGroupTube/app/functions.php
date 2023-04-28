<?php

/**
 * Here is your custom functions.
 */

define('MineIp', '127.0.0.1:8787');

use GuzzleHttp\Client; //请求
use support\Log; //日记
use app\model\GroupConfig; //群配置
use app\model\GroupMember; //用户
use app\common\WechatWork; //worker

/**
 * worker function
 *
 * @return object
 */
function task(): object
{
    return WechatWork::getInstance()->getTask();
}

/**
 * 操作事件 function
 *
 * @param array $data
 * @return array
 */
function sendEvent(array $data): array
{
    $Client = new Client([
        "base_uri" => "http://127.0.0.1:8090",
        "headers" => [
            "Authorization" => "Bearer 390un88G62u8pzDZtlve97c4T3l7DLYZ"
        ],
        "json" => $data,
        "timeout" => 30,
        // 'debug' => true
    ]);

    $res = $Client->request("POST");
    Log::channel('msgOut')->info("消息回复:", json_decode($res->getBody(), true));
    return json_decode($res->getBody(), true);
}

/**
 * 发送消息 function
 *
 * @param string $event 发送类型
 * @param array|string $msg 发送内容
 * @param string $member_wxid 目标id
 * @param string $member_name 目标名称
 * @param string $group_wxid 目标群id
 * @return void
 */
function sendMsg(
    string $event,
    array|string $msg,
    string $member_wxid = "",
    string $member_name = "",
    string $group_wxid = ""
) {
    $param = task()->param; //所有消息
    $json = [
        "event" => $event, //发送类型
        "robot_wxid" => $param['robot_wxid'] ? $param['robot_wxid'] : "", //机器人id
        "to_wxid" => $param['from_wxid'] ? $param['from_wxid'] : $param['final_from_wxid'], //发送id
        "member_wxid" => $member_wxid,
        "member_name" => $member_name,
        "group_wxid" => $group_wxid,
        "msg" => $msg, //发送内容
    ];
    return sendEvent($json);
}


/**
 * 消息整理 function
 *
 * @param array $data
 * @return array
 */
function dataMsg(array $data): array
{
    $str = $data["msg"];
    $data["at_list"] = [];
    if (is_string($str) && (strstr($str, "[[@at,nickname=") || strstr($str, "[@at,nickname="))) {
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
        preg_match_all('/\[@emoji=(\\\\u[0-9A-Fa-f]{4}|\S+)\]/', $str, $matches);

        foreach ($matches[0] as $k => $v) {
            $str = str_replace($v, "{@emoji=" . $matches[1][$k] . "}", $str);
        }
        // echo $str;
        Log::channel('getMsg')->info("消息str:" . $str);

        if (preg_match_all('/\b=(\S*)\,\s/', $str, $matches) || preg_match_all('/\b=(\S*)\s/', $str, $matches)) {
            foreach ($matches[1] as $k => $v) {
                $str = str_replace($v, "", $str);
                $v = str_replace("{", "[", $v);
                $v = str_replace("}", "]", $v);
                $result[$k]["nickname"] = $v;
            }
        }
        $str = str_replace("[nickname=", "", $str);
        $str = str_replace("[", "", $str);

        $str = str_replace("]", "", $str);

        $data["at_list"] = $result;
        $data["msg"] = $str;
    }
    $find = GroupMember::where([
        ['group_wxid', "=", $data["from_wxid"]],
        ["member_wxid", "=", $data["final_from_wxid"]]
    ])->find();
    if ($find) {
        $data["member_info"] = $find->toArray();
    }
    Log::channel('getMsg')->info("整理消息:", $data);
    return $data;
}

/**
 * 删除空格 function
 *
 * @param string|array $str
 * @return string|array
 */
function trimall(string|array $str): string|array
{
    if (is_string($str)) {
        $limit = array(" ", "　", "\t", "\n", "\r", " ");
        $rep = array("", "", "", "", "", "");
        $str = str_replace($limit, $rep, $str);
    }
    return $str;
}

/**
 * 聊天内容是否以关键词xx开头 function
 *
 * @param string $str 字符串
 * @param string $pattern 开头字符串
 * @return boolean
 */
function startWith(string $str, string $pattern): bool
{
    return strpos($str, $pattern) === 0 ? true : false;
}

/**
 * 聊天内容是否以关键词xx结尾 function
 *
 * @param string $str 字符串
 * @param string $suffix 结尾字符串
 * @return boolean
 */
function endWith(string $str, string $suffix): bool
{
    $length = strlen($suffix);
    if ($length == 0) {
        return true;
    }
    return (substr($str, -$length) === $suffix);
}

/**
 * 保存音乐 function
 *
 * @param string $name 音乐名
 * @param string $url 音乐链接
 * @return boolean
 */
function musicSave(string $name, string $url): bool
{
    $Client = new Client(['verify' => false, "timeout" => 60]);
    $state = false;
    if (!file_exists(public_path() . "/music" . "/" . $name . ".mp3")) { //不存在
        $api = $Client->request("get", $url)->getBody()->getContents();
        $fp = @fopen("public/music/" . $name . ".mp3", "a"); //将文件绑定到流
        $then = fwrite($fp, $api); //写入文
        if ($then) {
            $state = true;
        }
    }
    return $state;
}

/**
 * 配置信息 function
 *
 * @param [type] $class
 * @param [type] $group_wxid
 * @return void
 */
function configInfo($class, $group_wxid)
{
    $ReflectionClass = new \ReflectionClass($class);
    $info = $ReflectionClass->getAttributes()[0]->getArguments();
    // return $info;
    if (isset($info["config"]) && count($info["config"])) {
        foreach ($info["config"] as $k => $v) {
            $find = GroupConfig::where("name", '=', $k)
                ->where("group_wxid", $group_wxid)
                ->find();
            if ($find) {
                $info["config"][$k]["val"] = $find["val"];
            }
        }
    }
    $className = $ReflectionClass->getShortName();
    $info["className"] = $className;
    if (isset($info["switch"])) {
        $s = GroupConfig::where("name", '=', $className . "_switch")
            ->where("group_wxid", $group_wxid)
            ->value("val");
        if ($s) {
            $info["switch"] = $s;
        }
    }
    return $info;
}

/**
 * 时间转时间戳 function
 *
 * @param string $time
 * @return integer
 */
function timeConvert(string $time): int
{
    if (!ctype_digit($time)) {
        $time = strtotime($time);
    }
    return $time;
}

/**
 * 单位符 function
 *
 * @param integer $money
 * @return string
 */
function formatMoney(int $money): string
{
    $length = strlen($money);  //数字长度
    if ($length > 8) { //亿单位
        $str = substr_replace(floor($money * 0.0000001), '.', -1, 0) . "亿";
    } elseif ($length > 4) { //万单位
        //截取前俩为
        $str = floor($money * 0.001) * 0.1 . "万";
    } else {
        return $money;
    }
    return $str;
}

/**
 * 获取某文件夹中的php文件 function
 *
 * @param string $url 路径
 * @return array
 */
function get_file_php(string $url): array
{
    $path = app_path() . $url; //控制器路径
    $pathList = glob($path . '\\*.php'); //模糊查询匹配
    $controllerList = [];
    foreach ($pathList as $k => $v) { //循环查找
        $controllerList[] = basename($v, '.php');
    }
    return $controllerList;
}

/**
 * 随机数小数点 function
 *
 * @param integer $min
 * @param integer $max
 * @return string
 */
function randomFloat($min = 0, $max = 1): string
{
    $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
    return sprintf("%.2f", $num);  //控制小数后几位
}

define('wealth', [
    '送你这棵摇钱树，愿你财运亨通！',
    '万物有生意，春来发几枝。枝枝都是好运气，枝枝都带发财意，枝枝都祝你顺利。',
    '财有道，得之吉，利自己，福家庭，惠及亲友乐。',
    '希望大家开心，希望您发财。',
    '旺财，旺丁旺旺旺，愿大家好运来。',
    '辞旧迎新财运来，祝国泰民安，阖家幸福，万事如意。',
    '祝你财源广，宾客赶不跑。',
    '祝您有钱，祝您暴富，祝您深情不被辜负。',
    '发财要持久，愿慈悲的佛菩萨保佑您四季发财，年年有余。',
    '送你一个聚宝盆，财源广进幸福临门。',
    '愿财运把你缠绕，财神把你拥抱，财富为你倾倒。',
    '发一生发一世，一生发一世发，四季都要发：春天顺财要你发，夏天洪财要你发，秋天喜财要你发，冬天福财要你发。天天发，月月发，四季发，祝你快乐群发，财运暴发！',
    '清风拂过忘忧草，发财小鸟为你叫，祝你早安心情妙，鸿运当头步步高。',
    '祝你吉祥如意，发大财。',
    '愿你发财在今朝，兴隆在此刻。',
    '祝你生意兴隆乐无边，钱财不断把你缠！',
    '祝你多多发财，身体健康！阖家幸福！',
    '财神送财到你家！小财不发大财发，横财不发顺财发，凶财不发吉财发，怪财不发喜财发，祸财不发福财发！凡是该发都能发，随心所欲你大发！',
    '财源滚滚来，前程似锦绣。',
    "十全祝福送给你，一分祝福，二分盼望，三分健康，四分亲情，五分平安，六分顺利，七分幸福，八分钟爱，九分如意，十分财富，最后祝你家庭和和美美！",
    '开门大吉财运来，祝你发财又升官，心想事成。'
]);

define(
    'gift',
    [
        '蠢猪' => ['icon' => '[@emoji=\ue10B]', 'cash' => 10000, 'charm' => -750],
        '炸弹' => ['icon' => '[@emoji=\ue311]', 'cash' => 1000, 'charm' => -50],
        '药丸' => ['icon' => '[@emoji=\ue30F]', 'cash' => 50, 'charm' => -10],
        '大便' => ['icon' => '[@emoji=\ue05A]', 'cash' => 10, 'charm' => -1],
        '西瓜' => ['icon' => '[@emoji=\ue348]', 'cash' => 10, 'charm' => 1],
        '啤酒' => ['icon' => '[@emoji=\ue047]', 'cash' => 50, 'charm' => 10],
        '玫瑰' => ['icon' => '[@emoji=\ue032]', 'cash' => 1000, 'charm' => 50],
        '蛋糕' => ['icon' => '[@emoji=\ue34B]', 'cash' => 2000, 'charm' => 100],
        '口红' => ['icon' => '[@emoji=\ue31C]', 'cash' => 5000, 'charm' => 300],
        '手机' => ['icon' => '[@emoji=\ue00A]', 'cash' => 8000, 'charm' => 500],
        '内衣' => ['icon' => '[@emoji=\ue322]', 'cash' => 10000, 'charm' => 650],
        '包包' => ['icon' => '[@emoji=\ue323]', 'cash' => 20000, 'charm' => 1250],
        '戒指' => ['icon' => '[@emoji=\ue034]', 'cash' => 50000, 'charm' => 3800],
        '美女' => ['icon' => '[@emoji=\ue429]', 'cash' => 80000, 'charm' => 8000],
        '帅哥' => ['icon' => '[@emoji=\ue515]', 'cash' => 80000, 'charm' => 8000],
        '跑车' => ['icon' => '[@emoji=\ue42e]', 'cash' => 200000, 'charm' => 20000],
        '别墅' => ['icon' => '[@emoji=\ue036]', 'cash' => 1000000, 'charm' => 100000],
        '火箭' => ['icon' => '[@emoji=\ue10D]', 'cash' => 10000000, 'charm' => 1000000],
    ]
);

/**
 * 城市数据 function
 *
 * @return array
 */
function cityFile(): array
{
    return json_decode(file_get_contents(public_path() . "/city.json"), true);
}


/**
 * assign function
 *
 * @param array|string $name
 * @param mixed $value
 * @return void
 */
function assign(array|string $name, mixed $value = null)
{
    $request = \request();
    $plugin = $request->plugin ?? '';
    $handler = \config($plugin ? "plugin.$plugin.view.handler" : 'view.handler');
    $handler::assign($name, $value);
}

/**
 * 获取当前的response 输出类型
 *
 * @return string
 */
function getResponseType(): string
{
    return request()->acceptJson() || request()->isAjax() ? 'json' : 'html';
}

/**
 * 操作成功跳转的快捷方法
 * @access protected
 * @param  mixed $msg 提示信息
 * @param  string $url 跳转的URL地址
 * @param  mixed $data 返回的数据
 * @param  integer $wait 跳转等待时间
 * @param  array $header 发送的Header信息
 * @return void
 */
function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
{
    if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
        $url = $_SERVER["HTTP_REFERER"];
    } elseif ($url) {
        $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : route($url);
    }

    $result = [
        'code' => 1,
        'msg' => $msg,
        'data' => $data,
        'url' => $url,
        'wait' => $wait,
    ];

    $type = getResponseType();
    if ('html' == strtolower($type)) {
        static $handler;
        if (null === $handler) {
            $handler = config('view.handler');
        }
        //模板路径 BASE_PATH . '/public/jump.html'
        return response($handler::render(BASE_PATH . '/public/jump.html', $result), 200, $header);
    } else {
        return json($result);
    }
}

/**
 * 操作错误跳转的快捷方法
 * @access protected
 * @param  mixed $msg 提示信息
 * @param  string $url 跳转的URL地址
 * @param  mixed $data 返回的数据
 * @param  integer $wait 跳转等待时间
 * @param  array $header 发送的Header信息
 * @return void
 */
function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
{
    if (is_null($url)) {
        $url = request()->isAjax() ? '' : 'javascript:history.back(-1);';
    } elseif ($url) {
        $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : route($url);
    }

    $result = [
        'code' => 2,
        'msg' => $msg,
        'data' => $data,
        'url' => $url,
        'wait' => $wait,
    ];

    $type = getResponseType();

    if ('html' == strtolower($type)) {
        static $handler;
        if (null === $handler) {
            $handler = config('view.handler');
        }
        //模板路径 BASE_PATH . '/public/jump.html'
        return response($handler::render(BASE_PATH . '/public/jump.html', $result), 200, $header);
    } else {
        return json($result);
    }
}


/**
 * unicode转换 function
 *
 * @param string $unicode_str
 * @return string
 */
function unicodeDecode(string $unicode_str): string
{
    $json = '{"str":"' . $unicode_str . '"}';
    $arr = json_decode($json, true);
    if (empty($arr)) return '';
    return $arr['str'];
}
