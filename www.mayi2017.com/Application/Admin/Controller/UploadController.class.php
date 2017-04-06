<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/5 0005
 * Time: 下午 10:24
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Upload;

class UploadController extends Controller{
    //上传图片
    public function uploadImg(){
        //创建UPLOAD对象
        $options = C('UPLOAD_SETTING');
        $upload = new Upload($options);

        $file_info = $upload->uploadOne($_FILES['file_data']); //获取上传文件的信息

        if($file_info){
            if($upload->driver == 'Qiniu'){
                $file_url = $file_info['url'];
            }else{
                $file_url = BASE_URL.'/'.$file_info['savepath'].$file_info['savename'];
            }
            $return = [
                'file_url' =>$file_url,
                'msg'      =>'上传成功',
                'status'   =>1,
            ];
        }else{
            $return = [
                'file_url' =>'',
                'msg'      =>$upload->getError(),
                'status'   =>0,
            ];
        }
        echo $this->ajaxReturn($return);

    }

}