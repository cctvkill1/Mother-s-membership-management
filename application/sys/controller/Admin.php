<?php

namespace app\sys\controller;

use think\Controller;

// 后台基类
class Admin extends Controller
{
    public function _initialize()
    {
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        $url = $controller.'/'.$action;
        // 不需要登录的方法
        $no_login = ['index/login', 'index/loginout', 'index/noaccess', 'index/four0four'];
        if (in_array($url, $no_login)) {
            return;
        }
        $admin_info = session('admin_info');
        if (!$admin_info) {
            return $this->redirect('index/login');
        }

        if (request()->isAjax()) {
            $this->view->engine->layout(false);
        }
    }

    public function _empty($name)
    {
        return $this->redirect('index/four0four');
    }

}
