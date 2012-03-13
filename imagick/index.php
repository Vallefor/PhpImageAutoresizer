<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vallefor
 * Date: 20.10.11
 * Time: 15:03
 */
// 217 x 125

include_once "resizelib/catsMagick.php";
include_once "resizelib/filesystem.php";
$files=new filesystem();
$files=$files->getFileList("/imagick/input/",array("FILE"=>"Y"));

foreach($files as $ind=>$val)
{
	$top=new Imagick("top.png");
	$back=new Imagick("back.png");
	$im=new catsMagick("input/".$val);

	$im->resizeAndCrop(217,125);

	$back->compositeimage($im,$im->getImageCompose(),0,1);

	$back->compositeimage($top,$top->getImageCompose(),(217-48)/2,(126-41)/2);

	$back->setformat("jpg");
	$back->setCompressionQuality(95);
	$back->writeImage("output/".$ind.".jpg");

	$top->clear();
	$back->clear();
	$im->clear();
}

die();
/*
$top=new Imagick("top.png");
$back=new Imagick("back.png");
$im=new catsMagick("4.jpg");

$im->resizeAndCrop(217,125); 

$back->compositeimage($im,$im->getImageCompose(),0,1);
$back->compositeimage($top,$top->getImageCompose(),(217-48)/2,(126-41)/2);

//$back->setformat("jpg");
//$back->writeImageFile("") 


header('Content-type: image/jpeg');
echo $back;
 
die();*/
?>