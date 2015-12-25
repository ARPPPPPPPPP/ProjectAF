<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class SystemSettingController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function systemSetting() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SYSTEM' );
		
		$systemSetting = M ( 'systemsetting' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$systemSetting->where ( 'systemsettingid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$systemSetting->where ( 'systemsettingid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		$count = $systemSetting->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['systemsettingid'] = 'desc';
		$list = $systemSetting->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		for($i = 0; $i < count ( $list ); $i ++) {
			switch($list [$i] ['systemsettingname']){
				case 1:
					$list [$i] ['systemsettingname'] = "遊學優勢標題";
					break;
				case 2:
					$list [$i] ['systemsettingname'] = "遊學優勢內容";
					break;
				case 3:
					$list [$i] ['systemsettingname'] = "酒店鏈接";
					break;
				case 4:
					$list [$i] ['systemsettingname'] = "聯繫方式電話";
					break;
				case 5:
					$list [$i] ['systemsettingname'] = "聯繫方式郵箱";
					break;
				default:
					$list [$i] ['systemsettingname'] = "NULL";
					break;
			}
		}
		
		// dump($list);
		// return;
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		$this->display ();
	}
	public function addSystemSetting() {
		try {
			$systemSetting = M ( 'systemsetting' );
			
			$data ['systemSettingName'] = $_POST ['systemsettingname'];
			$data ['systemSettingContent'] = $_POST ['systemsettingcontent'];
			
			//TODO 检查当前Name是否存在
			$list = $systemSetting->select();
// 			dump($list);
			for($i = 0; $i < count ( $list ); $i ++) {
				if ($list [$i] ['systemsettingname'] == $_POST ['systemsettingname']) {
					$this->error ( 'Adding SystemSetting ' . ' Already Exist' , 'systemSetting' );
					return;
				} 
			}
			
			
			$systemSetting->create ( $data );
			$systemSettingId = $systemSetting->add ();
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'systemSetting' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'systemSetting' );
		}
	}
	public function editSystemSetting() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SIGNUP' );
		
		$systemSetting = M ( 'systemsetting' );
		$editSystemSetting = $systemSetting->where ( 'systemSettingId=' . $_GET ['systemsettingid'] )->find ();
// 		dump($editSignUp);
// 		return;
		$this->assign ( 'systemSetting', $editSystemSetting );
		$this->display ();
	}
	public function editSystemSettingSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SYSTEM' );
		
		$systemSetting = M ( 'systemsetting' );
		$data ['systemSettingId'] = $_GET ['systemsettingid'];
		$data ['systemSettingContent'] = $_POST ['systemsettingcontent'];
		
		
// 		dump($data);
// 		return;
		
		$result = $systemSetting->save ( $data );
		
		// doLog ( $_SESSION ['userId'], 36, 'Edit_BranchApperance_submit_Id_:_' . $_GET ['branchapperanceid'] );
		
		if ($result !== false) {
			// echo U('WorkTendency/allPage');
			echo '
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					</head>
					<h1 style="line-height:400px;text-align:center">Success，auto closed after 1s</h1>
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
}
