<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3 0003
 * Time: 下午 5:27
 */

namespace Admin\Model;
use Admin\Logic\NestedSets;
use Think\Model;
class GoodsCategoryModel extends Model
{
    protected $patchValidate = true; //开启批量验证
    /**
     * name   必填不能重复
     * status 可选0-1
     * sort   必须为数字
     * @var
     */
    protected $_validate = [
        ['name', 'require', '分类名称不能为空'],
    ];

    /**
     * 获取分页数据和分页代码
     * @param array $cond 查询条件
     */
    public function getList(){
        return $this->where(['status'=>['egt',0]])->order('lft')->select();
    }

    /**
     * 完成分类的左右层级节点运算,和添加
     * 使用nestedsets
     */
    public function addCategory(){
        unset($this->data[$this->getPk()]);
        //创建ORM对象
        $orm = D('MySQL','Logic');
        //创建NESTEDSETS对象
        $nestedsets = new NestedSets($orm, $this->trueTableName,'lft', 'rght', 'parent_id','id', 'level');

        return  $nestedsets->insert($this->data['parent_id'],$this->data,'bottom');

    }

    /**
     * 修改节点
     * 编辑分内并且自动计算左右节点和层级
     * 不允许移动到后代分类下
     */
    public function saveCategory(){
        //判断是否修改了父级分类 如果没有修改 就不创建NESTEDSETS
        //获取原来的父级分类
        $db_info = $this->getFieldById($this->data['id'],'parent_id');
        if($this->data['parent_id'] != $db_info){
            $orm = D('MySQL','Logic');
            //创建NESTEDSETS对象
            $nestedsets = new NestedSets($orm, $this->trueTableName,'lft', 'rght', 'parent_id','id', 'level');
            //moveunder只计算左右节点和层级,不保存其他数据
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') ===false){
                $this->error = '不能移动到后代分类';
                return false;
            };
        }
        return  $this->save();
    }

    /**
     * 会将所有的后代全部删除  并从新计算左右节点
     * @param $id
     * @return bool
     */
    public function deleteCategory($id){
        $orm = D('MySQL','Logic');
        //创建NESTEDSETS对象
        $nestedsets = new NestedSets($orm, $this->trueTableName,'lft', 'rght', 'parent_id','id', 'level');
       return $nestedsets->delete($id);
    }
}