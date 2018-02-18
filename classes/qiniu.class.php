<?php
/*
 *      $Id: 2013/8/22 15:27:25 discuz_ftp_ext.php Luca Shin $
 */
class QiNiu
{
	var $enabled = false;
	var $config = array();
	
	var $curobj;

	function __construct() {
		//如果设置attachurl是什么结尾，注意ftp_delete方法，这里删除时结尾要修改.cn 如改成.com就设成这个.com
		$this->config = array('curstorage' => 'qiniu','accesskey'=>'5493Yz3b3ECMS6Z0RNkoq6KI78M-96CnzJ1TBONQ','secretkey'=>'1wlGopC6PiG_wKRMYcDLGQ5xLGw1otQ5c6slQ_sN',
				'attachurl'=>'http://j06a.schoolxt.cn','bucket'=>'dudao');
		$this->enabled = false;
	
		require_once 'io.php';
		require_once 'rs.php';
		require_once 'fop.php';
		
		$GLOBALS['QINIU_UP_HOST'] = 'http://up-z2.qiniup.com';//华南
		$GLOBALS['QINIU_RS_HOST'] = 'http://rs.qbox.me';
		$GLOBALS['QINIU_RSF_HOST'] = 'http://rsf.qbox.me';
		Qiniu_setKeys($this->config['accesskey'], $this->config['secretkey']);
		$this->curobj = true;
		!empty($this->curobj) && $this->enabled = true;
		return true;
	}

	function upload2($source, $target) {
		$putPolicy = new Qiniu_RS_PutPolicy($this->config['bucket']);
		$upToken = $putPolicy->Token(null);
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		$target = substr($target, 0, 1) == '/' ? substr($target, 1) : (substr($target, 0, 2) == './' ? substr($target, 2) : $target);
		list($ret, $err) = Qiniu_PutFile($upToken, $target, PROJECT_PATH.$source, $putExtra);
	//	 $ret == null ? 0 : 1;	
		if($ret){
			$io = new iohandler();
			$io->DeleteFile('.'.$source);
			return $this->config['attachurl'].'/'.$ret['key'];
		}else{
			return 0;
		}
	}
	//删除七牛云资源 $qiniu->ftp_delete("public/uploads/newsimg/2018/02/17/20180217234043224e.png");
	function ftp_delete($path) {
		$this->curobj = new Qiniu_MacHttpClient(null);
		//http://p4ae5j06a.bkt.clouddn.com/public/uploads/newsimg/2018/02/17/20180217234043224e.png
		$arrStr = explode('.cn/',$path);
		$path=$arrStr[1];
		$err = Qiniu_RS_Delete($this->curobj, $this->config['bucket'], $path);
		return $err === null ? 1 : 0;	
	}

}
