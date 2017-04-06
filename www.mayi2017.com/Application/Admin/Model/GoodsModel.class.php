<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/6 0006
 * Time: 下午 11:39
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model
{

    //批量验证
    protected $patchValidate = true;
    //自动验证
    /**
     * 1. 商品名必填
     * 2. 商品分类必填
     * ...
     */
    protected $_validate     = [
        ['name', 'require', '商品名称不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE],
        ['goods_category_id', 'require', '商品分类不能为空'],
    ];

    //自动完成
    protected $_auto         = [
        ['sn', 'createSn', self::MODEL_INSERT, 'callback'],
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
    ];

    protected function createSn($sn) {
        $this->startTrans();
        //如果已经提交了,就什么都不做
        if ($sn) {
            return $sn;
        }
        //生成规则:SN年月日编号:SN2016062800001
        //1.获取今天已经常见了多少个商品
        $date            = date('Ymd');
        $goods_num_model = M('GoodsNum');
        //`保存到数据表中
        if ($num             = $goods_num_model->getFieldByDate($date, 'num')) {
            ++$num;
            $data = ['date' => $date, 'num' => $num];
            $flag = $goods_num_model->save($data);
        } else {
            $num  = 1;
            $data = ['date' => $date, 'num' => $num];
            $flag = $goods_num_model->add($data);
        }
        if ($flag === false) {
            $this->rollback();
        }
        //2.计算SN
        $sn = 'SN' . $date . str_pad($num, 5, '0', STR_PAD_LEFT);

        return $sn;
    }

    public function addGoods(){
        //保存基本信息
        $this->startTrans();
        unset($this->data['id']);
        if (($goods_id = $this->add()) === false) {
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        $data              = [
            'goods_id' => $goods_id,
            'content'  => I('post.content', '', false),
        ];
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->add($data) === false) {
            $this->rollback();
            return false;
        }
        //3.保存相册
        $goods_gallery_model = M('GoodsGallery');
        $pathes = I('post.path');
        $data = [];
        foreach($pathes as $path){
            $data[] = [
                'goods_id'=>$goods_id,
                'path'=>$path,
            ];
        }
        //如果上传了相册,并且相册保存失败,就回滚
        if($data && ($goods_gallery_model->addAll($data)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }
    /**
     * 获取分页数据
     * @param array $cond 查询条件.
     * @return type
     */
    public function getPageResult(array $cond = []) {
        $cond         = array_merge(['status' => 1], $cond);
        //1.获取总条数
        $count        = $this->where($cond)->count();
        //2.获取分页代码
        $page_setting = C('PAGE_SETTING');
        $page         = new \Think\Page($count, $page_setting['PAGE_SIZE']);
        $page->setConfig('theme', $page_setting['PAGE_THEME']);
        $page_html    = $page->show();
        //3.获取分页数据
        $rows         = $this->where($cond)->page(I('get.p', 1), $page_setting['PAGE_SIZE'])->select();
        //由于列表页要展示是否是新品精品热销,但是这些信息放在一个字段中,所以为了简化视图代码,我们在模型中处理好后再返回

        return compact('rows', 'page_html');
    }

    public function getGoodsInfo($id) {
        //获取商品的基本信息
        $row = $this->find($id);
        //获取商品的详细描述
        $goods_intro_model = M('GoodsIntro');
        $row['content'] = $goods_intro_model->getFieldByGoodsId($id,'content');
        //获取商品的相册
        $goods_gallery_model = M('GoodsGallery');
        $row['galleries']=$goods_gallery_model->getFieldByGoodsId($id,'id,path');
        return $row;
    }

    /**
     * 修改商品 包括商品详细描述和相册.
     * @return boolean
     */
    public function saveGoods() {
        $request_data = $this->data;
        $this->startTrans();
        //1.保存基本信息
        if($this->save()===false){
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        $data              = [
            'goods_id' => $request_data['id'],
            'content'  => I('post.content', '', false),
        ];
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->save($data) === false) {
            $this->rollback();
            return false;
        }
        $goods_gallery_model = M('GoodsGallery');
        $pathes = I('post.path');
        $data = [];
        foreach($pathes as $path){
            $data[] = [
                'goods_id'=>$request_data['id'],
                'path'=>$path,
            ];
        }
        //如果上传了相册,并且相册保存失败,就回滚
        if($data && ($goods_gallery_model->addAll($data)===false)){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}