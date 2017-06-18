<?php
/**
 * 将模型的错误信息转换成一个有序列表。
 * @param \Think\Model $model 模型对象
 * @return string
 */
function get_error(\Think\Model $model) {
    $errors = $model->getError();
    if (!is_array($errors)) {
        $errors = [$errors];
    }

    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '</ol>';
    return $html;
}

/**
 * 将一个关联数组转换成下拉列表
 * @param array  $data        关联数组,二维数组.
 * @param string $name_field  提示文本的字段名.
 * @param string $value_field value数据的字段名.
 * @param string $name        表单控件的name属性.
 * @return string 下拉列表的html代码.
 */
function arr2select(array $data, $name_field = 'name', $value_field = 'id', $name = '',$default_value='') {
    $html = '<select name="' . $name . '" class="' . $name . '">';
    $html .= '<option value=""> 请选择 </option>';
    foreach ($data as $key => $value) {
        //由于get和post提交的数据都是字符串,所以可能存在数字0和空字符串相等的问题
        //我们将遍历的数据变成string,然后强制类型比较.
        if((string)$value[$value_field] === $default_value){
            $html .= '<option value="' . $value[$value_field] . '" selected="selected">' . $value[$name_field] . '</option>';
        }else{
            $html .= '<option value="' . $value[$value_field] . '">' . $value[$name_field] . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

/**
 * 加盐加密
 * @param string $password 原密码
 * @param string $salt     盐
 */
function salt_mcrypt($password,$salt){
    return md5(md5($password).$salt);
}

/**
 * 获取和设置用户session
 * @param mixed $data
 * @return type
 */
function login($data=null){
    if(is_null($data)){
        return session('USERINFO');
    }else{
        session('USERINFO',$data);
    }
}

/**
 * 获取和设置用户权限session
 * @param mixed $data
 * @return type
 */
function permission_pathes($data=null){
    if(is_null($data)){
        $pathes = session('PERMISSION_PATHES');
        if(!is_array($pathes)){
            $pathes = [];
        }
        return $pathes;
    }else{
        session('PERMISSION_PATHES',$data);
    }
}
/**
 * 获取和设置用户权限ID session
 * @param mixed $data
 * @return type
 */
function permission_pids($data=null){
    if(is_null($data)){
        $pids = session('PERMISSION_PIDS');
        if(!is_array($pids)){
            $pids = [];
        }
        return $pids;
    }else{
        session('PERMISSION_PIDS',$data);
    }
}

function sendMail($email,$subject,$content){
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host       = 'smtp.126.com';  //填写发送邮件的服务器地址
    $mail->SMTPAuth   = true;                               // 使用smtp验证
    $mail->Username   = 'kunx_edu@126.com';                 // 发件人账号名
    $mail->Password   = 'iam4ge';                           // 密码
    $mail->SMTPSecure = 'ssl';                            // 使用协议,具体是什么根据你的邮件服务商来确定
    $mail->Port       = 465;                                    // 使用的端口

    $mail->setFrom('kunx_edu@126.com', 'ayiyayo');//发件人,注意:邮箱地址必须和上面的一致
    $mail->addAddress($email);     // 收件人

    $mail->isHTML(true);                                  // 是否是html格式的邮件

    $mail->Subject = $subject;//标题
    $mail->Body    = $content;//正文
    $mail->CharSet = 'UTF-8';

    if($mail->send()){
        return [
            'status'=>1,
            'msg'=>'发送成功',
        ];
    } else{
        return [
            'status'=>0,
            'msg'=>$mail->ErrorInfo,
        ];
        
    }
}

/**
 * 获取redis实例
 * @return Redis
 */
/*function get_redis(){
    $redis = new Redis();
    $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
    return $redis;
}*/

/**
 * 本地金钱表示形式：100 表示为 100.00
 * @param $number
 * @return string
 */
function locate_number_format($number){
    return number_format($number,2,'.','');
}