<?php

namespace App\Http\Controllers;


use App\Jobs\UpdateUserBalance;

class IndexController extends Controller
{

    public function Indeex()
    {
        $amt=1;
        $userid=1;
        $res=UpdateUserBalance::class::dispatch($userid,$amt);
        print_r("队列完成！");

    }
}
