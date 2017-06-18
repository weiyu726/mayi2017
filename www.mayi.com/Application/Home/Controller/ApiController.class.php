<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of ApiController
 *
 * @author qingf
 */
class ApiController extends \Think\Controller{
    public function regSms($tel) {
        //发送短信
        //引入topSdk.php
        Vendor('Alidayu.TopSdk');
        $c            = new \TopClient;
        $c->appkey    = '23399157';
        $c->secretKey = '9c636f9add5b83d92b0b408a04b09075';
        $req          = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("徐亚520");
        
        $code = \Org\Util\String::randNumber(100000, 999999);
        //保存到session中
        session('reg_tel_code',$code);
        $data = [
            'product'=>'啊咿呀哟',
            'code'=> $code,
        ];
        $req->setSmsParam(json_encode($data));
        $req->setRecNum($tel);
        $req->setSmsTemplateCode("SMS_11495217");
        $resp         = $c->execute($req);
        dump($resp);

    }
}
