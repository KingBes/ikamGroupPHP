<?php

namespace app\api\controller;

use app\Request;
use app\common\Route;
use app\model\Music;

class MusicController
{
    #[Route(path: '/api/music/index/{id}')]
    public function index(Request $request, int $id)
    {
        $find = Music::find($id);
        if ($find) {
            return response()->file(public_path() . "/music" . "/" . $find["title"] . "-" . $find["author"] . ".mp3");
        } else {
            return "没有效果";
        }
    }
}
