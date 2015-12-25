<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class ActivityPracticeController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allActivityPractice() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		doLog($_SESSION ['userId'],1,'view_allActivityPractice');
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','ACTIVITYPRACTICE');
		
		$activityPractice = M ( 'activitypractice' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$activityPractice->where ( 'activitypracticeid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$activityPractice->where ( 'activitypracticeid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $activityPractice->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ),'p1' );
		$page->setP ( 'p1' );
		$orderby ['activitypracticeid'] = 'desc';
		$list = $activityPractice->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		$user = M ('user');
		$listAllUser = $user->select ();
		// 将人员id转换为名称
		for($i = 0; $i < count ( $list ); $i ++) {
			for($j = 0; $j < count ( $listAllUser ); $j ++) {
				if ($list [$i] ['activitypracticereleaseid'] == $listAllUser [$j] ['userid']) {
					$list [$i] ['activitypracticereleaseid'] = $listAllUser [$j] ['usernickname'];
				}
				if ($list [$i] ['activitypracticeauditid'] == $listAllUser [$j] ['userid']) {
					$list [$i] ['activitypracticeauditid'] = $listAllUser [$j] ['usernickname'];
				}
			}
// 			$list [$i] ['activitypracticereleaseid'] = getUserNicknameByUserId($list [$i] ['activitypracticereleaseid']);
// 			$list [$i] ['activitypracticeauditid'] = getUserNicknameByUserId($list [$i] ['activitypracticeauditid']);
			
			if($list [$i] ['activitypracticeauditstatus'] == 0){
				$list [$i] ['activitypracticeauditstatus'] = '未审核';
			}else{
				$list [$i] ['activitypracticeauditstatus'] = '通过审核';
			}
			if($list [$i] ['activitypracticetype'] == 0){
				$list [$i] ['activitypracticetype'] = '社会实践';
			}else{
				$list [$i] ['activitypracticetype'] = '志愿服务';
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
	public function addActivityPractice() {
		try {
			$activityPractice = M ( 'activitypractice' );
			
			$data ['activityPracticeTitle'] = $_POST ['activityPracticeTitle'];
			$data ['activityPracticeReleaseId'] = $_SESSION ['userId'];
			$data ['activityPracticeReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['activityPracticePageView'] = 0;
			$data ['activityPracticeAuditStatus'] = 1;
			$data ['activityPracticeAuditId'] = $_SESSION['userId'];
			$data ['activityPracticeAuditDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['activityPracticeType'] = $_POST['activityPracticeType'];
// 			dump($_POST ['editor']);
// 			return ;
			// $data['workTendencyReleaseDate'] = $_POST['workTendencyReleaseDate'];
			// 创建内容的html文件
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, $_POST ['editor'] );
			fclose ( $myFile );
			$data ['activityPracticeContentURL'] = $myFilePath;
			$data ['activityPracticeInformation'] = '';
			$activityPractice->create ( $data );
			$activityPracticeId = $activityPractice->add();
// 			dump($activityPracticeId);
// 			return;
			doLog($_SESSION ['userId'],2,'add_allActivityPractice_Id_:_' . $activityPracticeId);
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allActivityPractice' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allActivityPractice' );
		}
	}
	public function editActivityPractice() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','WORKTENDENCY');
		
		$activityPractice = M ( 'activitypractice' );
		$editActivityPractice = $activityPractice->where ( 'activityPracticeId=' . $_GET ['activitypracticeid'] )->find ();
		
		doLog($_SESSION ['userId'],3,'edit_allActivityPractice_Id_:_' . $_GET ['activitypracticeid']);
		
// 		dump($editActivityPractice);
// 		return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $editActivityPractice ['activitypracticecontenturl'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		
		$this->assign ( 'activityPractice', $editActivityPractice );
		$this->display ();
	}
	public function editActivityPracticeSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign('APPLICATION_NAME',C('APPLICATION_NAME'));
		$this->assign('USER_ID',$_SESSION ['userId']);
		$this->assign('USER_LEVEL',$_SESSION ['userLevel']);
		$this->assign('CURRENT_MENU','WORKTENDENCY');
		
		$activityPractice = M ( 'activitypractice' );
		$data ['activityPracticeId'] = $_GET ['activitypracticeid'];
		$data ['activityPracticeTitle'] = $_POST ['activityPracticeTitle'];
		$data ['activityPracticeType'] = $_POST ['activityPracticeType'];
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
		$data ['activityPracticeContentURL'] = $myFilePath;
		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $activityPractice->save ( $data );
		
		doLog($_SESSION ['userId'],4,'edit_allActivityPractice_Submit_Id_:_' . $_GET ['activitypracticeid']);
		
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
