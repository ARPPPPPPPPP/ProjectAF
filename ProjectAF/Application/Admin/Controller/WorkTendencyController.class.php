<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class WorkTendencyController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allPage() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		

		doLog($_SESSION ['userId'],29,'View_All_WorkTendency');
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'WORKTENDENCY' );
		
		$workTendency = M ( 'worktendency' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$workTendency->where ( 'worktendencyid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$workTendency->where ( 'worktendencyid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $workTendency->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['worktendencyid'] = 'desc';
		$list = $workTendency->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		$user = M ( 'user' );
		$listAllUser = $user->select ();
		// 将人员id转换为名称
		for($i = 0; $i < count ( $list ); $i ++) {
			for($j = 0; $j < count ( $listAllUser ); $j ++) {
				if ($list [$i] ['worktendencyreleaseid'] == $listAllUser [$j] ['userid']) {
					$list [$i] ['worktendencyreleaseid'] = $listAllUser [$j] ['usernickname'];
				}
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
	public function addWorkTendency() {
		try {
			$workTendency = M ( 'worktendency' );
			
			$data ['workTendencyTitle'] = $_POST ['workTendencyTitle'];
			$data ['workTendencyReleaseId'] = $_SESSION ['userId'];
			$data ['workTendencyReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			// $data['workTendencyReleaseDate'] = $_POST['workTendencyReleaseDate'];
			// 创建内容的html文件
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, $_POST ['editor'] );
			fclose ( $myFile );
			$data ['workTendencyContentURL'] = $myFilePath;
			$data ['workTendencyReleaseInformation'] = '';
			$data ['workTendencyPageView'] = 0;
			
			$workTendency->create ( $data );
			$addWorkTendencyId = $workTendency->add ();

			doLog($_SESSION ['userId'],30,'Add_WorkTendency_Id_:_' . $addWorkTendencyId);
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allPage' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allPage' );
		}
	}
	public function editWorkTendency() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'WORKTENDENCY' );
		
		$workTendency = M ( 'worktendency' );
		$editWorkTendency = $workTendency->where ( 'workTendencyId=' . $_GET ['worktendencyid'] )->find ();
		
		// dump($editWorkTendency);
		// echo $editWorkTendency['worktendencyreleasedate'];
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $editWorkTendency ['worktendencycontenturl'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$editWorkTendency ['worktendencyreleaseid'] = getUserNicknameByUserId($editWorkTendency ['worktendencyreleaseid'] );
		

		doLog($_SESSION ['userId'],31,'Edit_WorkTendency_Id_:_' . $_GET ['worktendencyid']);
		$this->assign ( "editorHtml", $editorHtml );
		
		$this->assign ( 'workTendency', $editWorkTendency );
		$this->display ();
	}
	public function editWorkTendencySubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'WORKTENDENCY' );
		
		$workTendency = M ( 'worktendency' );
		$data ['workTendencyId'] = $_GET ['worktendencyid'];
		$data ['workTendencyTitle'] = $_POST ['workTendencyTitle'];
// 		$data ['workTendencyReleaseId'] = $_POST ['workTendencyReleaseId'];
// 		$data ['workTendencyReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
		
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencytitle',$_POST['workTendencyTitle']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleaseid',$_POST['workTendencyReleaseId']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleasedate',date ( 'Y-m-d H:i:s', time () ));
		// 创建内容的html文件
		$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
		$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$data ['workTendencyContentURL'] = $myFilePath;
		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $workTendency->save ( $data );
		if ($result !== false) {
			// echo U('WorkTendency/allPage');

			doLog($_SESSION ['userId'],32,'Edit_WorkTendency_Submit_Id_:_' . $_GET ['worktendencyid']);
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
