<?php

/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 14:57
 */
namespace Home\Controller;
use Think\Controller;
class IndexController extends  Controller
{
    public function index(){
        $homePicutre = M ( 'homepicture' );
        $news=M('breakingnews');
        //����ͼ
        $orderby ['homepictureid'] = 'desc';
        $list = $homePicutre->order ( $orderby )->limit (1)->select ();

        $newsorderby['breakingnewsid']='desc';
        //ѧ��֮��
        $voiceslist=$news->order($newsorderby)->where('breakingnewstype=10')->limit(4)->select();

        //����
        $newslist=$news->order($newsorderby)->where('breakingnewstype=1')->limit(2)->select();

        $this->assign('bgpic',$list[0]);
        $this->assign('voices',$voiceslist);
        $this->assign('news',$newslist);
        $this->display();
    }

}