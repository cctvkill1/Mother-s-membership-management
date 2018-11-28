<?php

namespace app\sys\controller;

use think\Db;

// 后台首页
class Index extends Admin
{
    public function index()
    {
        $admin_info = session('admin_info');
        // print_r(session_save_path());
        // 记录一次session 不能写的异常 发现是没权限 chown apache:apache /var/lib/php/session

        return view('index');
    }

    public function noaccess()
    {
        return view('noaccess');
    }

    public function four0four()
    {
        return view('four0four');
    }

    public function login()
    {
        if (request()->isPost()) {
            $username = input('post.username', '');
            $password = input('post.password', '');
            if (!$username) {
                return error('请输入姓名或手机号');
            }
            if (!$password) {
                return error('请输入密码');
            }
            $password = md5($password.config('password_secret'));
            $admin_info = Db::name('admin')->where(['username|phone' => $username, 'password' => $password])->find();
            if (!$admin_info) {
                return error('账号或密码错误');
            }
            session('admin_info', $admin_info);

            return success('登录成功');
        }

        return view('login');
    }

    public function loginOut()
    {
        session('admin_info', null);
        return success();
    }

    //base64 上传图片
    public function uploadImg()
    {
        $data = input('post.data', '');
        if ($data) {
            $res =  base64_upload($data);
            if ($res) {
                return success($res);
            } else {
                return error('上传失败');
            }
        }
        return error('请选择图片');
    }
}
