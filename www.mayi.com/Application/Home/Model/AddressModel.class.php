<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/9
 * Time: 14:12
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model{
    protected $patchValidate = true;

    protected $_validate = [
        ['name','require','收货人姓名不能为空'],
        ['province_id','require','省份不能为空'],
        ['city_id','require','市级城市不能为空'],
        ['area_id','require','区县不能为空'],
        ['detail_address','require','详细地址不能为空'],
        ['tel','require','手机不能为空'],
    ];

    /**
     * 添加一条用户收货地址到MySQL。
     * @return mixed
     */
    public function addAddress(){
        $userinfo = login();
        if(isset($this->data['is_default'])){
            //先将其它的默认改为非默认，然后添加
            $this->where(['member_id'=>$userinfo['id']])->setField('is_default',0);
        }
        $this->data['member_id'] = $userinfo['id'];
        return $this->add();
    }

    /**
     * 获取当前用户的所有收货地址。
     * @return mixed
     */
    public function getList() {
        $userinfo = login();
        return $this->where(['member_id'=>$userinfo['id']])->select();
    }

    /**
     * 获取指定的地址信息。
     * @param integer $id 地址id。
     * @param string  $field 要读取的字段列表。
     * @return array|null
     */
    public function getAddressInfo($id,$field = '*') {
        $userinfo = login();
        $cond = [
            'member_id'=>$userinfo['id'],
            'id'=>$id,
        ];
        return $this->field($field)->where($cond)->find();
    }
}