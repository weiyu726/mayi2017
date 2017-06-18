<?php

namespace Home\Model;
class MemberModel extends \Think\Model{
    protected $patchValidate = true;
    /**
     * 1.username 必填 唯一
     * 2.password 必填 长度6-16位
     * 3.repassword 必须和password一样
     * 4.email 必填  唯一
     * 5.手机号码 必填  唯一  手机号码合法
     * 
     * 6.验证手机验证码是否合法
     * 7.验证图片验证码是否合法
     */
    protected $_validate = [
        ['username','require','用户名不能为空'],
        ['username','','用户名已存在',self::EXISTS_VALIDATE,'unique','reg'],
        ['password','require','密码不能为空'],
        ['password','6,16','密码必须6-16位',self::EXISTS_VALIDATE,'length'],
        ['repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'],
        ['email','require','邮箱不能为空'],
        ['email','email','邮箱不合法'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
        ['tel','require','手机号码不能为空'],
        ['tel','/^1[34578]\d{9}$/','手机号码不合法',self::EXISTS_VALIDATE,'regex'],
        ['email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'],
//        ['checkcode','require','图片验证码不能为空'],
//        ['checkcode','checkImgCode','图验证码不正确',self::EXISTS_VALIDATE,'callback'],
        ['captcha','require','手机验证码不能为空'],
        ['captcha','checkTelCode','手机验证码不正确',self::EXISTS_VALIDATE,'callback'],
    ];
    
    /**
     * add_time: NOW_TIME
     * salt:随机
     */
    protected $_auto = [
        ['add_time',NOW_TIME,'reg'],
        ['salt','\Org\Util\String::randString','reg','function'],
        ['register_token','\Org\Util\String::randString','reg','function',32],
        ['status',0,'reg'],//没有通过邮件验证的账号是禁用账户
    ];
    
    /**
     * 验证图片验证码.
     * @param type $code
     * @return type
     */
    protected function checkImgCode($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    
    /**
     * 验证手机验证码.
     * @param type $code
     * @return boolean
     */
  /*  protected function checkTelCode($code) {
        if($code == session('reg_tel_code')){
            session('reg_tel_code',null);
            return true;
        }else{
            return false;
        }
    }*/
    
    /**
     * 注册
     */
    public function addMember() {
        //加盐加密
        $this->data['password']=  salt_mcrypt($this->data['password'], $this->data['salt']);
        $register_token = $this->data['register_token'];
        $email = $this->data['email'];
        if($this->add()===false){
            return false;
        }
        
        //发送激活邮件
        $email;
        $url = U('Member/active',['email'=>$email,'register_token'=>$register_token],true,true);
        $subject = '欢迎注册啊咿呀哟母婴商城';
        $content = '欢迎您注册我们的网站,请点击<a href="'.$url.'">链接</a>激活账号.如果无法点击,请复制以下链接粘贴到浏览器窗口打开!<br />' . $url;
        
        $rst = sendMail($email,$subject,$content);
        if($rst['status']){
            return true;
        }else{
            $this->error = $rst['msg'];
            return false;
        }
    }

    public function login() {
        //检查是否有这个用户
        $username = $this->data['username'];
        $password = $this->data['password'];
        if(!$userinfo = $this->getByUsername($username)){
            $this->error = '用户名或密码错误';
            return false;
        }

        if(salt_mcrypt($password,$userinfo['salt']) != $userinfo['password']){
            $this->error = '用户名或密码错误';
            return false;
        }

        //记录用户的登陆时间
        $data = [
            'id'=>$userinfo['id'],
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=>get_client_ip(1),
        ];
        $this->setField($data);
        //将用户信息保存到session中.
        login($userinfo);

        //获取cookie购物车数据保存到MySQL中，并清空cookie
        $shopping_car_model = D('ShoppingCar');
        $shopping_car_model->cookie2db();

        return $userinfo;
    }
}
