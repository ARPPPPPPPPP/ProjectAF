<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class EventController extends Controller {
	public function AEventList() {
		if (! isset ( $_SESSION ['userType'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$eventList = M ( 'event' );
		
		// 查询当前所有并且分页
		$count = $eventList->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['eventid'] = 'desc';
		
		$selectedType = $_GET ['selectedType'];
		$this->assign ( 'usertype', $_SESSION ['userType'] );
		$this->display ();
	}
	public function FEventList() {
		if (! isset ( $_SESSION ['userType'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		// dump($_SESSION ['userType'] );return;
		$eventList = M ( 'event' );
		
		// 查询当前所有并且分页
		$count = $eventList->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['eventid'] = 'desc';
		
		$this->assign ( 'usertype', $_SESSION ['userType'] );
		$this->display ();
	}
	public function AddEventHtml() {
		// dump(date("Y年m月d日"));
		$timeNow = date ( "Y-m-d" );
		$this->assign ( "time", $timeNow );
		$this->assign ( "usertype", $_GET ['usertype'] );
		$this->display ();
	}
	public function GetAEventListHtml() {
		$event = M ( 'event' );
		// 查询当前所有的工作状态并且分页
		$count = $event->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['eventid'] = 'desc';
		
		$list = $event->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
			// 进行返回前的处理
		for($i = 0; $i < count ( $list ); $i ++) {
			$list [$i] ['overtime'] = split ( ' ', $list [$i] ['overtime'] )[0];
			$timeNow = date ( "Y-m-d h:i:s" );
			$overtime = $list [$i] ['overtime'];
			if ($timeNow < $overtime) {
				$list [$i] ['overtimestatus'] = 0;
			} else {
				$list [$i] ['overtimestatus'] = 1;
			}
		}
		
// 		dump ( $list );
// 		return;
		
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		
		$this->display ();
	}
	public function GetFEventListHtml() {
		$event = M ( 'event' );
		// 查询当前所有的工作状态并且分页
		$count = $event->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['eventid'] = 'desc';
		
		$list = $event->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		// 进行返回前的处理
		for($i = 0; $i < count ( $list ); $i ++) {
			$list [$i] ['overtime'] = split ( ' ', $list [$i] ['overtime'] )[0];
			$timeNow = date ( "Y-m-d h:i:s" );
			$overtime = $list [$i] ['overtime'];
			if ($timeNow < $overtime) {
				$list [$i] ['overtimestatus'] = 0;
			} else {
				$list [$i] ['overtimestatus'] = 1;
			}
		}
		
// 		dump ( $list );
// 		return;
		
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		
		$this->display ();
	}
	public function AAddEventSubmit() {
		// dump($_GET['usertype'] . $_POST ['eventOvertime']);
		// return;
		try {
			$event = M ( 'event' );
			
			$data ['eventTitle'] = $_POST ['eventTitle'];
			$data ['eventOwner'] = $_SESSION ['userId'];
			$data ['releaseTime'] = date ( 'Y-m-d H:i:s', time () );
			if ('' == $_POST ['eventOvertime']) {
				if ($_GET ['usertype'] == 'A') {
					$this->error ( 'Expired At Can\'t Be Null', U ( 'Event/AEventList' ) );
				} else if ($_GET ['usertype'] == 'F') {
					$this->error ( 'Expired At Can\'t Be Null', U ( 'Event/FEventList' ) );
				} else {
					$this->error ( 'Expired At Can\'t Be Null', U ( 'Event/AEventList' ) );
				}
			}
			$data ['overTime'] = $_POST ['eventOvertime'] . ' 00:00:00';
			$data ['expired'] = 0;
			if ($_GET ['usertype'] == 'A') {
				$data ['eventStatus'] = 'A';
			} else if ($_GET ['usertype'] == 'F') {
				$data ['eventStatus'] = 'F';
			} else {
				$data ['eventStatus'] = ' ';
			}
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, '' );
			fclose ( $myFile );
			$data ['contentPathA'] = $myFilePath;
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, '' );
			fclose ( $myFile );
			$data ['contentPathB'] = $myFilePath;
			
			$event->create ( $data );
			$eventId = $event->add ();
			if ($_GET ['usertype'] == 'A') {
				$this->success ( C ( 'RELEASE_SUCCESS' ), U ( 'Event/AEventList' ) );
			} else if ($_GET ['usertype'] == 'F') {
				$this->success ( C ( 'RELEASE_SUCCESS' ), U ( 'Event/FEventList' ) );
			} else {
				$this->success ( C ( 'RELEASE_SUCCESS' ), U ( 'Event/AEventList' ) );
			}
			// $this->success ( C ( 'RELEASE_SUCCESS' ), 'AEventList' );
		} catch ( Exception $e ) {
			if ($_GET ['usertype'] == 'A') {
				$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), U ( 'Event/AEventList' ) );
			} else if ($_GET ['usertype'] == 'F') {
				$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), U ( 'Event/FEventList' ) );
			} else {
				$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), U ( 'Event/AEventList' ) );
			}
			// $this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'AEventList' );
		}
	}
	public function EventDetail() {
		$event = M ( 'event' )->where ( 'eventid=' . $_GET ['eventid'] )->select ();
		// dump($event);
		// return;
		$fileName = $event [0] ['contentpatha'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$contentA = $content; // 设置默认值
		$fileName = $event [0] ['contentpathb'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$contentB = $content; // 设置默认值
		                      
		// dump($contentA);
		                      // dump($contentB);
		                      // return ;
		
		$this->assign ( "contentA", $contentA );
		$this->assign ( "contentB", $contentB );
		$this->assign ( 'event', $event [0] ); // 赋值数据集
		$this->display ();
	}
	public function EventEditA() {
		$event = M ( 'event' )->where ( 'eventid=' . $_GET ['eventid'] )->select ();
		// dump($event);
		// return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $event [0] ['contentpatha'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		$this->assign ( 'event', $event [0] ); // 赋值数据集
		$this->display ();
	}
	public function EventEditB() {
		$event = M ( 'event' )->where ( 'eventid=' . $_GET ['eventid'] )->select ();
		// dump($event);
		// return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $event [0] ['contentpathb'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		$this->assign ( 'event', $event [0] ); // 赋值数据集
		$this->display ();
	}
	public function EventEditASubmit() {
		$eventM = M ( 'event' );
		$event = $eventM->where ( 'eventid=' . $_GET ['eventid'] )->select ();
		
		$fileName = $event [0] ['contentpatha'];
		
		$myFile = fopen ( $fileName, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$eventStatusTmp = $event [0] ['eventstatus'];
		switch ($event [0] ['eventstatus']) {
			case '' :
			case ' ' :
			case 'A' :
				$event [0] ['eventstatus'] = 'A';
				break;
			case 'F' :
				$event [0] ['eventstatus'] = 'AF';
				break;
		}
		
		if ($eventStatusTmp == $event [0] ['eventstatus']) {
			$this->success ( 'Edit Succeed!', U ( 'Event/AEventList' ) );
			return;
		}
		$saveData ['eventId'] = $event [0] ['eventid'];
		$saveData ['eventOwner'] = $event [0] ['eventowner'];
		$saveData ['eventTitle'] = $event [0] ['eventtitle'];
		$saveData ['contentPathA'] = $event [0] ['contentpatha'];
		$saveData ['releaseTime'] = $event [0] ['releasetime'];
		$saveData ['overTime'] = $event [0] ['overtime'];
		$saveData ['expired'] = $event [0] ['expired'];
		$saveData ['eventStatus'] = $event [0] ['eventstatus'];
		$saveData ['contentPathB'] = $event [0] ['contentpathb'];
		$result = $eventM->save ( $saveData );
		
		if ($result !== false) {
			$this->success ( 'Edit Succeed!', U ( 'Event/AEventList' ) );
		} else {
			$this->error ( 'Edit Failed!' );
		}
	}
	public function EventEditBSubmit() {
		$eventM = M ( 'event' );
		$event = $eventM->where ( 'eventid=' . $_GET ['eventid'] )->select ();
		
		$fileName = $event [0] ['contentpathb'];
		
		$myFile = fopen ( $fileName, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$eventStatusTmp = $event [0] ['eventstatus'];
		switch ($event [0] ['eventstatus']) {
			case '' :
			case ' ' :
			case 'F' :
				$event [0] ['eventstatus'] = 'F';
				break;
			case 'A' :
				$event [0] ['eventstatus'] = 'AF';
				break;
		}
		
		if ($eventStatusTmp == $event [0] ['eventstatus']) {
			$this->success ( 'Edit Succeed!', U ( 'Event/FEventList' ) );
			return;
		}
		$saveData ['eventId'] = $event [0] ['eventid'];
		$saveData ['eventOwner'] = $event [0] ['eventowner'];
		$saveData ['eventTitle'] = $event [0] ['eventtitle'];
		$saveData ['contentPathA'] = $event [0] ['contentpatha'];
		$saveData ['releaseTime'] = $event [0] ['releasetime'];
		$saveData ['overTime'] = $event [0] ['overtime'];
		$saveData ['expired'] = $event [0] ['expired'];
		$saveData ['eventStatus'] = $event [0] ['eventstatus'];
		$saveData ['contentPathB'] = $event [0] ['contentpathb'];
		$result = $eventM->save ( $saveData );
		
		if ($result !== false) {
			$this->success ( 'Edit Succeed!', U ( 'Event/FEventList' ) );
		} else {
			$this->error ( 'Edit Failed!' );
		}
	}
	public function allBreakingNews() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'BREAKINGNEWS' );
		
		$breakingNews = M ( 'breakingnews' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$breakingNews->where ( 'breakingnewsid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$breakingNews->where ( 'breakingnewsid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $breakingNews->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['breakingnewsid'] = 'desc';
		
		$selectedType = $_GET ['selectedType'];
		// $selectedType = "1";
		// dump ( $selectedType == null);
		// dump ("all" != $selectedType && $selectedType != null);
		// return;
		
		if ("all" != $selectedType && null != $selectedType) {
			$list = $breakingNews->where ( 'breakingnewstype=' . $selectedType )->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		} else {
			$selectedType = "all";
			$list = $breakingNews->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
			// dump ( $selectedType );
			// return;
		}
		
		// $list = $breakingNews->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		
		for($i = 0; $i < count ( $list ); $i ++) {
			switch ($list [$i] ['breakingnewstype']) {
				case 1 :
					$list [$i] ['breakingnewstype'] = "新聞";
					break;
				case 2 :
					$list [$i] ['breakingnewstype'] = "留學考試區";
					break;
				case 3 :
					$list [$i] ['breakingnewstype'] = "世界排名一覽";
					break;
				case 4 :
					$list [$i] ['breakingnewstype'] = "留學區域資料";
					break;
				case 5 :
					$list [$i] ['breakingnewstype'] = "申請步驟";
					break;
				case 6 :
					$list [$i] ['breakingnewstype'] = "項目費用明細";
					break;
				case 7 :
					$list [$i] ['breakingnewstype'] = "常見問題";
					break;
				case 8 :
					$list [$i] ['breakingnewstype'] = "遊學優勢";
					break;
				case 9 :
					$list [$i] ['breakingnewstype'] = "留學案例";
					break;
				case 10 :
					$list [$i] ['breakingnewstype'] = "學生之聲";
					break;
				default :
					$list [$i] ['breakingnewstype'] = "NULL";
					break;
			}
		}
		
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		
		$this->assign ( 'selectedtype', $selectedType ); // 赋值分页输出
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		$editor->Value = ' '; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		$this->display ();
	}
	public function addBreakingNews() {
		try {
			$breakingNews = M ( 'breakingnews' );
			
			$list = $breakingNews->select ();
			
			for($i = 0; $i < count ( $list ); $i ++) {
				if ((($_POST ['breakingNewsType'] == 5 || $_POST ['breakingNewsType'] == 6 || $_POST ['breakingNewsType'] == 7) && ($list [$i] ['breakingnewstype'] == $_POST ['breakingNewsType']))) {
					$this->error ( 'Adding Content ' . ' Already Exist' );
					return;
				}
			}
			
			$data ['breakingNewsName'] = $_POST ['breakingNewsName'];
			// $data ['breakingNewsRelease'] = $_SESSION ['userId'];
			$data ['breakingNewsRelease'] = $_POST ['breakingNewsRelease'];
			$data ['breakingNewsReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['breakingNewsPageView'] = 0;
			$data ['breakingNewsType'] = $_POST ['breakingNewsType'];
			$data ['breakingNewsMainContent'] = $_POST ['breakingNewsMainContent'];
			// dump($_POST ['editor']);
			// return ;
			// $data['workTendencyReleaseDate'] = $_POST['workTendencyReleaseDate'];
			// 创建内容的html文件
			$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
			$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
			fwrite ( $myFile, $_POST ['editor'] );
			fclose ( $myFile );
			$data ['breakingNewsContentURL'] = $myFilePath;
			
			// 文件上传
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = C ( 'APPLICATION_DOWNLOAD_PATH' );
			$info = $upload->upload ();
			if ($data ['breakingNewsType'] == '10') {
				if (! $info) { // 上传错误提示错误信息
					$this->error ( 'File Type Error! ' . $upload->getError () );
				} else { // 上传成功 获取上传文件信息
					foreach ( $info as $file ) {
						$data ['breakingNewsURL'] = $file ['savename'];
					}
				}
			}
			
			$breakingNews->create ( $data );
			$breakingNewsId = $breakingNews->add ();
			// dump($activityPracticeId);
			// return;
			// doLog($_SESSION ['userId'],2,'add_allActivityPractice_Id_:_' . $activityPracticeId);
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allBreakingNews' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allBreakingNews' );
		}
	}
	public function editBreakingNews() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'BREAKINGNEWS' );
		
		$breakingNews = M ( 'breakingnews' );
		$editBreakingNews = $breakingNews->where ( 'breakingNewsId=' . $_GET ['breakingnewsid'] )->find ();
		
		// doLog($_SESSION ['userId'],3,'edit_allActivityPractice_Id_:_' . $_GET ['activitypracticeid']);
		
		// dump($editActivityPractice);
		// return;
		
		$editor = new \FCKeditor\FCKeditor ( 'editor' );
		// 从contenturl中读取信息
		$fileName = $editBreakingNews ['breakingnewscontenturl'];
		$myFile = fopen ( $fileName, "r" ) or die ( "Unable to open file!" );
		$content = fread ( $myFile, filesize ( $fileName ) );
		fclose ( $myFile );
		$editor->Value = $content; // 设置默认值
		$editorHtml = $editor->Createhtml (); // 创建。注意：若用到模板（如smarty）则$fck = $oFCKeditor->CreateHtml();然后把$fck抛给模板
		
		$this->assign ( "editorHtml", $editorHtml );
		
		$this->assign ( 'breakingNews', $editBreakingNews );
		$this->display ();
	}
	public function editBreakingNewsSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'BREAKINGNEWS' );
		
		$breakingNews = M ( 'breakingnews' );
		
		$list = $breakingNews->select ();
		
		for($i = 0; $i < count ( $list ); $i ++) {
			if ((($_POST ['breakingNewsType'] == 5 || $_POST ['breakingNewsType'] == 6 || $_POST ['breakingNewsType'] == 7) && ($list [$i] ['breakingnewstype'] == $_POST ['breakingNewsType']))) {
				$this->error ( 'Adding Content ' . ' Already Exist' );
				return;
			}
		}
		
		$data ['breakingNewsId'] = $_GET ['breakingnewsid'];
		$data ['breakingNewsName'] = $_POST ['breakingNewsName'];
		$data ['breakingNewsType'] = $_POST ['breakingNewsType'];
		$data ['breakingNewsMainContent'] = $_POST ['breakingNewsMainContent'];
		
		$data ['breakingNewsRelease'] = $_POST ['breakingNewsRelease'];
		
		$myFilePath = C ( 'APPLICATION_CONTENTHTML_PATH' ) . '/' . time () . rand () . '.html';
		$myFile = fopen ( $myFilePath, "w" ) or die ( "Unable to open file!" );
		fwrite ( $myFile, $_POST ['editor'] );
		fclose ( $myFile );
		$data ['breakingNewsContentURL'] = $myFilePath;
		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $breakingNews->save ( $data );
		
		// doLog($_SESSION ['userId'],4,'edit_allActivityPractice_Submit_Id_:_' . $_GET ['activitypracticeid']);
		
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
	public function example() {
		$this->assign ( "html", $_POST ['editor'] );
		$this->display ();
	}
}
