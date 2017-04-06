<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/4 0004
 * Time: 上午 3:20
 */

namespace Admin\Controller;


use Think\Controller;

class MenuController extends Controller
{
    /**
     * @var \Admin\Model\MenuModel
     */
    private $_model = null;

    protected function _initialize(){
        $this->_model = D('Menu');
    }

    public function index(){
        //获取品牌列表
        $this->assign('rows',$this->_model->getList());

        $this->display();
    }
    public function add(){
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->addMenu() === false){
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
            if($this->_model->saveMenu() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('保存成功',U('index'));
        }else{
            //展示数据
            $row = $this->_model->getMenuInfo($id);
            $this->assign('row', $row);
            $this->_before_view();
            $this->display('add');
        }
    }
    /**
     * 物理删除节点,会同时删除后代节点
     * @param type $id
     */
    public function remove($id) {
        if ($this->_model->deleteMenu($id) === false) {
            $this->error(get_error($this->_model));
        } else {
            $this->success('删除成功', U('index'));
        }
    }

    private function _before_view(){
        $menus = $this->_model->getList();
        array_unshift($menus,['id'=>0,'name'=>'顶级菜单','prent_id'=>0]);
        $menus = json_encode($menus);
        $this->assign('menus',$menus);

        //获取权限列表
        $permission_model = D('Permission');
        $permissions = $permission_model->getList();
        $permissions = json_encode($permissions);
        $this->assign('permissions', $permissions);
    }
}