<?php
	class Index  extends Action {
		
		function init(){
				
				if( is_array($_SESSION['userinfo']) && !$_SESSION['userinfo']['allow2']  && $_SESSION['userinfo']['school_id'] !=$_GET['scid']  ){
						
						$this->redirect('index/index/scid/'.$_SESSION['userinfo']['school_id']);
						
				}
				
		}	
		function index(){
			
			scid();//判断学校
			
			$setDB = D("setting");			
			
			$arrOne = $setDB->find($_GET['scid']);
			
			$pBook = D("rebook");
			
			$page = new Page($pBook->where(array("scid"=>$_GET['scid']))->total(),2);
			//echo $page->limit;
			$data = $pBook->where(array("scid"=>$_GET['scid']))->limit($page->limit)->order(" id desc ")->select();
				
			$pImgnews = D('imgnews');
			
			$arrimgs = $pImgnews->field('id,title,url,jump_url')->where(array("scid"=>$_GET['scid']))->limit(8)->order('id desc')->select();
					
			$docmentDB = D('document');
			
			$arrlogs = $docmentDB->field('username,times,title,id,sid,dept_id')->where(array("sid"=>$_GET['scid']))->limit(5)->order('id desc')->select();
			
			/*$d = $docmentDB -> query("SELECT m2.name , m1.n ,m2.id  FROM ( SELECT dept_id,COUNT(*) AS n ,sid FROM cc_document GROUP BY dept_id HAVING sid=?) m1 LEFT JOIN cc_tree AS m2 ON m1.dept_id = m2.id","select",array($_GET['scid']));*/
			
		//	$d = $docmentDB -> query("SELECT m2.name ,m2.id ,m1.sid , m1.title , COUNT(m2.id) AS n FROM cc_document  m1 LEFT JOIN  cc_tree AS m2 ON m2.id = m1.dept_id  WHERE sid =? GROUP BY m2.id","select",array($_GET['scid']));
		    //$this->assign("d",$d); 
			
			
			$DBannouncement = D('announcement');
					
			//$nTotal = $DBannouncement->where(array("scid"=>$_GET['scid']))->total();
								
			//$page = new Page($nTotal,20);
				
			$newsList = $DBannouncement->field('id,title,scid,style,times')->where(' scid='.$_GET['scid'].' OR scid=0 ')->limit(10)->order('id desc')->select();
				
		//	$this->assign('fpage',$page->fpage(1,2,4,5,6,7,8));
				
		    $this->assign("announcement",$newsList);
				
			$this->assign("nmun",count($newsList));
					 
			$this->assign("arrlogs",$arrlogs);
			
			$this->assign("data",$data);
			
			$this->assign("fpage",$page->fpage(3,4,5,6));
			
			$this->assign("arrimgs",$arrimgs);
			
			$this->assign("introduce",mb_substr($arrOne['introduce'],0,160,"utf-8").'......');
			
			$this->assign("arrOne2",$arrOne);
			/*			
			$arrReList = $pNews->field('id,title,times')->where(array("sonname"=>"新闻动态","id <"=>$id))->limit(2)->order('id desc')->select();
			$arrCustOne = $pNews->field('id,title,times')->where(array("sonname"=>"客户案例","id !="=>$nId))->limit(1)->order('id desc')->select();
			*/
			
			$this->assign("scid",$_GET['scid']);
			
			$this->display();
		}
		//公告list
		function ggmore(){ 
			$GLOBALS['debug'] = 0;
			
			scid();//判断学校
			
			$setDB = D("setting");			
			
			$arrOne = $setDB->find($_GET['scid']);
			
			$docmentDB = D('announcement');
			
			$nTotal = $docmentDB->where(' scid='.$_GET['scid'].' OR scid=0 ')->total();
								
			$page = new Page( $nTotal,20,"scid/".$_GET['scid']);
			
			$newsList = $docmentDB->field('id,style,title,times')->limit($page->limit)->where(' scid='.$_GET['scid'].' OR scid=0 ')->order('id desc')->select();
			
			$setDB = D("setting");			
			$arrOne2 = $setDB->find($_GET['scid']);
			$this->assign("arrOne2",$arrOne2);
			
			$this->assign("arrOne",$arrOne);

			$this->assign('fpage',$page->fpage(1,2,4,5,6,7,8));
			
			$this->assign("mList",$newsList);
			
			
			$this->assign("scid",$_GET['scid']);
			
			$this->display();
			
		}
		
		function ggcontent(){
			
			
			$GLOBALS['debug'] = 0;
			
			scid();//判断学校
			
			$setDB = D("setting");			
			
			$arrimg = $setDB->find($_GET['scid']);
			
			$docmentDB = D('announcement');
			
			$arrOne = $docmentDB->field('*')->where(array("id"=>$_GET['id']))->limit(1)->select();
			
			$arrOne['unit_img'] = $arrimg['unit_img'];
			
			$filedown = D("filedown");	
			
			$filearr = $filedown->where(array("ggid"=>$_GET['id']))->select();	
			$arrOne2 = $setDB->find($_GET['scid']);
			
			$commentDB = D('ggcomment');
			$commentls = $commentDB->field('*')->where(array("scid"=>$_GET['scid'],"title_id"=>$_GET['id']))->order('id DESC')->select();
			$total = count($commentls);
			
			if( isset($_GET['flag']) && is_numeric($_GET['flag']) ){
				$commentDB->update(array("id"=>$_GET['flag'],"flag"=>0));
			}
			
		
			$this->assign("total",$total);
			$this->assign("filearr",$filearr);
			$this->assign("arrOne2",$arrOne2);
			$this->assign("scid",$_GET['scid']);
			$this->assign("arrOne",$arrOne);
			$this->assign("commentls",$commentls);
			
				$this->assign("json",json_encode($commentls));
			$this->display();
		}
		
		function content(){
			$GLOBALS['debug'] = 0;
			scid();//判断学校
			
			if($_GET['nid']){
				$newsDB = D("news");	
				$newsDB->delete($_GET['nid']);	
			}
			
			$setDB = D("setting");			
			
			$arrimg = $setDB->find($_GET['scid']);
			
			$docmentDB = D('document');
			
			$arrOne = $docmentDB->field('*')->where(array("id"=>$_GET['id'],"sid"=>$_GET['scid']))->limit(1)->select();
			
			$commentDB = D('comment');
			
			$commentls = $commentDB->field('username,times,content,id,cid,username_id')->where(array("scid"=>$_GET['scid'],'cid'=>0,"title_id"=>$_GET['id']))->limit(8)->order('id desc')->select();
			
			$commentlson = $commentDB->field('username,times,content,id,cid')->where(array("scid"=>$_GET['scid'],'cid <>'=>0,"title_id"=>$_GET['id']))->limit(8)->select();
		
			$arrOne['unit_img'] = $arrimg['unit_img'];
			
			$filedown = D("filedown");	
			
			$filearr = $filedown->where(array("wid"=>$_GET['id']))->select();	
			$arrOne2 = $setDB->find($_GET['scid']);
			
			$this->assign("filearr",$filearr);
			$this->assign("arrOne2",$arrOne2);
			$this->assign("scid",$_GET['scid']);
			$this->assign("nodeid",$_GET['nodeid']);
			$this->assign("arrOne",$arrOne);
			$this->assign("commentlson",$commentlson);
			$this->assign("commentls",$commentls);
			$this->display();
		}
		
		
		//最新消息
		function news(){
			$GLOBALS['debug'] = 0;
			if(!$_SESSION['userinfo']['id']){
				$this->redirect('index/index/scid/'.$_GET['scid']);
			}
			scid();//判断学校
			
			
			
			$newsDB = D('news');
			
			$w = array("userid"=>$_SESSION['userinfo']['id'],"flag"=>$_SESSION['userinfo']['allow2']);
			$nTotal = $newsDB->where($w)->total();
								
			$page = new Page( $nTotal,20,"scid/".$_GET['scid']);
			
			$newsList = $newsDB->field('id,url,whoname,content,flag,times')->limit($page->limit)->where($w)->order('id desc')->select();
			
			$setDB = D("setting");			
			$arrOne2 = $setDB->find($_GET['scid']);
			
			$DBannouncement = D('announcement');
				
			$ggarr = $DBannouncement->field('id,title,scid,style,times')->where(' scid='.$_GET['scid'].' OR scid=0 ')->limit(10)->order('id desc')->select();
				
		    $this->assign("announcement",$ggarr);
			
			$this->assign("arrOne2",$arrOne2);

			$this->assign('fpage',$page->fpage(1,2,4,5,6,7,8));
			
			$this->assign("mList",$newsList);
			
			
			$this->assign("scid",$_GET['scid']);
			
			$this->display();
		}
		
		//栏目列表
		function lists(){
			
			$GLOBALS['debug'] = 1;
			
			scid();//判断学校
			
			$setDB = D("setting");			
			
			$arrOne = $setDB->find($_GET['scid']);
			
			$docmentDB = D('document');
			
			$mulu = $this->getmulu($_GET['nodeid']);

			$nTotal = $docmentDB->where(array("dept_id"=>$_GET['nodeid'],"sid"=>$_GET['scid']))->total();
								
			$page = new Page( $nTotal,20,"scid/".$_GET['scid']."/nodeid/".$_GET['nodeid']);
			
			$newsList = $docmentDB->field('id,style,title,times')->limit($page->limit)->where(array("dept_id"=>$_GET['nodeid'],"sid"=>$_GET['scid']))->order('id desc')->select();
			
			$DBannouncement = D('announcement');
				
			$ggarr = $DBannouncement->field('id,title,scid,style,times')->where(' scid='.$_GET['scid'].' OR scid=0 ')->limit(10)->order('id desc')->select();
				
		    $this->assign("announcement",$ggarr);
			
			$this->assign("arrOne2",$arrOne);
			
			$this->assign("arrOne",$arrOne);
			
			$this->assign("mulu",$mulu);

			$this->assign('fpage',$page->fpage(1,2,4,5,6,7,8));
			
			$this->assign("mList",$newsList);
			$this->assign('nodeid',$_GET['nodeid']);
			
			$this->assign("scid",$_GET['scid']);
			
			$this->display();
			
		}
		
		function getmulu($id){
			
			$GLOBALS['debug'] = 0;
			
			if($id){
				$tree = D('tree');
				$mulu = array();
				$str = $j = '';
				$arrmulu = $tree->field('id,pid,name')->find($id);
			$i = 0;
				array_push($mulu,$arrmulu['name']);
				while($arrmulu['pid']!= 1 && $arrmulu['pid']!=-1){
					
					$arrmulu = $tree->field('id,pid,name')->find($arrmulu['pid']);
					if($i == 10){
						break;
					}
					$i++;
					array_push($mulu,$arrmulu['name']);
				}
				
				krsort($mulu);
				
				foreach($mulu as $k => $v){
					$str .= $j.$v;
					$j = '->';
				}
				
				return $str;
			}
			
		}
		
		
		function insert()
		{
			$GLOBALS['debug'] = 0;
			if($_SESSION['userinfo']['allow2']!=1){
				$this->error('请督导登陆再留言',3);
			}
			
			$pBook = D("rebook");
	
			if($_POST['lytext']=="欢迎你的留言")
			{
				$this->error('请填写留言',3);
			}
			
			if(!$_POST['name']){
				$this->error('请督导登陆再留言',3);
			}
			
			$_POST['lytime'] = time(); 
			
			/*if(!preg_match('/^(010|02\d{1}|0[3-9]\d{2})-?\d{7,9}(-\d+)?$/',$_POST['tell'],$arrTell))			
			{
					if(!preg_match("/^[0]?[1][3][0-9]{9}$/",$_POST['tell'],$arrTell))
					{
						$this->error('电话号码不正确');
					}
			}*/
			
			
			if($pBook->insert($_POST,1))
			{
				$this->success("成功");
			}
			
		}
		
		function downfile(){
			
			$GLOBALS['debug'] = 0;
			if($_GET['id']){
				
				//ob_clean();
				//header("Content-type:text/html;charset=utf-8");
				
				// $file_name="cookie.jpg"; 
				
				if($_GET['url_vido']){
					$docmentDB = D('document');
					$docmentDB->field('url_vido')->where(array("id"=>$_GET['id']))->limit(1)->select();
					$arr = explode("/",$arrOne[0]['url_vido']);
					$fileArr = pathinfo($arrOne[0]['url_vido']);
				}else{
					if($_GET['type']){
						$filedown = D('filedown');
						$arrOne = $filedown->field('urlfile')->where(array("id"=>$_GET['id']))->limit(1)->select();
						if(stristr($arrOne[0]['urlfile'],'http://')!==false){
							//header ( 'Content-type: application/pdf' );指定类型
							//header ( 'Content-Disposition: attachment; filename="downloaded.pdf"' );
							//readfile ( $arrOne['urlfile']);
							header('Location:'.$arrOne[0]['urlfile']);
							exit;
						}
						
						$arr = explode("/",$arrOne[0]['urlfile']);
						$fileArr = pathinfo($arrOne[0]['urlfile']);
						
					}else{
						$docmentDB = D('document');
						$arrOne = $docmentDB->field('url')->where(array("id"=>$_GET['id']))->limit(1)->select();
						$arr = explode("/",$arrOne[0]['url']);
						$fileArr = pathinfo($arrOne[0]['url']);
					}
					
				}
				
				$file_name = $arr[count($arr)-1];;
				
				//用以解决中文不能显示出来的问题 
				//$file_name=iconv('UTF-8', 'GB2312',$file_name); 
				$file_sub_path=$_SERVER['DOCUMENT_ROOT'].$fileArr['dirname'].'/'; 
				$file_path=$file_sub_path.$file_name; 
				$this->sendFile($file_path);
				//p($file_path);exit;
				//首先要判断给定的文件存在与否 
				/*if(!file_exists($file_path)){ 
				echo "没有该文件文件"; 
				return ; 
				} 
				$fp=fopen($file_path,"rb"); 
				$file_size=filesize($file_path); 
				//下载文件需要用到的头 
				 
				header("Content-type: application/octet-stream"); 
				header("Accept-Ranges: bytes"); 
				header("Accept-Length:".$file_size); 
				header("Content-Disposition: attachment; filename=".$file_name); 
				$buffer=1024; 
				$file_count=0; 
				//向浏览器返回数据 
				while(!feof($fp) && $file_count<$file_size){ 
				$file_con=fread($fp,$buffer); 
				$file_count+=$buffer; 
				echo $file_con; 
				} 
				fclose($fp);*/
			}
			
		}
		
		function code()
		{
			echo new Vcode();
		}	
		
		function arrlogs(){
			$scid = $_POST['scid'];
			$limit = $_POST['limit'];
			$docmentDB = D('document');
			$arrlogs = $docmentDB->field('username,times,title,id,sid,dept_id')->where(array("sid"=>$scid))->limit($limit,5)->order('id desc')->select();
			if(count($arrlogs))
				$json =  json_encode($arrlogs);
			else
				$json = json_encode(array('error'=>1));
			
			echo $json;
		}		
		
		function comment(){
			$GLOBALS['debug'] = 0;
			if($_POST['id']){
				if($_GET['flag']==1){
					$pBook = D("rebook");
					$nId = $pBook->update($_POST);
					if($nId){
						$json =  json_encode(array('error'=>0,'lytext'=>$_POST['lytext']));
					
					}else{
						$json =  json_encode(array('error'=>1,'content'=>'系统繁忙,修改失败!'));
					}
					
				}else{
					$pBook = D("rebook");
					$_POST['retime'] = time();
					$nId = $pBook->update($_POST);
					if($nId){
						$json =  json_encode(array('error'=>0,'retime'=>date("Y-m-d H:i:s",$_POST['retime']),'avatar'=>$_SESSION['userinfo']['avatar'],'retext'=>$_POST['retext']));
					
					}else{
						$json =  json_encode(array('error'=>1,'content'=>'系统繁忙,回复失败!'));
					}
					
				}
				
					ob_end_clean();
					print_r($json);
			}else{
				if($_GET['flag']==1){//修改评论
					$pBook = D("rebook");
					$lytext = $pBook->field('lytext')->find($_GET['content_id']);
					$this->assign("lytext",$lytext['lytext']);
				}
				$this->assign("scid",$_GET['scid']);
				$this->assign("content_id",$_GET['content_id']);
				$this->display();
			}
			
			
		}	
		/**
 * 发送文件用于下载
 *
 * @author: legend(legendsky@hotmail.com)
 * @link: http://www.ugia.cn/?p=109
 * @description: send file to client
 * @version: 1.0
 *
 * @param string   $fileName      文件名称或路径
 * @param string   $fancyName     自定义的文件名,为空则使用filename
 * @param boolean  $forceDownload 是否强制下载
 * @param integer  $speedLimit    速度限制,单位为字节,0为不限制,不支持windows服务器
 * @param string   $$contentType  文件类型,默认为application/octet-stream
 *
 * @return boolean
 */
function sendFile($fileName, $fancyName = '', $forceDownload = true, $speedLimit = 0, $contentType = '')
{
    if (!is_readable($fileName))// 判断文件是否可读
    {
        header("HTTP/1.1 404 Not Found");
        return false;
    }
	
    $fileStat = stat($fileName);
    $lastModified = $fileStat['mtime'];

    $md5 = md5($fileStat['mtime'] .'='. $fileStat['ino'] .'='. $fileStat['size']);
    $etag = '"' . $md5 . '-' . crc32($md5) . '"';
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastModified) . ' GMT');
    header("ETag: $etag");

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)
    {
        header("HTTP/1.1 304 Not Modified");
        return true;
    }
    if (isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_UNMODIFIED_SINCE']) < $lastModified)
    {
        header("HTTP/1.1 304 Not Modified");
        return true;
    }
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&  $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)
    {
        header("HTTP/1.1 304 Not Modified");
        return true;
    }
    if ($fancyName == '')
    {
        //$fancyName = basename($fileName);
		$arr = explode('/',$fileName);
		$fancyName =  end($arr);
		//file_put_contents('info.txt',$fancyName."==1\n".$fileName,FILE_APPEND);
    }

    if ($contentType == '')
    {
        $contentType = 'application/octet-stream';
    }
    $fileSize = $fileStat['size'];   

    $contentLength = $fileSize;
    $isPartial = false;
    if (isset($_SERVER['HTTP_RANGE']))
    {
        if (preg_match('/^bytes=(d*)-(d*)$/', $_SERVER['HTTP_RANGE'], $matches))
        {    
            $startPos = $matches[1];
            $endPos = $matches[2];
            if ($startPos == '' && $endPos == '')
            {
                return false;
            }

            if ($startPos == '')
            {
                $startPos = $fileSize - $endPos;
                $endPos = $fileSize - 1;
            }
            else if ($endPos == '')
            {
                $endPos = $fileSize - 1;
            }
            $startPos = $startPos < 0 ? 0 : $startPos;
            $endPos = $endPos > $fileSize - 1 ? $fileSize - 1 : $endPos;
            $length = $endPos - $startPos + 1;
            if ($length < 0)
            {
                return false;
            }
            $contentLength = $length;
            $isPartial = true;
        }
    }

    // send headers
    if ($isPartial)
    {
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $startPos-$endPos/$fileSize");

    }
    else
    {
        header("HTTP/1.1 200 OK");
        $startPos = 0;
        $endPos = $contentLength - 1;
    }
    header('Pragma: cache');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Accept-Ranges: bytes');
    header('Content-type: ' . $contentType);
    header('Content-Length: ' . $contentLength);

    if ($forceDownload)
    {
        header('Content-Disposition: attachment; filename="' . rawurlencode($fancyName). '"');//汉字自动转为URL编码
 		//header('Content-Disposition: attachment; filename="' . $fancyName. '"');
    }
    header("Content-Transfer-Encoding: binary");

    $bufferSize = 2048;
    if ($speedLimit != 0)
    {
        $packetTime = floor($bufferSize * 1000000 / $speedLimit);
    }
    $bytesSent = 0;
    $fp = fopen($fileName, "rb");
    fseek($fp, $startPos);
    //fpassthru($fp);

    while ($bytesSent < $contentLength && !feof($fp) && connection_status() == 0 )
    {
        if ($speedLimit != 0)
        {
            list($usec, $sec) = explode(" ", microtime()); 
            $outputTimeStart = ((float)$usec + (float)$sec);
        }
        $readBufferSize = $contentLength - $bytesSent < $bufferSize ? $contentLength - $bytesSent : $bufferSize;
        $buffer = fread($fp, $readBufferSize);       
        echo $buffer;
        ob_flush();
        flush();
        $bytesSent += $readBufferSize;

        if ($speedLimit != 0)
        {
            list($usec, $sec) = explode(" ", microtime()); 
            $outputTimeEnd = ((float)$usec + (float)$sec);

            $useTime = ((float) $outputTimeEnd - (float) $outputTimeStart) * 1000000;
            $sleepTime = round($packetTime - $useTime);
            if ($sleepTime > 0)
            {
                usleep($sleepTime);
            }
        }
    }
   
    return true;
	}
 
}