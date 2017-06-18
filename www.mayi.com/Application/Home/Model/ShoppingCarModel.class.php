<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/8
 * Time: 15:36
 */

namespace Home\Model;


use Think\Model;

class ShoppingCarModel extends Model {

    /**
     * 获取购物车中指定商品的数量.
     * @param integer $goods_id 商品id.
     * @return integer
     */
    public function getAmountByGoodsId($goods_id) {
        $userinfo = login();
        $cond     = [
            'member_id' => $userinfo['id'],
            'goods_id' => $goods_id,
        ];
        return $this->where($cond)->getField('amount');
    }

    /**
     * 将数据表中,指定的商品购买数量增加.
     * @param integer $goods_id 商品id.
     * @param integer $amount 商品的数量.
     * @return bool
     */
    public function addAmount($goods_id, $amount) {
        $userinfo = login();
        $cond     = [
            'member_id' => $userinfo['id'],
            'goods_id' => $goods_id,
        ];
        return $this->where($cond)->setInc('amount', $amount);
    }

    /**
     * 将商品添加到数据库中.
     * @param integer $goods_id 商品id.
     * @param integer $amount 商品的数量.
     * @return bool
     */
    public function add2car($goods_id, $amount) {
        $userinfo = login();
        $data     = [
            'member_id' => $userinfo['id'],
            'goods_id' => $goods_id,
            'amount' => $amount,
        ];
        return $this->add($data);
    }


    public function cookie2db() {
        $userinfo   = login();
        $key        = C('SHOPPING_CAR_COOKIE_KEY');
        $cookie_car = cookie($key);
        if (!$cookie_car) {
            return true;
        }
        $cond = [
            'member_id' => $userinfo['id'],
            'goods_id' => [
                'in', array_keys($cookie_car),
            ],
        ];
        //删除数据表中同样商品的记录
        if ($this->where($cond)->delete() === false) {
            return false;
        }
        //添加cookie中的到数据表
        $data = [];
        foreach ($cookie_car as $key => $value) {
            $data[] = [
                'goods_id' => $key,
                'amount' => $value,
                'member_id' => $userinfo['id'],
            ];
        }

        return $this->addAll($data);

    }

    /**
     * 获取购物车列表。区分是否登陆
     * 1.登陆状态
     *  1.1 从MySQL中获取商品的id和amount
     *  1.2 从goods表中获取商品的logo shop_price name
     *  1.3 准备完整的数据给前端
     * 2.未登录状态
     *  1.1 从cookie中获取商品的id和amount
     *  1.2 从goods表中获取商品的logo shop_price name
     *  1.3 准备完整的数据给前端
     */
    public function getShoppingCarList() {
        //判断是否登陆
        $userinfo = login();
        //获取商品的id和amount
        /**
         * [gid=>amount]
         */
        if ($userinfo) {
            $car_list = $this->where(['member_id' => $userinfo['id']])->getField('goods_id,amount');
        } else {
            $car_list = cookie(C('SHOPPING_CAR_COOKIE_KEY'));
        }

        if(!$car_list){
            return [
                'total_price' => '0.00',
                'goods_info_list'=>[],
            ];
        }

        //获取出商品的详细信息
        //有商品
        $goods_model = M('Goods');
        $cond        = [
            'id' => ['in', array_keys($car_list)],
            'is_on_sale'=>1,
            'status'=>1,
        ];
        /**
         * [
         *  gid=>['id'=>'','name'=>'','logo'=>'']
         * ]
         */
        $goods_info_list = $goods_model->where($cond)->getField('id,name,logo,shop_price');
        $total_price = 0.00;
        //读取用户的积分
        $score = M('Member')->where(['id'=>$userinfo['id']])->getField('score');
        //获取用户的级别

        //   bottom    socre    top
        $cond = [
            'bottom'=>['elt',$score],
            'top'=>['egt',$score],
        ];
        $member_level = M('MemberLevel')->where($cond)->field('id,discount')->find();
        $member_level_id = $member_level['id'];
        $discount = $member_level['discount'];
        //获取用户的会员价
        $member_goods_price_model = M('MemberGoodsPrice');
        foreach($car_list as $goods_id=>$amount){
            //获取当前商品的会员价
            $cond = [
                'goods_id'=>$goods_id,
                'member_level_id'=>$member_level_id,
            ];
            $member_price = $member_goods_price_model->where($cond)->getField('price');
            if($member_price){
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($member_price);
            }elseif($userinfo){
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($goods_info_list[$goods_id]['shop_price'] * $discount / 100);

            }else{
                $goods_info_list[$goods_id]['shop_price'] = locate_number_format($goods_info_list[$goods_id]['shop_price']);
            }












            //此时应当将会员价读取出来
            $goods_info_list[$goods_id]['amount'] = $amount;

            $goods_info_list[$goods_id]['sub_total'] = locate_number_format($goods_info_list[$goods_id]['shop_price'] * $amount);
            $total_price += $goods_info_list[$goods_id]['sub_total'];

        }
        $total_price = locate_number_format($total_price);
        return compact('total_price','goods_info_list');
    }

    /**
     * 删除购物车数据。
     * @return mixed
     */
    public function clearShoppingCar() {
        $userinfo = login();
        return $this->where(['member_id'=>$userinfo['id']])->delete();
    }
}