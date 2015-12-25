<?php

namespace Home\Controller;
use Think\Controller;

class SignUpController extends Controller {

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
			$this->success ( 'success', '../Index/index' );
		} catch ( Exception $e ) {
			$this->error ( 'fail', '../Application/application' );
		}
	}

}
