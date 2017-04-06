<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       $this->display();
    }
    public function top(){
        $userinfo = session('USERINFO');
        $this->assign('userinfo',$userinfo);
        $this->display();
    }
    public function menu(){
        $this->display();
    }
    public function main(){
        $this->display();
    }
}