<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 19:10
 */

namespace Home\Controller;
use Think\Controller;
class StudyabroadController extends Controller
{
    public function studyabroad(){
        $news=M('breakingnews');
        $orderby['breakingnewsid']='desc';
        //��ѧ����
        $cases=$news->where('breakingnewstype=9')->order($orderby)->limit(3)->select();

        //��ѧ��������
        $exams=$news->where('breakingnewstype=2')->order($orderby)->select();
        $lenOfExams=count($exams);
        $tmp=ceil($lenOfExams/2);
        for($x=0;$x<$tmp;$x++){
            $exam1[$x]=$exams[$x];
        }
        for($x=0;$x<$lenOfExams-$tmp;$x++){
            $exam2[$x]=$exams[$x+$tmp-1];
        }

        //��������
        $rank=$news->where('breakingnewstype=3')->order($orderby)->select();
        $lenOfRank=count($rank);
        $tmp=ceil($lenOfRank/2);
        for($x=0;$x<$tmp;$x++){
            $rank1[$x]=$rank[$x];
        }
        for($x=0;$x<$lenOfRank-$tmp;$x++){
            $rank2[$x]=$rank[$x+$tmp-1];
        }

        //��ѧ����
        $type=mb_convert_encoding("����", "UTF-8","EUC-CN");
        //American
        $American=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Canada
        $type=mb_convert_encoding("���ô�", "UTF-8","EUC-CN");
        $Canada=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Australia
        $type=mb_convert_encoding("�Ĵ�����", "UTF-8","EUC-CN");
        $Australia=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //England
        $type=mb_convert_encoding("Ӣ��", "UTF-8","EUC-CN");
        $England=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Hongkong
        $type=mb_convert_encoding("���", "UTF-8","EUC-CN");
        $Hongkong=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();

        $this->assign('cases',$cases);
        $this->assign('exam1',$exam1);
        $this->assign('exam2',$exam2);
        $this->assign('rank1',$rank1);
        $this->assign('rank2',$rank2);
        $this->assign('American',$American);
        $this->assign('Canada',$Canada);
        $this->assign('Australia',$Australia);
        $this->assign('England',$England);
        $this->assign('Hongkong',$Hongkong);
        $this->display();
    }
}