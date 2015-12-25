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
        //留学案例
        $cases=$news->where('breakingnewstype=9')->order($orderby)->limit(3)->select();

        //留学考试资料
        $exams=$news->where('breakingnewstype=2')->order($orderby)->select();
        $lenOfExams=count($exams);
        $tmp=ceil($lenOfExams/2);
        for($x=0;$x<$tmp;$x++){
            $exam1[$x]=$exams[$x];
        }
        for($x=0;$x<$lenOfExams-$tmp;$x++){
            $exam2[$x]=$exams[$x+$tmp-1];
        }

        //世界排名
        $rank=$news->where('breakingnewstype=3')->order($orderby)->select();
        $lenOfRank=count($rank);
        $tmp=ceil($lenOfRank/2);
        for($x=0;$x<$tmp;$x++){
            $rank1[$x]=$rank[$x];
        }
        for($x=0;$x<$lenOfRank-$tmp;$x++){
            $rank2[$x]=$rank[$x+$tmp-1];
        }

        //留学区域
        $type=mb_convert_encoding("美国", "UTF-8","EUC-CN");
        //American
        $American=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Canada
        $type=mb_convert_encoding("加拿大", "UTF-8","EUC-CN");
        $Canada=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Australia
        $type=mb_convert_encoding("澳大利亚", "UTF-8","EUC-CN");
        $Australia=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //England
        $type=mb_convert_encoding("英国", "UTF-8","EUC-CN");
        $England=$news->where('breakingnewstype=4 and breakingnewsmaincontent="'.$type.'"')->order($orderby)->select();
        //Hongkong
        $type=mb_convert_encoding("香港", "UTF-8","EUC-CN");
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