<?php

namespace app\sys\controller;

use think\Db;

// 会员管理
class User extends Admin
{
    public function index()
    {
        $admin_info = session('admin_info');
        $key = input('param.keyword', '');
        $query = [];
        if ($key) {
            $query['name'] = ['like', $key];
        }
        $data = Db::name('user')->where($query)->order(['last_time' => -1])->select();
        foreach ($data as $key => $value) {
            $age = calcAge($value['birthday']);
            $data[$key]['age'] = $age ? $age.'岁' : 0;
            $data[$key]['last_time'] = $value['last_time'] ? date('Y-m-d H:i', $value['last_time']) : '没消费过';
        }

        return view('index', ['data' => $data]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $admin_info = session('admin_info');
            $data = input('param.', '');
            $id = $data['id'];
            unset($data['id']);
            $data['admin_id'] = $admin_info['id'];
            $data['ct'] = time();
            $data['balance'] = intval($data['balance']);
            $data['avatar'] = strval($data['avatar']);
            $data['avatar'] = $data['avatar'] ? $data['avatar'] : '/static/images/default_avatar.png';
            if (!$id && !$data['balance']) {
                return error('请填写办卡金额');
            }
            if ($id) {
                $data['recharge'] = intval($data['recharge']);
                $user_info = Db::name('user')->find($id);
                if ($data['recharge'] > 0) {
                    $data['balance'] = $user_info['balance'] + $data['recharge'];
                    // 计入充值收入
                    Db::name('income')->insert(['money' => $data['recharge'], 'ct' => time(), 'user_id' => $id, 'type' => '充值']);
                }
                Db::name('user')->where('id', $id)->update($data);
            } else {
                $id = Db::name('user')->insert($data);
                // 新增用户 余额大于0 计入充值收入
                if ($id && $data['balance'] > 0) {
                    Db::name('income')->insert(['money' => $data['balance'], 'ct' => time(), 'user_id' => $id, 'type' => '办卡']);
                }
            }

            return success();
        }

        $id = input('param.id', '');
        if ($id) {
            $data = Db::name('user')->find($id);
        }

        return view('add', ['data' => $data]);
    }

    public function del()
    {
        if (request()->isPost()) {
            $id = input('param.id', '');
            Db::name('user')->delete($id);
            Db::name('consumption_records')->where(['user_id' => $id])->delete();

            return success();
        }
    }
}
