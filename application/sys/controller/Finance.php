<?php

namespace app\sys\controller;

use think\Db;

// 财务管理
class Finance extends Admin
{
    public function index()
    {
        echo '还没做';
        exit;
        $admin_info = session('admin_info');
        $data = Db::name('user')->order(['last_time'=>-1])->select();
        foreach ($data as $key => $value) { 
            $age  = calcAge($value['birthday']);
            $data[$key]['age'] = $age?$age.'岁':0;
            $data[$key]['last_time'] = $value['last_time']?date('Y-m-d H:i', $value['last_time']):'没消费过';
        } 

        return view('index',['data'=>$data]);
    }
 
}

