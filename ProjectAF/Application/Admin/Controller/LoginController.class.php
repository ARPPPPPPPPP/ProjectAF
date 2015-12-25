<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
	public function Login() {
		$usertype = $_GET ['usertype'];
		$this->assign ( 'usertype', $usertype );
		$this->display ();
	}
	public function loginSubmit() {
		$usertype = $_GET ['usertype'];
		$password = $_POST ['password'];
		$userM = M ( "user" );
		$tempUser = $userM->where ( 'usertype=' . '"' . $usertype . '"' )->limit ( 1 )->select ();
		if ($tempUser [0] ['userpassword'] == '') {
			$tempUser [0] ['userpassword'] = $password;
			$saveTmp ['userId'] = $tempUser [0]['userid'];
			$saveTmp ['userPassword'] = $password;
			$result = $userM->save ( $saveTmp );
			session ( 'userId', $tempUser [0] ['userid'] );
			session ( 'userType', $tempUser [0] ['usertype'] );
			if('A' == $usertype){
				$this->success ( 'Set Password Succeed', U ( 'Event/AEventList' ) );
			}else{
				$this->success ( 'Set Password Succeed', U ( 'Event/FEventList' ) );
			}
			return;
		}
		
		if (strcmp ( $password, $tempUser [0] ['userpassword'] ) == 0) {
			// 登录成功
// 			session ( 'userAccount', $tempUser [0] ['useraccount'] );
			session ( 'userId', $tempUser [0] ['userid'] );
			session ( 'userType', $tempUser [0] ['usertype'] );
			if('A' == $usertype){
				$this->success ( C ( 'LOGIN_SUCCESS' ), U ( 'Event/AEventList' ) );
			}else{
				$this->success ( C ( 'LOGIN_SUCCESS' ), U ( 'Event/FEventList' ) );
			}
			
		} else {
			// 登录失败
			// echo "false";
			$this->error ( C ( 'LOGIN_FAIL' ) );
		}
	}
}
