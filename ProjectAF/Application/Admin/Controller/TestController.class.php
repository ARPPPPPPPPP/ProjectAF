<?php
namespace Admin\Controller;
use Think\Controller;
class TestController extends Controller {
	public function test(){
		$fp = fopen("E:\\PhpTimedTask\\test.txt", "a+");
		fwrite($fp, date("Y-m-d H:i:s") . "successed \n");
		fclose($fp);
	}
	
    public function testForm(){
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
					'zip' ,'amr'
			); // 设置附件上传类型
			$upload->rootPath = './Public/DownloadTest/'; // 设置附件上传根目录
			                                                         // $upload->savePath = 'Download'; // 设置附件上传（子）目录
			                                                         // 上传文件
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$this->error ( $upload->getError () );
			} else { // 上传成功 获取上传文件信息
				foreach ( $info as $file ) {
					// echo $file['savepath'].$file['savename'].'<br />';
					// echo $file['name'];
					$data ['fileName'] = $file ['name'];
					$data ['downloadURL'] = $file ['savename'];
				}
			}
			
			dump($data);
			return;
			
		} catch ( Exception $e ) {
			$this->error ( C ( 'RELEASE_FAIL' ) . $e->__toString (), 'allDownload' );
		}
    }
}