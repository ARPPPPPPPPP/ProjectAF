<?php
/**
 * Created by PhpStorm.
 * User: zhangguixu
 * Date: 2015/11/10
 * Time: 19:03
 */

namespace Home\Controller;
use Think\Controller;

class GuideController extends Controller
{
    public function guide(){
        $news=M('breakingnews');
        //步骤
        $step=$news->where('breakingnewstype=5')->find();
        $fileName = $step['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $stepcontent = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );
        //费用明细
        $fare=$news->where('breakingnewstype=6')->find();
        $fileName = $fare['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $farecontent = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );
        //常见问题
        $faq=$news->where('breakingnewstype=7')->find();
        $fileName = $faq['breakingnewscontenturl'];
        $myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
        $faqcontent = fread ( $myFile, filesize ( $fileName ) );
        fclose ( $myFile );

        $this->assign('step',$stepcontent);
        $this->assign('fare',$farecontent);
        $this->assign('faq',$faqcontent);
        $this->display();
    }
}