<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/9
 * Time: 11:38
 */

namespace Home\Model;


use Think\Model;

class LocationsModel extends Model{

    /**
     * 获取指定城市的子级城市
     * @param int $parent_id
     * @return mixed
     */
    public function getListByParentId($parent_id=0) {
        return $this->where(['parent_id'=>$parent_id])->select();
    }
}