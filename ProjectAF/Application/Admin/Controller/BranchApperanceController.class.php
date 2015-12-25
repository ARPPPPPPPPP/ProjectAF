<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class BranchApperanceController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allBranchApperance() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		doLog ( $_SESSION ['userId'], 33, 'View_allBranchApperance' );
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'BRANCHAPPERANCE' );
		
		$branchApperance = M ( 'branchapperance' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$branchApperance->where ( 'branchapperanceid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$branchApperance->where ( 'branchapperanceid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $branchApperance->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['branchapperanceid'] = 'desc';
		$list = $branchApperance->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		// dump($list);
		// return;
		
		$user = M ( 'user' );
		$listAllUser = $user->select ();
		// 将人员id转换为名称
		for($i = 0; $i < count ( $list ); $i ++) {
			$list [$i] ['branchapperancereleaseid'] = getUserNicknameByUserId ( $list [$i] ['branchapperancereleaseid'] );
			$list [$i] ['branchapperanceacademyauditid'] = getUserNicknameByUserId ( $list [$i] ['branchapperanceacademyauditid'] );
			$list [$i] ['branchapperanceorangizationauditid'] = getUserNicknameByUserId ( $list [$i] ['branchapperanceorangizationauditid'] );
			// $list [$i] ['activitypracticereleaseid'] = getUserNicknameByUserId($list [$i] ['activitypracticereleaseid']);
			// $list [$i] ['activitypracticeauditid'] = getUserNicknameByUserId($list [$i] ['activitypracticeauditid']);
			
			if ($list [$i] ['branchapperanceacademyauditstatus'] == 0) {
				$list [$i] ['branchapperanceacademyauditstatus'] = '未审核';
			} else {
				$list [$i] ['branchapperanceacademyauditstatus'] = '通过审核';
			}
			if ($list [$i] ['branchapperanceorangizationauditstatus'] == 0) {
				$list [$i] ['branchapperanceorangizationauditstatus'] = '未审核';
			} else {
				$list [$i] ['branchapperanceorangizationauditstatus'] = '通过审核';
			}
		}
		$academy = M ( 'academy' );
		$listAcademy = $academy->select ();
		$branch = M ( 'branch' );
		$listBranch = $branch->select ();
		$this->assign ( 'listAcademy', $listAcademy ); // 赋值数据集
		$this->assign ( 'listBranch', $listBranch ); // 赋值数据集
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		$editor->Value = ' '; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
// 		dump($list);
// 		return;
		
		$this->assign ( "editorHtml", $editorHtml );
		$this->display ();
	}
	public function addBranchApperance() {
		try {
			$branchapperance = M ( 'branchapperance' );
			
			$data ['branchApperanceTitle'] = $_POST ['branchapperanceTitle'];
			$data ['branchApperanceBranch'] = $_POST ['branchapperancebranch'];
			$data ['branchApperanceAcademy'] = $_POST ['branchapperanceacademy'];
			$data ['noticeInformation'] = C ( 'ORANGIZATION' );
			$data ['branchApperanceReleaseId'] = $_SESSION ['userId'];
			$data ['branchApperanceReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['branchApperancePageView'] = 0;
			$data ['branchApperanceAcademyAuditStatus'] = 1;
			$data ['branchApperanceAcademyAuditId'] = $_SESSION ['userId'];
			$data ['branchApperanceAcademyAuditDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['branchApperanceOrangizationAuditStatus'] = 1;
			$data ['branchApperanceOrangizationAuditId'] = $_SESSION ['userId'];
			$data ['branchApperanceOrangizationAuditDate'] = date ( 'Y-m-d H:i:s', time () );
			// dump($_POST ['editor']);
			// dump($data);
			// return ;
			// $data['workTendencyReleaseDate'] = $_POST['workTendencyReleaseDate'];
			// 创建内容的html文件
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, $_POST ['editor'] );
			fclose ( $myFile );
			$data ['branchApperanceContentURL'] = $myFilePath;
			$data ['branchApperanceInformation'] = '';
			
			$branchapperance->create ( $data );
			$addBranchApperanceId = $branchapperance->add ();
			
			doLog ( $_SESSION ['userId'], 34, 'Add_BranchApperance_Id_:_' . $addBranchApperanceId );
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allBranchApperance' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allBranchApperance' );
		}
	}
	public function editBranchApperance() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		
		doLog ( $_SESSION ['userId'], 35, 'Edit_BranchApperance_Id_:_' . $_GET ['branchapperanceid'] );
		
// 		dump($_GET ['branchapperanceid']);
// 		return;
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'BRANCHAPPERANCE' );
		
		$branchapperance = M ( 'branchapperance' );
		$editBranchApperance = $branchapperance->where ( 'branchApperanceId=' . $_GET ['branchapperanceid'] )->find ();
		
// 		dump($editBranchApperance);
// 		return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $editBranchApperance ['branchapperancecontenturl'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$user = M ( 'user' );
		$listUser = $user->select ();
		$academy = M ( 'academy' );
		$listAcademy = $academy->select ();
		$branch = M ( 'branch' );
		$listBranch = $branch->select ();
		$this->assign ( 'listUser', $listUser ); // 赋值数据集
		$this->assign ( 'listAcademy', $listAcademy ); // 赋值数据集
		$this->assign ( 'listBranch', $listBranch ); // 赋值数据集
		$this->assign ( "editorHtml", $editorHtml );
		
		$this->assign ( 'branchApperance', $editBranchApperance );
		$this->display ();
	}
	public function editBranchApperanceSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'BRANCHAPPERANCE' );
		
		$branchapperance = M ( 'branchapperance' );
		$data ['branchApperanceId'] = $_GET ['branchapperanceid'];
		$data ['branchApperanceTitle'] = $_POST ['branchapperanceTitle'];
		$data ['branchApperanceReleaseId'] = $_POST ['branchapperancereleaseid'];
		$data ['branchApperanceAcademy'] = $_POST ['branchapperanceacademy'];
		$data ['branchApperanceBranch'] = $_POST ['branchapperancebranch'];
// 		dump ( $_POST ['branchapperancebranch']);
// 		return;
		
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencytitle',$_POST['workTendencyTitle']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleaseid',$_POST['workTendencyReleaseId']);
		// $workTendency-> where('worktendencyid=' . $_GET['worktendencyid'])->setField('worktendencyreleasedate',date ( 'Y-m-d H:i:s', time () ));
		// 创建内容的html文件
		$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
		$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$data ['branchApperanceContentURL'] = $myFilePath;
		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $branchapperance->save ( $data );
		
		doLog ( $_SESSION ['userId'], 36, 'Edit_BranchApperance_submit_Id_:_' . $_GET ['branchapperanceid'] );
		
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
