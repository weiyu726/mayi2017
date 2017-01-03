<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/2 0002
 * Time: 下午 7:28
 */

namespace Admin\Controller;
use Think\Controller;

class SupplierController extends Controller
{
    /**
     * @var \Admin\Model\SupplierModel
     */
    //构造模型对象
    // $this->_model = D('supplier');
    private $_model=null;
    protected  function _initialize(){
        $this ->_model =  D('supplier');
    }
    /**
     * 列表页面
     */
    public function index(){
        //搜索
        $name = I('get.name');
        $cond['status'] = ['egt',0];
        if($name){
            $cond['name']=['like','%'.$name.'%'];
        }

        //查询数据
        $date =$this->_model->getPageResult($cond);

        $this->assign($date);
        //调用视图
        $this->display();


    }

    /**
     * 添加页面
     */
    public function add(){
        if(IS_POST){

            //收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            };
            //保存数据
            if($this->_model->add() === false){
                $this->error(get_error($this->_model));
            }else{
                //提示跳转
                $this->success('添加成功',U('index'));
            }
        }else{
            $this->display();
        }
    }
    //修改页面
    public function edit($id){

            if(IS_POST){
                if($this->_model->create()===false){
                    $this->error(get_error($this->_model));
                }
                if($this->_model->save()===false){
                    $this->error(get_error($this->_model));
                }
                $this->success('修改成功',U('index'));
            }else{
                $rows =$this->_model->find($id);
                $this->assign('rows',$rows);
                $this->display();

            }

    }
    //逻辑删除
    public function remove($id,$name){

        if($this->_model->setField(['id'=>$id,'status'=>-1,'name'=>['exp','concat(name,"_del")']])===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('index'));
        }
    }
    //回收站
    public function rubbish(){
        //搜索
        $name = I('get.name');
        $cond['status'] = [-1];
        if($name){
            $cond['name']=['like','%'.$name.'%'];
        }
        //查询数据
        $date =$this->_model->getPageResult($cond);

        $this->assign($date);
        //调用视图
        $this->display();


    }
    //物理删除
    public function del($id){

        if($this->_model->delete($id)===false){
            $this->error(get_error($this->_model));
        }else{
            $this->success('删除成功',U('rubbish'));
        }
    }


}