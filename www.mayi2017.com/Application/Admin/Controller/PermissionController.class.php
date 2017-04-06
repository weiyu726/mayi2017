<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/12 0012
 * Time: 上午 12:11
 */

namespace Admin\Controller;


use Think\Controller;

class PermissionController extends Controller
{
    /**
     * @var \Admin\Model\PermissionModel
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('Permission');
    }
    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            //手机数据
           if($this->_model->create() === false){
               $this->error(get_error($this->_model));
           }
            //保存数据
            if($this->_model->addPermission() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功',U('index'));
        }else{
            //准备父级权限
            $this->_before_View();
            $this->display();
        }

    }
    public function edit($id){
        if(IS_POST){
            //手机数据
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            //保存数据
            if($this->_model->savePermission() === false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功',U('index'));
        }else{
        //获取数据
        $row = $this->_model->find($id);
        //传递数据
        $this->assign('row',$row);
        //全部权限列表，json字符串给ZTREE使用
        $this->_before_View();
        $this->display('add');
        }
    }
    public function remove($id){
        if($this->_model->deletePermission($id) === false){
            $this->error(get_error($this->_model));
        }
        $this->success('删除成功',U('index'));
    }

    protected function _before_View(){
        $permissions = $this->_model->getList();
        array_unshift($permissions,['id'=>0,'name'=>'顶级权限 ','parent_id'=>null]);
        $this->assign('permissions',json_encode($permissions));
    }
}