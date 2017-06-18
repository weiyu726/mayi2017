<?php

namespace Common\Behaviors;

class CheckPermissionBehavior extends \Think\Behavior{

    public function run(&$params)
    {
        //获取并验证权限
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        //配置所有用户都可访问页面
        $ignore_setting = C('ACCESS_IGNORE');
        //配置所有用户都可以访问的页面
        $ignore = $ignore_setting['IGNORE'];
        if(in_array($url, $ignore)){
            return true;
        }
        //获取用户信息
        $userinfo  =login();
        //如果没有登录就自动登录
        if(!$userinfo){
            $userinfo = D('Admin')->autoLogin();
        }
        if(isset($userinfo['username']) && $userinfo['username'] == 'admin'){
            return true;
        }
        //获取权限列表
        $pathes = permission_pathes();
        //允许访问的页面,角色出获取的权限 和忽略页表
        $user_ignore =$ignore_setting['USER_IGNORE'];
        $urls = $pathes;
        if($userinfo){
            //额外加上登录出的忽略页表
            $urls = array_merge($urls,$user_ignore);
        }
        if(!in_array($url,$urls)){
            header('Content-Type: text/html;charset=utf-8');
            redirect(U('Admin/Admin/login'), 3, '无权访问');
        }
    }
}