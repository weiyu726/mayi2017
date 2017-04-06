<?php

namespace Admin\Model;

use Think\Verify;

class AdminModel extends \Think\Model{
    protected $patchValidate = true;
    
    /**
     * 1.username必填 唯一
     * 2.password必填 长度6-16位
     * 3.repassword 和password一致
     * 4.email 必填 唯一
     * @var type 
     */
    protected $_validate = [
        ['username','require','用户名不能为空'],
        ['username','','用户名已被占用',self::EXISTS_VALIDATE,'unique','register'],
        ['password','require','密码不能为空',self::EXISTS_VALIDATE],
        ['password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱格式不合法',self::EXISTS_VALIDATE],
        ['email','','邮箱已被占用',self::EXISTS_VALIDATE,'unique'],
        ['captcha','checkCaptcha','验证码不正确',self::EXISTS_VALIDATE,'callback'],
    ];
    
    /**
     * 1. add_time 当前时间
     * 2. 盐 自动生成随机盐
     * @var type 
     */
    protected $_auto = [
        ['add_time', NOW_TIME, 'register'],
        ['salt', '\Org\Util\String::randString', 'register', 'function']
    ];

    /**
     * 验证验证码是否匹配
     * @param string $code 用户输入的验证码
     * @return bool
     */
    protected function checkCaptcha($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    /**
     * 获取分页数据
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond=[]) {
        //查询条件
        $cond = array_merge(['status'=>1],$cond);
        //总行数
        $count = $this->where($cond)->count();
        //获取配置
        $page_setting = C('PAGE_SETTING');
        //工具类对象
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        //设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }
    
    /**
     * 创建管理员.
     * @return type
     */
    public function addAdmin() {
        $this->startTrans();
        //加盐加密
        $this->data['password'] = salt_mcrypt($this->data['password'], $this->data['salt']);
       if(($admin_id = $this->add())===false){
            $this->rollback();
            return false;
        }
        //保存管理员角色关联
        $admin_role_model = M('AdminRole');
        $data = [];
        $role_ids = I('post.role_id');
        foreach($role_ids as $role_id){
            $data[] = [
                'admin_id'=>$admin_id,
                'role_id'=>$role_id,
            ];
        }
        if($data){
            if($admin_role_model->addAll($data)===false){
                $this->error = '保存角色关联失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }
    
  public function getAdminInfo($id) {
        $row = $this->find($id);
        $admin_role_model = M('AdminRole');
        $row['role_ids'] = json_encode($admin_role_model->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
   }
    
    /**
     * 修改管理员.
     * @param integer $id 管理员id.
     * @return boolean
     */
    public function saveAdmin($id) {
        $this->startTrans();
        //保存管理员角色关联
        $admin_role_model = M('AdminRole');
        //删除关联的角色
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error = '删除原有的角色失败';
            $this->rollback();
            return false;
        }
        $data = [];
        $role_ids = I('post.role_id');
        foreach($role_ids as $role_id){
            $data[] = [
                'admin_id'=>$id,
                'role_id'=>$role_id,
            ];
        }
        if($data){
            if($admin_role_model->addAll($data)===false){
                $this->error = '保存角色关联失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }
    
    /**
     * 删除管理员,同时删除角色关联.
     * @param integer $id 管理员id
     * @return boolean
     */
   public function deleteAdmin($id) {
      $this->startTrans();
      //1.删除admin中的管理员记录
       if($this->delete($id)===false){
           $this->rollback();
           return false;
       }
       //2.删除admin和role的关联关系
       $admin_role_model = M('AdminRole');
      //删除关联的角色
      if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
          $this->error = '删除角色关联失败';
           $this->rollback();
           return false;
        }
       $this->commit();
      return true;
   }

    public function login()
    {
        $username = $this->data['username'];
        $password = $this->data['password'];
        $userinfo = $this->getByUsername($username);

        if(!$userinfo){
            $this->error = '用户名或者密码错误';
            return false;

        }
        //验证密码

        $salt_password = salt_mcrypt($password,$userinfo['salt']);
        if($salt_password != $userinfo['password']){
            $this->error = '用户名或者密码错误';

            return false;
        }
        //保存用户的最后登录时间和IP
        $data =[
            'last_login_time' => NOW_TIME,
            'last_login_ip'   =>get_client_ip(1),
            'id'              =>$userinfo['id'],
        ];
        $this->save($data);
        //将用户数据进行保存
        permissions($userinfo);
        //获取用户权限
        $this->getPermissions($userinfo['id']);

        //自动登录相关
        if(I('post.remember')){
        //生成COOKIE
            $data =[
                'admin_id'=>$userinfo['id'],
                'token'   =>\Org\Util\String::randstring(40),

            ];
            cookie('USER_AUTO_LOGIN_TOKEN',$data,604800);//保存一星期
            $admin_token_model = M('AdminToken');
            $admin_token_model->add($data);
        }
        return $userinfo;
    }

    public function getPermissions($admin_id)
    {
        //SELECT DISTINCT	path FROM admin_role AS ar JOIN role_permission AS rp ON ar.`role_id` =rp.`role_id` JOIN permission AS p ON p.`id`=rp.`permission_id` WHERE path<>'' AND admin_id=2;
        $cond        = [
            'path'     => ['neq', ''],
            'admin_id' => $admin_id,
        ];
        $permissions = M()->distinct(true)->field('permission_id,path')->table('admin_role')->alias('ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON p.`id`=rp.`permission_id`')->where($cond)->select();
        $pids = [];
        $paths       = [];
        foreach ($permissions as $permission) {
            $paths[] = $permission['path'];
            $pids[] = $permission['permission_id'];
        }
        permission_pathes($paths);
       // permission_pids($pids);
        return true;
    }
}
