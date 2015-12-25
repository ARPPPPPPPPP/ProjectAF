<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class SignUpController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allSignUp() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		doLog ( $_SESSION ['userId'], 33, 'View_allBranchApperance' );
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SIGNUP' );
		
		$signUp = M ( 'signup' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$signUp->where ( 'signupid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$signUp->where ( 'signupid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $signUp->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['signupid'] = 'desc';
		$list = $signUp->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		// dump($list);
		// return;
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		$this->display ();
	}
	public function addSignUp() {
		try {
			$signUp = M ( 'signup' );
			
			$data ['signUpProject'] = $_POST ['signupproject'];
			$data ['signUpArea'] = $_POST ['signuparea'];
			$data ['signUpName'] = $_POST ['signupname'];
			$data ['signUpSex'] = $_POST ['signupsex'];
			$data ['signUpBirthday'] = $_POST ['signupbirthday'];
			$data ['signUpSchool'] = $_POST ['signupschool'];
			$data ['signUpMajor'] = $_POST ['signupmajor'];
			$data ['signUpGradeYear'] = $_POST ['signupgradeyear'];
			$data ['signUpCellPhone'] = $_POST ['signupcellphone'];
			$data ['signUpMail'] = $_POST ['signupmail'];
			$data ['signUpWeiXing'] = $_POST ['signupweixing'];
			$data ['signUpReferee'] = $_POST ['signupreferee'];
			$data ['signUpIDCard'] = $_POST ['signupidcard'];
			$data ['signUpFamilyContact'] = $_POST ['signupfamilycontact'];
			$data ['signUpAward'] = $_POST ['signupaward'];
			$data ['signUpExperience'] = $_POST ['signupexperience'];
			$data ['signUpTime'] = date ( 'Y-m-d H:i:s', time () );
			$data ['signUpHandle'] = 0;
			
			$signUp->create ( $data );
			$signUpId = $signUp->add ();
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allSignUp' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allSignUp' );
		}
	}
	public function editSignUp() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SIGNUP' );
		
		$signUp = M ( 'signup' );
		$editSignUp = $signUp->where ( 'signUpId=' . $_GET ['signupid'] )->find ();
// 		dump($editSignUp);
// 		return;
		$this->assign ( 'signUp', $editSignUp );
		$this->display ();
	}
	public function editSignUpSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'SIGNUP' );
		
		$signUp = M ( 'signup' );
		$data ['signUpId'] = $_GET ['signupid'];
		$data ['signUpProject'] = $_POST ['signupproject'];
		$data ['signUpArea'] = $_POST ['signuparea'];
		$data ['signUpName'] = $_POST ['signupname'];
		$data ['signUpSex'] = $_POST ['signupsex'];
		$data ['signUpBirthday'] = $_POST ['signupbirthday'];
		$data ['signUpSchool'] = $_POST ['signupschool'];
		$data ['signUpMajor'] = $_POST ['signupmajor'];
		$data ['signUpGradeYear'] = $_POST ['signupgradeyear'];
		$data ['signUpCellPhone'] = $_POST ['signupcellphone'];
		$data ['signUpMail'] = $_POST ['signupmail'];
		$data ['signUpWeiXing'] = $_POST ['signupweixing'];
		$data ['signUpReferee'] = $_POST ['signupreferee'];
		$data ['signUpIDCard'] = $_POST ['signupidcard'];
		$data ['signUpFamilyContact'] = $_POST ['signupfamilycontact'];
		$data ['signUpAward'] = $_POST ['signupaward'];
		$data ['signUpExperience'] = $_POST ['signupexperience'];
		
// 		dump($data);
// 		return;
		
		$result = $signUp->save ( $data );
		
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
