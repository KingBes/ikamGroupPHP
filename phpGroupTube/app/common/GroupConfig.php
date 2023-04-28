<?php

namespace app\common;

class GroupConfig
{
    /**
     * 群拓展配置 function
     *
     * @param string $title 名称
     * @param string $icon 图标
     * @param array $config 配置组
     * @param integer $switch 开关 1开0关
     * @param integer $deploy 是否配置 1是配置0不是
     */
    public function __construct(
        string $title,
        string $icon,
        array $config,
        int $switch,
        int $deploy
    ) {
        return [
            "title" => $title,
            "icon" => $icon,
            "config" => $config,
            "switch" => $switch,
            "deploy" => $deploy,
        ];
    }
}
