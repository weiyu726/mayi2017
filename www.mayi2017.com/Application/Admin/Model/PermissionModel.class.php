<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/12 0012
 * Time: 上午 12:29
 */

namespace Admin\Model;


use Admin\Logic\NestedSets;
use Think\Model;

class PermissionModel extends Model
{
    protected $_validate = [
        ['name', 'require', '权限名称不能为空'],
    ];

    public function getList()
    {
        return $this->where(['status' => 1])->order('lft')->select();
    }

    public function addPermission()
    {
        unset($this->data[$this->getPk()]);
        //创建ORM对象
        $orm = D('MySQL', 'Logic');
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加失败';
            return false;
        }
        return true;
    }

    /**
     * 编辑权限
     * @return bool
     */
    public function savePermission()
    {
        //判断是否修改了父级ID
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        if ($parent_id != $this->data['parent_id']) {
            //创建ORM对象
            $orm = D('MySQL', 'Logic');
            $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将分类移动到自身或后代分类';
                return false;
            }
        }
        return $this->save();
    }

    /**
     * 删除权限及其后代权限
     * 如果权限已经不存在,那么角色的关联也应当销毁,销毁的当然也是包括后代的.
     * @param type $id
     * @return boolean
     */
    public function deletePermission($id){
        $this->startTrans();
        //获取后代权限
        $permission_info = $this->field('lft,rght')->find($id);
        $cond = [
            'lft'=>['egt',$permission_info['lft']],
            'rght'=>['elt',$permission_info['rght']],
        ];
        $permission_ids = $this->where($cond)->getField('id',true);
        //删除角色-权限中间表的相关权限记录
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['permission_id'=>['in',$permission_ids]])->delete()===false){
            $this->error = '删除角色-权限关联失败';
            $this->rollback();
            return false;
        }
        //删除菜单和权限的关联
        $menu_permission_model = M('MenuPermission');
        //先删除历史关系
        if ($menu_permission_model->where(['permission_id' => $id])->delete() === false) {
            $this->error = '删除菜单————权限失败';
            $this->rollback();
            return false;
        }
        $orm = D('MySQL', 'Logic');
        $nestedsets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nestedsets->delete($id) === false) {
            $this->error = '删除失败';
            $this->rollback();
            return false;
        }else{
            $this->commit();
            return true;
        }

    }
}