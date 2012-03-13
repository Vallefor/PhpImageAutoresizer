<?php
/**
 * Created by JetBrains PhpStorm.
 * Class: catsMagick + Imagick-GD emulator
 * User: Lipovtsev Dmitry aka Vallefor
 * Contact: madsorcerer@gmail.com
 * Date: 26.10.11
 * Time: 11:22
 * Version: 1.1
 */

if(!class_exists("Imagick"))
{
	class Imagick
	{
		const FILTER_LANCZOS=false;
		
		public $src;
		public $imageSize=false;

		public $saveFunc;

		private $curPath;
		private $compression;

		function setimagecompression($int)
		{

		}
		function setimagecompressionquality($int)
		{
			$this->compression=$int;
		}
		function stripimage()
		{

		}
		function getImageGeometry()
		{
			$this->getImageSize();
			return array("width"=>$this->imageSize[0],"height"=>$this->imageSize[1]);
		}
		function readimage($path)
		{
			$this->curPath=$path;
			$fname=$this->getFunctionName($path);
			$this->src=$fname($path);
		}
		function getImageSize()
		{
			if(!$this->imageSize)
				$this->imageSize=getimagesize($this->curPath);
		}
		function getFunctionName()
		{
			$this->getImageSize();
			return "imagecreatefrom".strtolower(substr($this->imageSize["mime"], strpos($this->imageSize["mime"], '/')+1));
		}
		function setimageformat($str)
		{
			$trans=array(
				"jpg"=>"jpeg"
			);
			$this->saveFunc=$trans[$str]?$trans[$str]:$str;
			$this->saveFunc="image".$this->saveFunc;
		}
		function setTransparent(&$link)
		{
			imagesavealpha($link, true);
			$trans_colour = imagecolorallocatealpha($link, 0, 0, 0, 127);
     		imagefill($link, 0, 0, $trans_colour);
		}
		function resizeImage($w,$h,$filter,$sharp)
		{
			$dst=imagecreatetruecolor($w,$h);

			$this->setTransparent($dst);

			$this->getImageSize();
			imagecopyresampled($dst,$this->src,0,0,0,0,$w,$h,$this->imageSize[0],$this->imageSize[1]);
			$this->imageSize[0]=$w;
			$this->imageSize[1]=$h;
			imagedestroy($this->src);
			$this->src=$dst;
			//imagedestroy($dst);
		}
		function cropimage($w,$h,$x,$y)
		{
			$dst=imagecreatetruecolor($w,$h);

			$this->setTransparent($dst);

			$this->getImageSize();

			imagecopyresampled($dst,$this->src,0,0,$x,$y,$w,$h,$w,$h);
			imagedestroy($this->src);
			$this->src=$dst;
		}
		function writeimage($save)
		{
			$sa=$this->saveFunc;
			if($sa=="imagejpeg")
				$sa($this->src,$save,$this->compression);
			else
				$sa($this->src,$save);
		}
		function sharpenimage($v1,$v2)
		{
			//Шарпить через ГД это изврат :)
		}
		function __toString()
		{
			//небольшой хак, GD не может положить вывод в переменную, а юзать буфер вывода совсем не айс.
			$sa=$this->saveFunc;
			//$sa($this->src,NULL,$this->compression);
			if($sa=="imagejpeg")
				$sa($this->src,NULL,$this->compression);
			else
				$sa($this->src,NULL);
			return "";
		}
		function compositeimage(catsMagick &$comp,$someInt,$x,$y)
		{
			$geo=$comp->getImageGeometry();
			imagecopyresampled($this->src,$comp->src,0,0,$x,$y,$geo["width"],$geo["height"],$geo["width"],$geo["height"]);
		}
		function getImageCompose()
		{
			return 40;
		}
		function clear()
		{
			imagedestroy($this->src);
		}
	}
}

