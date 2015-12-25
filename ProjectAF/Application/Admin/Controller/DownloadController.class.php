<?php

namespace Admin\Controller;

use Think\Controller;
use FCKeditor\FCKeditor;

class DownloadController extends Controller {
	
	// public function _initialize(){
	// if(!isset($_SESSION['userId'])){
	// $this->error('请先登录 ! ');
	// }
	// }
	public function allDownload() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'DOWNLOAD' );
		
		$download = M ( 'download' );
		try {
			if (isset ( $_GET ['delete'] )) {
				// 传入删除参数
				$download->where ( 'downloadid=' . $_GET ['delete'] )->delete ();
			}
			if (isset ( $_GET ['deleteMulti'] )) {
				// 传入删除多项的参数
				$multi = explode ( ',', $_GET ['deleteMulti'] );
				for($index = 1; $index < count ( $multi ); $index ++) {
					// 从第二个开始删除，第一个的产生是由于U方法生成参数的时候无法不输入一个参数
					if ($multi [$index] != null) {
						$download->where ( 'downloadid=' . $multi [$index] )->delete ();
					}
				}
			}
		} catch ( Exception $e ) {
			// 删除错误
			$this->error ( C ( 'DELETE_FAIL' ) . $e->__toString () );
			return;
		}
		
		// 查询当前所有的工作状态并且分页
		$count = $download->count ();
		$page = new \Think\Page ( $count, C ( 'PAGE_COUNT' ), 'p1' );
		$page->setP ( 'p1' );
		$orderby ['downloadid'] = 'desc';
		$list = $download->order ( $orderby )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		// dump($list);
		// return;
		$this->assign ( 'list', $list ); // 赋值数据集
		$this->assign ( 'page', $page->show () ); // 赋值分页输出
		$this->display ();
	}
	public function addDownload() {
		try {
			$download = M ( 'download' );
			
			$data ['downloadTitle'] = $_POST ['downloadtitle'];
			$data ['downloadReleaseId'] = $_SESSION ['userId'];
			$data ['downloadReleaseDate'] = date ( 'Y-m-d H:i:s', time () );
			$data ['downloadPageView'] = 0;
			
			// $data ['workTendencyContentURL'] = $myFilePath;
			// $data ['workTendencyReleaseInformation'] = '';
			// $data ['workTendencyPageView'] = 0;
			
			// 文件上传
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->maxSize = 3145728; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg',
					'doc',
					'docx',
					'apk',
					'xls',
					'ppt',
					'pptx',
					'rar',
					'zip',
					'pdf'
			); // 设置附件上传类型
			$upload->rootPath = C ( 'APPLICATION_DOWNLOAD_PATH' ); // 设置附件上传根目录
			                                                         // $upload->savePath = 'Download'; // 设置附件上传（子）目录
			                                                         // 上传文件
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( 'File Type Error! ' . $upload->getError () );
			} else { // 上传成功 获取上传文件信息
				foreach ( $info as $file ) {
					// echo $file['savepath'].$file['savename'].'<br />';
					// echo $file['name'];
					$data ['fileName'] = $file ['name'];
					$data ['downloadURL'] = $file ['savename'];
				}
			}
			
			// dump($data);
			// return;
			
			$download->create ( $data );
			$addDownloadId = $download->add ();
			
// 			doLog($_SESSION ['userId'],6,'add_Download_Id_:_' . $addDownloadId);
			
			$this->success ( C ( 'RELEASE_SUCCESS' ), 'allDownload' );
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allDownload' );
		}
	}
	public function editDownload() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'CURRENT_MENU', 'DOWNLOAD' );
		
// 		doLog($_SESSION ['userId'],7,'edit_Download_Id_:_' . $_GET ['downloadid']);
		
		$download = M ( 'download' );
		$editDownload = $download->where ( 'downloadId=' . $_GET ['downloadid'] )->find ();
		
		// dump($editDownload);
		// return;
		
		$this->assign ( 'download', $editDownload );
		$this->display ();
	}
	public function editDownloadSubmit() {
		if (! isset ( $_SESSION ['userId'] )) {
			$this->error ( C ( 'LOGIN_FIRST' ) );
		}
		$this->assign ( 'APPLICATION_NAME', C ( 'APPLICATION_NAME' ) );
		$this->assign ( 'USER_ID', $_SESSION ['userId'] );
		$this->assign ( 'USER_LEVEL', $_SESSION ['userLevel'] );
		$this->assign ( 'CURRENT_MENU', 'WORKTENDENCY' );
		
		$download = M ( 'download' );
		$data ['downloadId'] = $_GET ['downloadid'];
		$data ['downloadTitle'] = $_POST ['downloadTitle'];
		
// 		dump($data);
// 		return;
		
		// 文件上传
		$upload = new \Think\Upload (); // 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->exts = array (
				'jpg',
				'gif',
				'png',
				'jpeg',
				'doc',
				'docx',
				'apk',
				'xls',
				'ppt',
				'pptx',
				'rar',
				'zip',
				'pdf'
		); // 设置附件上传类型
		$upload->rootPath = C ( 'APPLICATION_DOWNLOAD_PATH' ); // 设置附件上传根目录
		                                                         // $upload->savePath = 'Download'; // 设置附件上传（子）目录
		                                                         // 上传文件
		$info = $upload->upload ();
		
		doLog($_SESSION ['userId'],8,'edit_Download_Submit_Id_:_' . $_GET ['downloadid']);
		
		if (! $info) { // 上传错误提示错误信息
			echo '
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					</head>
					<h1 style="line-height:400px;text-align:center">上传错误:' . $upload->getError () . '，1秒后自动关闭</h1>
					<script language="javascript">
						function closeWindow(){
							window.opener=null;
							window.open("","_self")
							window.close();
						}
						setTimeout("closeWindow()",1000);
						
					</script>';
			return ;
		} else { // 上传成功 获取上传文件信息
			foreach ( $info as $file ) {
				// echo $file['savepath'].$file['savename'].'<br />';
				// echo $file['name'];
				$data ['fileName'] = $file ['name'];
				$data ['downloadURL'] = $file ['savename'];
			}
		}

		// $workTendency-> where('workTendencyId=' . $_GET['worktendencyid'])->setField('worktendencycontenturl',$myFilePath);
		$result = $download->save ( $data );
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
	public function startDownload() {
		// dump($_GET ['filePath']);
		// return;
	}
}
