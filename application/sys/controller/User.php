<?php

namespace app\sys\controller;

use think\Db;

// 会员管理
class User extends Admin
{
    public function index()
    {
        $admin_info = session('admin_info');
        $data = Db::name('user')->order(['last_time'=>-1])->select();
        foreach ($data as $key => $value) {
            $age  = calcAge($value['birthday']);
            $data[$key]['age'] = $age?$age.'岁':0;
            $data[$key]['last_time'] = $value['last_time']?date('Y-m-d H:i', $value['last_time']):'没消费过';
        }

        return view('index', ['data'=>$data]);
    }
 
    public function add()
    {
        if (request()->isPost()) {
            $admin_info = session('admin_info');
            $data = input('param.', '');
            $id = $data['id'];
            $data['admin_id'] = $admin_info['id'];
            unset($data['id']);
            Db::name('user')->where(['id' => $id])->update($data, ['upsert' => true]);
            return success();
        }

        $id = input('param.id', '');
        if ($id) {
            $data = Db::name('user')->find($id);
        }
        return view('add', ['data' => $data ]);
    }
}
