<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/3 0003
 * Time: 下午 5:27
 */

namespace Admin\Model;
use Think\Model;
class SupplierModel extends Model
{
    protected $patchValidate = true; //开启批量验证
    /**
     * name   必填不能重复
     * status 可选0-1
     * sort   必须为数字
     * @var
     */
    protected $validate = [
        array('name','require','名称不能为空'), //默认情况下用正则进行验证
        array('name','','名称已经存在！',self::EXISTS_VALIDATE,'unique'), // 在新增的时候验证name字段是否唯一
        array('status','0,1','状态不合法',self::EXISTS_VALIDATE,'in'), // 当值不为空的时候判断是否在一个范围内
        array('sort','number','排序必须为数字'),
    ];

    /**
     * 获取分页数据和分页代码
     * @param array $cond 查询条件
     */
    public function getPageResult(array $cond=[]){
        //获取总行数
        //获取分页配置
        $page_setting = C('PAGE_SETTING');
        $count =$this->where($cond)->count();
        $page = new \Think\Page($count,$page_setting['PAGE_SIZE']);
        $page->setConfig('theme' ,$page_setting['PAGE_THEME']);
        $page_html = $page->show();
        $rows = $this->where($cond)->page(I('get.p',1),$page_setting['PAGE_SIZE'])->select();
        return [
            'rows' => $rows,
            'page_html'=>$page_html,
        ];

    }
}