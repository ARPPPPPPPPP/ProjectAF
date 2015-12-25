<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class NoticeController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allNotice() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		doLog($_SESSION ['userId'],11,'View_allNotice');
		
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','NOTICE');
		
		$notice = M ( 'notice' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$notice->where ( 'noticeid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$notice->where ( 'noticeid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $notice->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ),'p1' );
		$page->setP ( 'p1' );
		$orderby ['noticeid'] = 'desc';
		$list = $notice->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		$user = M ('user');
		$listAllUser = $user->select ();
		// 将人员id转换为名称
		for($i = 0; $i < count ( $list ); $i ++) {
			for($j = 0; $j < count ( $listAllUser ); $j ++) {
				if ($list [$i] ['noticereleaseid'] == $listAllUser [$j] ['userid']) {
					$list [$i] ['noticereleaseid'] = $listAllUser [$j] ['usernickname'];
				}
				if ($list [$i] ['noticeauditid'] == $listAllUser [$j] ['userid']) {
					$list [$i] ['noticeauditid'] = $listAllUser [$j] ['usernickname'];
				}
			}
// 			$list [$i] ['activitypracticereleaseid'] = getUserNicknameByUserId($list [$i] ['activitypracticereleaseid']);
// 			$list [$i] ['activitypracticeauditid'] = getUserNicknameByUserId($list [$i] ['activitypracticeauditid']);
			
			if($list [$i] ['noticeauditstatus'] == 0){
				$list [$i] ['noticeauditstatus'] = '未审核';
			}else{
				$list [$i] ['noticeauditstatus'] = '通过审核';
			}
		}
		
		
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		$editor->Value = ' '; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		$this->display ();
	}
	public function addNotice() {
		try {
			$notice = M ( 'notice' );
			
			$data ['noticeTitle'] = $_POST ['noticeTitle'];
			$data ['noticeInformation'] = C('ORANGIZATION');
			$data ['noticeReleaseId'] = $_SESSION ['userId'];
			$data ['noticeReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['noticePageView'] = 0;
			$data ['noticeAuditStatus'] = 1;
			$data ['noticeAuditId'] = $_SESSION['userId'];
			$data ['noticeAuditDate'] = date ( 'Y-m-d H:i:s', time () );
// 			dump($_POST ['editor']);
// 			dump($data);
// 			return ;
			// $data['workTendencyReleaseDate'] = $_POST['workTendencyReleaseDate'];
			// 创建内容的html文件
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, $_POST ['editor'] );
			fclose ( $myFile );
			$data ['noticeContentURL'] = $myFilePath;
			$data ['noticeInformation'] = '';
			
			$notice->create ( $data );
			$addNoticeId = $notice->add ();
			
			doLog($_SESSION ['userId'],12,'Add_notice_Id_:_' . $addNoticeId);
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allNotice' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allNotice' );
		}
	}
	public function editNotice() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		
		doLog($_SESSION ['userId'],13,'Edit_notice_Id_:_' . $_GET ['noticeid']);
		
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','NOTICE');
		
		$notice = M ( 'notice' );
		$editNotice = $notice->where ( 'noticeId=' . $_GET ['noticeid'] )->find ();
		
// 		dump($editNotice);
// 		return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $editNotice ['noticecontenturl'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		
		$this->assign ( 'notice', $editNotice );
		$this->display ();
	}
	public function editNoticeSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','NOTICE');
		
		$notice = M ( 'notice' );
		$data ['noticeId'] = $_GET ['noticeid'];
		$data ['noticeTitle'] = $_POST ['noticeTitle'];
// 		dump ($data);
// 		return;
		
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencytitle',$_POST['workTendencyTitle']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleaseid',$_POST['workTendencyReleaseId']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleasedate',date ( 'Y-m-d H:i:s', time () ));
		// 创建内容的html文件
		$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
		$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$data ['noticeContentURL'] = $myFilePath;
		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $notice->save ( $data );
		
		doLog($_SESSION ['userId'],14,'Edit_notice_submit_Id_:_' . $_GET ['noticeid']);
		
		if ($result !== false) {
			// echo U('WorkTendency/allPage');
			echo '
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					</head>
					<h1 style="line-height:400px;text-align:center">成功，1秒后自动关闭</h1>
					<script language="javascript">
						function closeWindow(){
							window.opener=null;
							window.open("","_self")
							window.close();
						}
						setTimeout("closeWindow()",1000);
						
					</script>';
			// $this->success ( C ( 'EDIT_SUCCESS' ), '/WorkTendency/allPage' );
		} else {
			$this->error ( C ( 'EDIT_FAIL' ) );
		}
	}
	public function example() {
		$this->assign ( "html", $_POST ['editor'] );
		$this->display ();
	}
}
