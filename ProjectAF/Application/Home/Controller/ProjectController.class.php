<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 16:44
 */

namespace Home\Controller;
use Think\Controller;

class ProjectController extends Controller
{
    //pfp香港项目
    public function pfp(){
        $this->display();
    }
    //pst香港项目
    public function pst(){
        $news=M('breakingnews');
        $color=array('panel-default','panel-success','panel-primary','panel-danger','panel-info');
        $list=$news->where('breakingnewstype=8')->select();
        $length=count($list);
        for($x=0;$x<$length;$x++){
            $list[$x]['color']=$color[$x % 5];
            $list[$x]['id']=$x;
            $list[$x]['collapse']='collapse'.$x;
            $list[$x]['href']='#collapse'.$x;
        }
        $this->assign('list',$list);
        $this->display();
    }
}