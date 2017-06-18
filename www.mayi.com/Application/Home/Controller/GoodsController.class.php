<?php
/**
 * Created by PhpStorm.
 * User: qingf
 * Date: 2016/7/8
 * Time: 10:46
 */

namespace Home\Controller;


use Think\Controller;

class GoodsController extends Controller{

    /**
     * 获取商品点击次数.
     * @param integer $id 商品id
     */
    public function clickTimes($id) {
        $goods_click_model = M('GoodsClick');
        //获取历史点击次数
        $num = $goods_click_model->getFieldByGoodsId($id,'click_times');
        //存储新的点击次数
        if(!$num){
            $num = 1;
            $data = [
                'goods_id'=>$id,
                'click_times'=>$num,
            ];
            $goods_click_model->add($data);
        } else{
            ++$num;
            $data = [
                'goods_id'=>$id,
                'click_times'=>$num,
            ];
            $goods_click_model->save($data);
        }
        $this->ajaxReturn($num);
    }

    /**
     * 从redis中获取商品的点击次数.
     * @param integer $id 商品id.
     */
    public function getClickTimes($id) {
        $redis = get_redis();
        $key = 'goods_clicks';
        $this->ajaxReturn($redis->zIncrBy($key,1,$id));
    }


    /**
     * 将redis中的点击次数保存到数据库中.
     * @return bool|string
     */
    public function syncGoodsClicks() {
        $redis = get_redis();
        $key = 'goods_clicks';
        //获取到所有商品的点击次数
        $goods_clicks = $redis->zRange($key,0,-1,true);

        //一次插入500-1000条分段
//        $tmp = array_chunk($goods_clicks,1000,true);//遍历里面的第一维,然后重复使用下面的代码


        if(empty($goods_clicks)){
            return true;
        }

        //将redis中点击数保存到数据表中
        $goods_click_model = M('GoodsClick');
        //删除所有的已经存在的数据
        $goods_ids = array_keys($goods_clicks);
        $goods_click_model->where(['goods_id'=>['in',$goods_ids]])->delete();

        //将redis中的数据保存到数据表中
        $data = [];
        foreach($goods_clicks as $key=>$value){
            $data[] = [
                'goods_id'=>$key,
                'click_times'=>$value,
            ];
        }
        echo '<script type="text/javascript">window.close();</script>';
        return $goods_click_model->addAll($data);
    }
}