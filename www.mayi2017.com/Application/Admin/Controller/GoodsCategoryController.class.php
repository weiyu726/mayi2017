<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/6 0006
 * Time: 上午 12:15
 */

namespace Admin\Controller;

use Think\Controller;

class GoodsCategoryController extends Controller{
    /**
     * @var \Admin\Model\GoodsCategoryModel
     */
    private $_model=null;
    protected function _initialize(){
       $this->_model = D('GoodsCategory');
}
    public function index(){

        $this->assign('rows',$this->_model->getList());
        $this->display();
    }

    public function add(){
        //收集数据
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->_before_view();
            $this->display();
        }
    }

    public function edit($id){
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->saveCategory() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功',U('index'));
        }else{
            $row = $this->_model->find($id);
            $this->assign('row',$row);
            $this->_before_view();
            $this->display('add');
        }
    }

    public function remove($id) {
        if($this->_model->deleteCategory($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }

    private function _before_view() {
        $goods_categories = $this->_model->getList();
        array_unshift($goods_categories, ['id' => 0, 'name' => '顶级分类', 'parent_id' => 0]);
        $goods_categories = json_encode($goods_categories);
        $this->assign('goods_categories', $goods_categories);
    }
}