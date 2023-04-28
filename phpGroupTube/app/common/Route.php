<?php

namespace app\common;

class Route
{
    /**
     * 路由注解 function
     *
     * @param string $request
     * @param string $path
     * @param array|null $middleware
     */
    public function __construct(
        string $request,
        string $path,
        array|null $middleware
    ) {
        return [
            'request' => $request,
            'path' => $path,
            'middleware' => $middleware
        ];
    }
}
