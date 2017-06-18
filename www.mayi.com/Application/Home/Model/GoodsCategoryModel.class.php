<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/7
 * Time: 11:12
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model{

    public function getList($field='*') {
        return $this->field($field)->where(['status'=>1])->select();
    }
}