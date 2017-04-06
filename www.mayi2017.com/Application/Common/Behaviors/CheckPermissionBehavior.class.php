<?php

namespace Common\Behaviors;

class CheckPermissionBehavior extends \Think\Behavior{

    public function run(&$params)
    {
        //获取并验证权限
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        //获取用户信息
        $userinfo  =session('USERINFO');
        if(isset($userinfo['username']) && $userinfo['username'] == 'admin'){
            return true;
        }
        //配置所有用户都可访问页面
        $ignore_setting = C('ACCESS_STTING');
        $ignore = $ignore_setting['IGNORE'];
        $user_ignore =$ignore_setting['USER_IGNORE'];
        //允许访问的页面,角色出获取的权限 和忽略页表
        //获取权限列表
        $pathes = permission_pathes();
        $urls = array_merge($pathes,$ignore);
        if($userinfo){
            //额外加上登录出的忽略页表
            $urls = array_merge($urls,$user_ignore);
        }
        if(!in_array($url,$urls)){
            redirect(U('Admin/Admin/login'), 3, '无权访问');
        }
    }
}