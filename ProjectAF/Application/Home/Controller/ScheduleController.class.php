<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 19:08
 */

namespace Home\Controller;
use Think\Controller;
class ScheduleController extends Controller
{
    public function schedule(){
        $setting = M ( 'systemsetting' );
        $download=M('download');
        $link=$setting->where('systemsettingname=3')->select();
//        dump($link);
//        return;
        $pfp=$download->where('downloadtitle="pfp"')->select();
        $pst=$download->where('downloadtitle="pst"')->select();
//        dump($pfp);
//        return;
        $this->assign('link',$link[0]);
        $this->assign('pfp',$pfp[0]);
        $this->assign('pst',$pst[0]);
        $this->display();
    }
}