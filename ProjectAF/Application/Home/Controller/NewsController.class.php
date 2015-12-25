<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 19:04
 */
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller
{
    //breaking news
    public function news(){
        $news=M('breakingnews');
        //获取新闻
        if(isset ( $_GET ['breakingnewsid'] )){
            $n=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($n==null)$n=$news->where('breakingnewstype=1')->find();
        //内容
        $fileName = $n['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //其他链接
        $links=$news->where('breakingnewstype=1 and breakingnewsid!='.$n['breakingnewsid'])->limit(4)->select();
        $this->assign('news',$n);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
        $this->display();
    }
    //学生之声
    public function voice(){
        $news=M('breakingnews');
        //获取新闻
        if(isset ( $_GET ['breakingnewsid'] )){
            $voice=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($voice==null)$voice=$news->where('breakingnewstype=10')->find();
        //内容
        $fileName = $voice['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //其他链接
        $links=$news->where('breakingnewstype=10 and breakingnewsid!='.$voice['breakingnewsid'])->limit(4)->select();

        $this->assign('voice',$voice);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }

    //留学案例
    public function cases(){
        $news=M('breakingnews');
        //获取新闻
        if(isset ( $_GET ['breakingnewsid'] )){
            $case=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($case==null)$case=$news->where('breakingnewstype=9')->find();
        //内容
        $fileName = $case['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //其他链接
        $links=$news->where('breakingnewstype=9 and breakingnewsid!='.$case['breakingnewsid'])->limit(4)->select();
        $this->assign('case',$case);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }

    //留学资料
    public function information(){
        $news=M('breakingnews');
        //获取新闻
        if(isset ( $_GET ['breakingnewsid'] )){
            $info=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($info==null)$info=$news->where('breakingnewstype=4')->find();
        //内容
        $fileName = $info['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //其他链接
        $links=$news->where('breakingnewstype=4 and breakingnewsid!='.$info['breakingnewsid'])->limit(4)->select();
        $this->assign('info',$info);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }
}