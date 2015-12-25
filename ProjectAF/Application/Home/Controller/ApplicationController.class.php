<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 16:55
 */
namespace Home\Controller;
use Think\Controller;
class ApplicationController extends  Controller
{
    public function application(){
        $this->display();
    }
}