<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lipovtsev Dmitry aka Vallefor
 * Contact: madsorcerer@gmail.com
 * Date: 26.10.11
 * Time: 11:08
 */
//print_r($_SERVER);
include_once "config.php";
include_once "filesystem.php";
include_once "catsMagick.php";

$ex=explode("?",$_SERVER["REQUEST_URI"]);

$file=$ex[0];

preg_match_all("#^".CATS_BASE."/(.*)/#U",$file,$matches);
$woFile=preg_replace("#^".CATS_BASE."/(.*)/#U","/",$file);
$realFile=$_SERVER["DOCUMENT_ROOT"].$woFile;

$pInfo=pathinfo($file);

$resizeOptions=$matches[1][0];

$cat=new catsMagick();

if($resizeOptions=="D")
{
	$cat->deleteMagick($woFile);
	die("deleted");
}

if($cat->isresizeAllowed($pInfo["extension"],$resizeOptions) && is_file($realFile))
{
	$cat->readimage($realFile);
	$cat->readResizeOptions($resizeOptions);

	$cat->setimagecompressionquality(CATS_JPG_QUALITY);
	$cat->stripimage();

	$cat->setimageformat($pInfo["extension"]);
	
	if(!is_dir($_SERVER["DOCUMENT_ROOT"].$pInfo["dirname"]))
		mkdir($_SERVER["DOCUMENT_ROOT"].$pInfo["dirname"],CATS_DIR_CHMOD,true);
	$cat->writeimage($_SERVER["DOCUMENT_ROOT"].$pInfo["dirname"]."/".$pInfo["basename"]);
	chmod($_SERVER["DOCUMENT_ROOT"].$pInfo["dirname"]."/".$pInfo["basename"],CATS_FILE_CHMOD);

	header('Content-type: '.$cat->contentType);
	echo $cat;
}
?>