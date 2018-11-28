<?php

namespace app\sys\controller;

use think\Db;

// 消费
class Consume extends Admin
{
    // 最近消费记录
    public function index()
    {
        $admin_info = session('admin_info');
        $user_id = input('param.user_id', '');
        $query = [];
        if ($user_id) {
            $query['user_id'] = $user_id;
            $data = Db::name('consumption_records')->where($query)->order(['ct' => -1])->select();
            $dy = date('y');
            foreach ($data as $key => $value) {
                $ct = date('Y-m-d H:i', $value['ct']);
                if (strpos($ct, $dy)!==false) {
                    $ct = date('m-d H:i', $value['ct']);
                }
                $data[$key]['ct'] = $ct;
            }
        }

        return view('index', ['data' => $data]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $data = input('param.', '');
            $data['item'] = strval($data['item']);
            $data['money'] = intval($data['money']);
            if (!$data['item']) {
                return error('请填写消费项目');
            }
            if ($data['money'] <= 0) {
                return error('请填写消费金额');
            }
            if ($data['user_id']) {
                $user_info = Db::name('user')->find($data['user_id']);
                $user_info['balance'] -= $data['money'];
                $data['balance'] = $user_info['balance'] = intval($user_info['balance']);
                $data['ct'] = time();
                Db::name('user')->where('id', $data['user_id'])->update(['balance' => $user_info['balance'], 'last_time' => time()]);
                Db::name('consumption_records')->insert($data);
            }

            return success();
        }

        $user_id = input('param.user_id', '');
        $query = [];
        if ($user_id) {
            $query['user_id'] = $user_id;
            $user_info = Db::name('user')->find($user_id);
        }

        return view('add', ['user_info' => $user_info]);
    }
}
