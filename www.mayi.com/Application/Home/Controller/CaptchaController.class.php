<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of CaptchaController
 *
 * @author qingf
 */
class CaptchaController extends \Think\Controller{
    public function captcha() {
        $setting = ['length'=>4];
        $verify = new \Think\Verify($setting);
        $verify->entry();
    }
}
