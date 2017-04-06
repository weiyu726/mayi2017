<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24 0024
 * Time: 下午 9:46
 */

namespace Admin\Model;
use Think\Model;

class RoleModel extends Model{

    /**
     * 获取分页数据
     * @param array $cond
     * @return type
     */
    public function getPageResult(array $cond=[]) {
        //查询条件
        $cond = array_merge(['status'=>1],$cond);
        //总行数
        $count = $this->where($cond)->count();
        //获取配置
        $page_setting = C('PAGE_SETTING');
        //工具类对象
        $page = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        //设置主题
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        //获取分页代码
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return compact('rows', 'page_html');
    }

    public function addRole() {
        $this->startTrans();
        //1.保存基本信息
        if(($role_id = $this->add())===false){
            $this->rollback();
            return false;
        }

        //2.保存关联的权限
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if($data){
            $role_permission_model = M('RolePermission');
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }

        $this->commit();
        return true;
    }

    public function getPermissionsInfo($id){
        //获取基本信息
       $row =  $this->find($id);
        //获取关联权限
        $role_permission_model = M('RolePermission');
        $row['permission_ids'] =json_encode($role_permission_model->where(['Role'=>$id])->getField('permission_id',true));
        return $row;

    }
    public function saveRole(){
        $this->startTrans();
        $role_id = $this->data['id'];
        //保存基本信息
        if ($this->save() === false){
            $this->rollback();
            return false;
        }
        //删除原有的
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$role_id])->delete()===false){
            $this->error = '删除历史权限失败';
            $this->rollback();
            return false;
        }
        //保存关联的权限
        $permission_ids = I('post.permission_id');
        $data = [];
        foreach($permission_ids as $permission_id){
            $data[] = [
                'role_id'=>$role_id,
                'permission_id'=>$permission_id,
            ];
        }
        if($data){
            if($role_permission_model->addAll($data) ===false){
                $this->error = '保存权限失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;

    }

    /**
     * 删除角色,同时删除对应的权限关联.
     * @param integer $id 角色id.
     */
    public function deleteRole($id) {
        $this->startTrans();
        //删除角色记录
        if($this->delete($id) === false){
            $this->rollback();
            return false;
        }

        //删除权限关联
        $role_permission_model = M('RolePermission');
        if($role_permission_model->where(['role_id'=>$id])->delete()===false){
            $this->error = '删除权限关联失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    public function getList(){
        return $this->where(['status'=>1])->select();
    }
}