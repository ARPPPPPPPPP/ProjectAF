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
        //��ȡ����
        if(isset ( $_GET ['breakingnewsid'] )){
            $n=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($n==null)$n=$news->where('breakingnewstype=1')->find();
        //����
        $fileName = $n['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //��������
        $links=$news->where('breakingnewstype=1 and breakingnewsid!='.$n['breakingnewsid'])->limit(4)->select();
        $this->assign('news',$n);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
        $this->display();
    }
    //ѧ��֮��
    public function voice(){
        $news=M('breakingnews');
        //��ȡ����
        if(isset ( $_GET ['breakingnewsid'] )){
            $voice=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($voice==null)$voice=$news->where('breakingnewstype=10')->find();
        //����
        $fileName = $voice['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //��������
        $links=$news->where('breakingnewstype=10 and breakingnewsid!='.$voice['breakingnewsid'])->limit(4)->select();

        $this->assign('voice',$voice);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }

    //��ѧ����
    public function cases(){
        $news=M('breakingnews');
        //��ȡ����
        if(isset ( $_GET ['breakingnewsid'] )){
            $case=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($case==null)$case=$news->where('breakingnewstype=9')->find();
        //����
        $fileName = $case['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //��������
        $links=$news->where('breakingnewstype=9 and breakingnewsid!='.$case['breakingnewsid'])->limit(4)->select();
        $this->assign('case',$case);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }

    //��ѧ����
    public function information(){
        $news=M('breakingnews');
        //��ȡ����
        if(isset ( $_GET ['breakingnewsid'] )){
            $info=$news->where('breakingnewsid='.$_GET['breakingnewsid'])->find();
        }
        if($info==null)$info=$news->where('breakingnewstype=4')->find();
        //����
        $fileName = $info['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $content = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        //��������
        $links=$news->where('breakingnewstype=4 and breakingnewsid!='.$info['breakingnewsid'])->limit(4)->select();
        $this->assign('info',$info);
        $this->assign('content',$content);
        $this->assign('links',$links);
        $this->display();
    }
}