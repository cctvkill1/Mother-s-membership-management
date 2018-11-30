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
                if (false !== strpos($ct, $dy)) {
                    $ct = date('m-d H:i', $value['ct']);
                }
                $data[$key]['ct'] = $ct;
            }
            $user_info = Db::name('user')->find($user_id);
            $age = calcAge($user_info['birthday']);
            $user_info['age'] = $age.'岁';
        }

        return view('index', ['data' => $data, 'user_info' => $user_info]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $admin_info = session('admin_info');
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
                Db::name('user')->where('id', $data['user_id'])->update(['balance' => $user_info['balance'], 'last_time' => time()]);
            } else {
                // 散客的消费直接计入收入
                Db::name('income')->insert(['money' => $data['money'], 'ct' => time(),  'type' => '散客消费']);
            }
            $data['admin_id'] = $admin_info['id'];
            $data['ct'] = time();
            Db::name('consumption_records')->insert($data);
            // 消费统计累加
            $date = date('Y-m-d');
            $find = Db::name('total')->where('date', $date)->find();
            if ($find) {
                Db::name('total')->where('date', $date)->update(['money' => $data['money'] + $find['money']]);
            } else {
                Db::name('total')->insert(['date' => $date, 'ct' => time(), 'money' => $data['money']]);
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
