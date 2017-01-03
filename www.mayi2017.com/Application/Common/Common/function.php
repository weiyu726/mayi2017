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