class catsMagick extends Imagick
{
	public $curImagePath=false;
	public $contentType;
	function isresizeAllowed($ext,$opt)
	{
		$extAllow=array(
			"jpg"=>true,
			"JPG"=>true,
			"JPEG"=>true,
			"jpeg"=>true,
			"png"=>true,
		);
		$optAllow=array(
			"209x154xC"=>true,
			"500x500xCxL"=>true,
			"610x0xL"=>true,
			"610x0xCxL"=>true,
			"610x0"=>true,
		);
		//return true;
		if($extAllow[$ext] && $optAllow[$opt])
		{
			return true;
		}
		else
			return false;
	}
	function dellOldFiles($minutes)
	{
		$commands=array(
			'find '.$_SERVER["DOCUMENT_ROOT"].CATS_BASE.' -type f -amin +'.$minutes.' -exec rm {} \;',
			'find '.$_SERVER["DOCUMENT_ROOT"].CATS_BASE.' -depth -type d -empty -exec rmdir -v {} \;'
		);
		foreach($commands as $val)
		{
			echo $val."<br/>";
			exec($val,$output);
			print_r($output);
		}
	}
	function readimage($str)
	{

		if(parent::readimage($str))
		{
			$this->curImagePath=$str;
			return true;
		}
		return false;
	}
	function setimageformat($str)
	{
		$arr=array(
			"jpg"=>"image/jpeg",
			"jpeg"=>"image/jpeg",
			"JPG"=>"image/jpeg",
			"JPEG"=>"image/jpeg",
			"png"=>"image/png",
		);

		$this->contentType=$arr[$str];
		parent::setimageformat($str);
	}
	function deleteMagick($realFile)
	{
		$obFiles=new filesystem();
		$dirs=$obFiles->getFileList(CATS_BASE."/",array("DIR"=>true));

		foreach($dirs as $val)
		{
			$file=$_SERVER["DOCUMENT_ROOT"].CATS_BASE."/".$val.$realFile;
			if(is_file($file))
			{
				if(!unlink($file))
				{
					//$obFiles->dellEmptyDirs($_SERVER["DOCUMENT_ROOT"].CATS_BASE,dirname($file));
					return false;
				}
				else
				{
					$obFiles->dellEmptyDirs($_SERVER["DOCUMENT_ROOT"].CATS_BASE,dirname($file));
				}
			}
		}
		return true;
	}
	function readResizeOptions($str)
	{
		///echo $str;
		$ex=explode("x",$str);
		$ex[0]=intval($ex[0]);
		$ex[1]=intval($ex[1]);
		if(($ex[0]==0 || $ex[1]==0) && isset($ex[2]) && $ex[2]!="L" ) unset($ex[2]);

		if(count($ex)>=3)
		{
			if($ex[2]=="C")
				$this->resizeAndCrop($ex[0],$ex[1]);
			else
			{
				$this->rightResize($ex[0],$ex[1]);
			}
			if($ex[2]=="L" || $ex[3]=="L")
			{
				$logo=new catsMagick();
				$logo->readimage($_SERVER["DOCUMENT_ROOT"].CATS_WATERMARK_PATH);
				$this->compositeimage($logo,$logo->getImageCompose(),0,0);
				$logo->clear();
			}
		}
		elseif(count($ex)==2)
		{
			$this->rightResize($ex[0],$ex[1]);
		}

		if(isset($ex[3]) && isset($ex[4]))
			$this->sharpenimage($ex[3],$ex[4]);
	}
	function rightResize($needW,$needH,$cropMode=false,$filter=imagick::FILTER_LANCZOS,$sharp=1)
	{

		$size=$this->getImageGeometry();

		if($needW<0) $needW=0;
		if($needH<0) $needH=0;

		if(($size["width"]>$needW && $needW>0) || ($size["height"]>$needH && $needH>0) || $cropMode)
		{
			if(
				($needH>0 && $needW>0 && (
				(($size["width"]/$needW)<($size["height"]/$needH) && !$cropMode)
					||
				(($size["width"]/$needW)>($size["height"]/$needH) && $cropMode)
				)) || $needW==0
			)
			{
				$h=$needH;
				$w=($needH*$size["width"])/$size["height"];

				$cropY=0;
				$cropX=($w-$needW)/2;
			}
			else
			{
				$w=$needW;
				$h=($needW*$size["height"])/$size["width"];

				$cropX=0;
				$cropY=($h-$needH)/2;
			}
		}
		else
		{
			$w=$size["width"];
			$h=$size["height"];
		}

		$this->resizeImage($w,$h,$filter,$sharp);
		return array("x"=>$cropX,"y"=>$cropY);
	}
	function resizeAndCrop($needW,$needH,$filter=imagick::FILTER_LANCZOS,$sharp=1)
	{

		//echo $this->lastCropX."x".$this->lastCropY;
		$crop=$this->rightResize($needW,$needH,true,$filter,$sharp);
		$this->cropimage($needW,$needH,$crop["x"],$crop["y"]);
	}
}
?>