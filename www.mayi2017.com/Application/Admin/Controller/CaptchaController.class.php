<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6 0006
 * Time: 上午 1:49
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;

class CaptchaController extends Controller
{
    public function captcha()
    {
        $setting = [
            'length' =>4,
        ];
        $verify = new Verify($setting);
        $verify->entry();
    }
}