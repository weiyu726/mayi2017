<?php
/**
 * 将模型的错误信息转换为有序列表
 * @param \Think\Model $model 模型对象
 * @return string
 */
    function get_error(\Think\Model $model){
        $errors = $model->getError();
        if(!is_array($errors)){
            $errors = [$errors];
        }
        $html = '<ol>';
        foreach($errors as $error){
            $html .= '<il>'.$error.'</il>';
        }
        $html .= '<ol>';

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
 * @param $password 原密码
 * @param $salt  盐
 */
function salt_mcrypt($password,$salt){
    return md5(md5($password).$salt);
}

/**
 * 获取和设置用户session
 * @param mixed $data
 * @return type
 */

/**
 * 获取用户权限SESSINO
 *
 * @param  mixed $data
 */
function login($data = null){
    if(is_null($data)){
        return session('USERINFO');
    }else{
        session('USERINFO',$data);
    }
}

/**
 * 获取和设置用户权限路径 SESSINO
 *
 * @param  mixed $data
 */
function permission_pathes($data = null){
    if(is_null($data)){
        $pathes =  session('PERMISSIONS_PATHES');
        if(!is_array($pathes)){
            $pathes = [];
        }
        return $pathes;
    }else{
        session('PERMISSIONS_PATHES',$data);
    }
}
/**
 * 获取和设置用户权限 ID SESSINO
 *
 * @param  mixed $data
 */
function permission_pids($data = null){
    if(is_null($data)){
        $pids =  session('PERMISSION_PIDS');
        if(!is_array($pids)){
            $pids = [];
        }
        return $pids;
    }else{
        session('PERMISSION_PIDS',$data);
    }
}