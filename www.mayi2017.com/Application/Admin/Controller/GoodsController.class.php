<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/6 0006
 * Time: 下午 11:33
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    /**
     * @var \Admin\Model\GoodsModel
     */
    protected $_model = null;
    protected function _initialize(){
        $this->_model = D('Goods');
    }
    public function index(){
        //接收查询条件,进行条件拼接
        //商品名字
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        //分类
        $goods_category_id = I('get.goods_category_id');
        if($goods_category_id){
            $cond['goods_category_id'] = $goods_category_id;
        }

        //是否上架
        $is_on_sale = I('get.is_on_sale');
        if(strlen($is_on_sale)){
            $cond['is_on_sale'] = $is_on_sale;
        }
        $is_on_sales    = [
            ['id' => 1, 'name' => '上架',],
            ['id' => 0, 'name' => '下架',],
        ];
        $this->assign($this->_model->getPageResult($cond));
        //取出商品所有分类
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList();
        $this->assign('goods_categories',$goods_categories);
        $this->assign('is_on_sales', $is_on_sales);
        $this->display();

    }
    public function add(){
        if(IS_POST){
            //收集数据
           if($this->_model->create() === false){
               $this->error(get_error($this->_model));
           }
            //添加商品
            if($this->_model->addGoods() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));
        }else{
            //获取所有的商品分类 使用ZTREE展示 转为JSON对象
            $goods_category_model = D('GoodsCategory');
            $goods_categories     = $goods_category_model->getList();
            $this->assign('goods_categories', json_encode($goods_categories));
            $this->display();
        }
    }
    public function edit($id) {
        if (IS_POST) {
            if($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            //修改商品
            if ($this->_model->saveGoods() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功', U('index'));
        } else {
            //1.获取数据
            $row = $this->_model->getGoodsInfo($id);
            //2.传递数据
            $this->assign('row', $row);
            //获取所有的商品分类 使用ZTREE展示 转为JSON对象
            $goods_category_model = D('GoodsCategory');
            $goods_categories     = $goods_category_model->getList();
            $this->assign('goods_categories', json_encode($goods_categories));
            $this->display('add');
        }
    }
    public function remove(){

    }

    /**
     * 移除相册表中的记录.
     * @param type $id
     */
    public function removeGallery($id) {
        $goods_gallery_model = M('GoodsGallery');
        if($goods_gallery_model->delete($id) ===false){
            $this->error('删除失败');
        } else{
            $this->success('删除成功');
        }
    }
}