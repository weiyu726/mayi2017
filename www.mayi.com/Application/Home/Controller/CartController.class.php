<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/8
 * Time: 15:28
 */

namespace Home\Controller;


use Think\Controller;

/**
 * 购物车相关逻辑的控制器.
 * Class CartController
 * @package Home\Controller
 */
class CartController extends Controller {

    /**
     * @var \Home\Model\ShoppingCarModel
     */
    private $_model = null;

    protected function _initialize() {
        $this->_model = D('ShoppingCar');
    }

    public function add2car($id, $amount) {
        $userinfo = login();
        if (!$userinfo) {
            //未登录
            $key      = C('SHOPPING_CAR_COOKIE_KEY');
            $car_list = cookie($key);
            /**
             * [
             *  'goods_id'=>amount,
             *  'goods_id'=>amount,
             * ]
             * 假设我们的需求是:
             * 如果cookie中已经有了此商品,再在详情页添加,其实是增加商品的数量
             */
            if (isset($car_list[$id])) {
                $car_list[$id] += $amount;
            } else {
                $car_list[$id] = $amount;
            }
            cookie($key, $car_list, 604800);//保存一周
        } else {
            //已登录
            //获取当前商品的数量
            $db_amount = $this->_model->getAmountByGoodsId($id);
            if ($db_amount) {
                //如果已经存在,就加数量
                $this->_model->addAmount($id, $amount);
            } else {
                //如果不存在,就加记录
                $this->_model->add2car($id, $amount);
            }
        }
        //跳转到购物车列表页面
        $this->success('添加成功', U('flow1'));

    }

    /**
     * 购物车列表
     */
    public function flow1() {
        $car_list = $this->_model->getShoppingCarList();
        $this->assign($car_list);
        $this->display();
    }

    /**
     * 填写订单信息，比如收获地址，发票信息，配送方式
     * 此页面必须登陆才能看到。
     */
    public function flow2() {
        $userinfo = login();
        if (!$userinfo) {
            cookie('__FORWARD__', __SELF__);//将当前页面地址保存到cookie中，以便能够登陆后跳转
            $this->error('本店不招待无名之辈', U('Member/login'));
        }

        //1.获取收获地址列表
        $address_model = D('Address');
        $this->assign('addresses', $address_model->getList());
        //2.获取配送方式列表
        $delivery_model = D('Delivery');
        $this->assign('deliveries', $delivery_model->getList());
        //3.获取支付方式列表
        $payment_model = D('Payment');
        $this->assign('payments', $payment_model->getList());

        //4.获取购物车数据
        $car_list = $this->_model->getShoppingCarList();
        $this->assign($car_list);
        $this->display();

    }
}