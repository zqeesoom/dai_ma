<?php
	class Document {
		
		function index(){
			
			$GLOBALS['debug'] = 0;
			
			$this->assign("scid",$_GET['scid']);
				
			/*$arrOnes = $tree->find();

			if($arrOnes){
				writeJSON($arrOnes);
			}*/
			$_SESSION['userinfo']['username'];
			$this->display();
			
		}
		
		function modfiy(){
			$this->assign("scid",$_GET['scid']);
			$GLOBALS['debug'] = 0;
			
			if(!empty($_POST)){//post提交
				
				$docmentDB = D('document');
				
				//$_POST['content'] = stripslashes($_POST['content']);
				
				$arrNew = $docmentDB->find($_POST['id']);
				
				/*if($arrNew['url_vido']!=$_POST['url_vido']){
					$_POST['url_vido'] = tomv($_POST['url_vido']);
				}
				if(!$_FILES['url']['error'])
				{
					
						$pImgUp = new FileUpload();
					
						$pImgUp->set('path', 'public/uploads/newsimg')
					
						->set("maxSize", 20000000)          //设置上传大小，单位字节
					
						//设置允许上传的类型
					
						->set("allowType", array("gif", "jpg", "png","doc","xls","txt","zip","ppt","rar","docx"))
					
						//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
					
						->set("israndname", false);				
					
						if( $pImgUp->upload('url') )
						{ 
							 $strImgName = $pImgUp->getFileName();  
							
						}else
						{
							$this->error($pImgUp->getErrorMsg(), 10);
							exit;
						}
						
						 $strImgName = $GLOBALS["public"]."uploads/newsimg/".$strImgName;
						 
						 $_POST['url'] = $strImgName;
						 
						 $io = new iohandler();
				   		 $io->DeleteFile('.'.$arrNew['url']);
				}*/
				
			if(!$_FILES['url_vido']['error'])
			{
				
					$pImgUp = new FileUpload();
				
					$pImgUp->set('path', 'public/uploads/newsimg')
									
					->set("maxSize", 40000000)          //设置上传大小，单位字节
				
					//设置允许上传的类型
				
					->set("allowType", array("flv"))
				
					//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
				
					->set("israndname", true);				
				
					if( $pImgUp->upload('url_vido') )
					{ 
						 $strImgName = $pImgUp->getFileName();  
						
					}else
					{
						$this->error($pImgUp->getErrorMsg(), 10);
						exit;
					}
					
				    $strImgName = $GLOBALS["public"]."uploads/newsimg/".$strImgName;
					
				    $_POST['url_vido'] = $strImgName;
					
					$io = new iohandler();
				   	$io->DeleteFile('.'.$arrNew['url_vido']);
			}
				
				 
				 $nId = $docmentDB->update($_POST);
				 
				 
				/*添加多附件*/
				if(isset($_FILES['file_url'])){
						$pImgUp = new FileUpload();
					
						$pImgUp->set('path', 'public/uploads/downimg')
					
						->set("maxSize", 20000000)          //设置上传大小，单位字节
					
						//设置允许上传的类型
					
						->set("allowType", array("doc","xlsx","xls","txt","zip","ppt","rar","docx"))
					
						//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
					
						->set("israndname", false);
					
					
							if( $pImgUp->upload('file_url') )
							{ 
								 $strImgName = $pImgUp->getFileName();  
								 
								 $pImgnews = D('filedown');
								
								 foreach($strImgName as $key=>$val)
								 {
									 $arrData['urlfile'] 	=  '/public/uploads/downimg/'.$val;
									 
									 $arrData['wid']  = $_POST['id'];
									 
									 $qiniu = new QiNiu();
									 $strFileNew = $qiniu->upload2($arrData['urlfile'], $arrData['urlfile']);
									 if($strFileNew){
									 	$arrData['urlfile'] = $strFileNew;
									 }else{
									 	$arrData['urlfile'] = "文件上传失败";
									 }
									
									 $pImgnews->insert($arrData);
								 }
													
							   }
						}
					
				$this->success("修改成功",100);
				
			
				
				
				
			}else{
				
				if($_GET['id'] ){
				
				$document = D('document');
				
				$filedown = D('filedown');
				
				$filearr = $filedown->where(array("wid"=>$_GET["id"]))->select();
				
				$arrOnes = $document->find($_GET["id"]);
				
				$this->assign("arrOnes",$arrOnes);
				
				$this->assign("filearr",$filearr);
				
				$this->display('updatenews');
				
				}
			}
			
		}
		
		function deletefiled(){
			if($_GET['id'] ){
							
					$docmentDB = D('filedown');
					
					$arrNew = $docmentDB->find($_GET['id']);
					
					if( !empty($arrNew['urlfile'])){
					
						if(stristr($arrNew['urlfile'],'http://')!==false){
							$qiniu = new QiNiu();
							$qiniu->ftp_delete($arrNew['urlfile']);
						}else{
							$io = new iohandler();
							$io->DeleteFile('.'.$arrNew['urlfile']);
						}
						
						
					}				
					
									
					$docmentDB->delete($_GET['id']);
					
					$this->success("删除成功");
				}
			else
			{
				$this->error("删除失败");
			}
		}
		
		function delete(){
			
			if($_GET['id'] ){
						
				$docmentDB = D('document');
				
				$arrNew = $docmentDB->find($_GET['id']);
				
				if( !empty($arrNew['content'])){
					
					//i-忽略英文字母大小写 $arrmatg[0][0]匹配整个模式，$arrmatg[1][0]匹配是子表达式
					if(preg_match_all("/<img.*?src=[\"\']?(.*?)[\"\']?\s.*?>/i", html_entity_decode($arrNew['content']), $Arrmat,PREG_PATTERN_ORDER
							)){
								for ($i = 0; $i < count($Arrmat[0]); $i++)
								{
									
									if(stristr($Arrmat[1][$i],'http://')!==false){
										$qiniu = new QiNiu();
										$qiniu->ftp_delete($Arrmat[1][$i]);
									}else{
										$io = new iohandler();
										$io->DeleteFile('.'.$Arrmat[1][$i]);
									}
								}
					}
					
					$io = new iohandler();
				
			  	    $io->DeleteFile('.'.$arrNew['url']);
					
				}				
				if( !empty($arrNew['url_vido'])){
					$io = new iohandler();
					$io->DeleteFile('.'.$arrNew['url_vido']);
				}
				
				$filedown = D('filedown');
				$arrNew = $filedown->where(array('wid'=>$_GET['id']))->find();
					
			   if( !empty($arrNew['urlfile'])){
				
					if(stristr($arrNew['urlfile'],'http://')!==false){
						$qiniu = new QiNiu();
						$qiniu->ftp_delete($arrNew['urlfile']);
					}else{
						$io = new iohandler();
						$io->DeleteFile('.'.$arrNew['urlfile']);
					}
					
					 $filedown->delete($arrNew['id']);
				}
								
				$docmentDB->delete($_GET['id']);
				
				$this->success("删除成功");
			}
			else
			{
				$this->error("删除失败");
			}
		}
		
		function looklist(){
						
			$GLOBALS['debug'] = 0;
			
			
			if(isset($_GET['nodeid'])&& is_numeric(($_GET['nodeid']))){
				
				$docmentDB = D('document');
				
				if($_SESSION['userinfo']['allow3']){
							
					$nTotal = $docmentDB->where(array("dept_id"=>$_GET['nodeid']))->total();
								
					$page = new Page( $nTotal,20,"/nodeid/".$_GET['nodeid']);
				
					$newsList = $docmentDB->field('id,style,title,times,sid')->limit($page->limit)->where(array("dept_id"=>$_GET['nodeid']))->order('id desc')->select();
				}else{
					$scid = $_SESSION['userinfo']['school_id'];
								
					$nTotal = $docmentDB->where(array("dept_id"=>$_GET['nodeid'],"sid"=>$scid))->total();
								
					$page = new Page( $nTotal,20,"scid/".$scid."/nodeid/".$_GET['nodeid']);
				
					$newsList = $docmentDB->field('id,style,title,times')->limit($page->limit)->where(array("dept_id"=>$_GET['nodeid'],"sid"=>$scid))->order('id desc')->select();
				}
	
				$this->assign('fpage',$page->fpage(1,2,4,5,6,7,8));
				
				$this->assign("arrList",$newsList);
				
				$this->assign("scid",$scid);
				
				$this->assign("nodeid",$_GET['nodeid']);
			
				$this->display();
				
			}else{	
				$this->assign("scid",$_GET['scid']);
				$this->display('modfiy');
			}
					
			
		}
		
		function add(){
						
			$GLOBALS['debug'] = 0;
			
			
			
			if(isset($_GET['nodeid'])&& is_numeric(($_GET['nodeid']))){
				
				$scid = $_SESSION['userinfo']['school_id'];
				
				$this->assign("scid",$scid);
							
				$this->assign("nodeid",$_GET['nodeid']);
			
				$this->display('addnews');
				
			}	
			else{
				$this->display();
			}			
			
		}
		
	   function uploadeimg(){
		  
			$GLOBALS['debug'] = 0;
			
			if(!$_FILES['imgFile']['error'])
			{
					$pImgUp = new FileUpload();
				
					$pImgUp->set('path', 'public/uploads/newsimg')
									
					->set("maxSize", 10000000)          //设置上传大小，单位字节
				
					//设置允许上传的类型
				
					->set("allowType", array("gif", "jpg", "png"))
				
					//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
				
					->set("israndname", true);				
				
					if( $pImgUp->upload('imgFile') )
					{ 
						 $strImgName = $pImgUp->getFileName();  
						 $newImg = new Image('public/uploads/newsimg');//图片缩略
						 $strImgName = $newImg->thumb($strImgName,'670','670','');
						
					}else
					{
						$this->error($pImgUp->getErrorMsg(), 10);
						exit;
					}
					
				    $strImgName = $GLOBALS["public"]."uploads/newsimg/".$strImgName;
					
										    
					$qiniu = new QiNiu();
					$strImgNew = $qiniu->upload2($strImgName, $strImgName);
					if($strImgNew){
						writeJSON(array('error' => 0, 'url' => $strImgNew));
					}
					exit; 
					
			}
			
	   }	
		
	   function doadd(){
		   
	   		$pNews = D("document");
			
			if(empty($_POST['title'])){
				$this->error("标题不能为空",100, 'index');exit;
			}
			
			if(empty($_POST['content'])){
				$this->error("内容不能为空",100, 'index');exit;
			}
			
			$_POST = array_merge($_POST,array("times"=>time()));
			
			//$_POST['content'] = stripslashes($_POST['content']);
			
			//$_POST['url_vido'] = tomv($_POST['url_vido']);
			/*
			
			if(!$_FILES['url']['error'])
			{
				
					$pImgUp = new FileUpload();
				
					$pImgUp->set('path', 'public/uploads/newsimg')
				
					->set("maxSize", 20000000)          //设置上传大小，单位字节
				
					//设置允许上传的类型
				
					->set("allowType", array("gif", "jpg", "png","doc","xls","txt","zip","ppt","rar","docx"))
				
					//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
				
					->set("israndname", false);				
				
					if( $pImgUp->upload('url') )
					{ 
						 $strImgName = $pImgUp->getFileName();  
						
					}else
					{
						$this->error($pImgUp->getErrorMsg(), 10);
						exit;
					}
					
					 $strImgName = $GLOBALS["public"]."uploads/newsimg/".$strImgName;
					 
					
					 
					 $_POST['url'] = $strImgName;
			}*/
			if(!$_FILES['url_vido']['error'])
			{
				
					$pImgUp = new FileUpload();
				
					$pImgUp->set('path', 'public/uploads/newsimg')
									
					->set("maxSize", 40000000)          //设置上传大小，单位字节
				
					//设置允许上传的类型
				
					->set("allowType", array("flv"))
				
					//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
				
					->set("israndname", true);				
				
					if( $pImgUp->upload('url_vido') )
					{ 
						 $strImgName = $pImgUp->getFileName();  
						
					}else
					{
						$this->error($pImgUp->getErrorMsg(), 10);
						exit;
					}
					
				    $strImgName = $GLOBALS["public"]."uploads/newsimg/".$strImgName;
					
				    $_POST['url_vido'] = $strImgName;
			}
			
			 
			 $nId = $pNews->insert($_POST);
			 
			
			
			if($nId)
			{
				/*添加多附件*/
				if(isset($_FILES['file_url'])){
				$pImgUp = new FileUpload();
			
				$pImgUp->set('path', 'public/uploads/downimg')
			
				->set("maxSize", 20000000)          //设置上传大小，单位字节
			
				//设置允许上传的类型
			
				->set("allowType", array("xlsx","doc","xls","txt","zip","ppt","rar","docx"))
			
				//设置启用上传后随机文件名，true启用（默认）， false使用原文件名
			
				->set("israndname", false);
			
			
					if( $pImgUp->upload('file_url') )
					{ 
						 $strImgName = $pImgUp->getFileName();  
						 
						 $pImgnews = D('filedown');
						
						 foreach($strImgName as $key=>$val)
						 {
							 $arrData['urlfile'] 	=  '/public/uploads/downimg/'.$val;
							 
							 $arrData['wid']  = $nId;
							 
							 $qiniu = new QiNiu();
							 $strFileNew = $qiniu->upload2($arrData['urlfile'], $arrData['urlfile']);
							 if($strFileNew){
							 	$arrData['urlfile'] = $strFileNew;
							 }else{
							 	$arrData['urlfile'] = "文件上传失败";
							 }
							  
							 $pImgnews->insert($arrData);
						 }
						 				 	
					   }
 				}
				$this->success("添加成功",100);
			}
			else
			{
				$this->error("添加失败",100, 'index');
			}
	   
	   }
		
		
	
	}