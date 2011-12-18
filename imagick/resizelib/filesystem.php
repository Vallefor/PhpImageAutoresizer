<?php
class filesystem
{
	function dellEmptyDirs($base,$start)
	{
		if($start!=$base)
		{
			if($this->isEmptyDir($start))
			{
				if(rmdir($start))
				{
					$this->dellEmptyDirs($base,dirname($start));
				}
			}
		}
	}
	function isEmptyDir($dir)
	{
		return (($files = @scandir($dir)) && count($files) <= 2);
 	}
	function getFileList($dir,$arParams)
	{
		$iReturn=array();
		$dir=$_SERVER['DOCUMENT_ROOT'].$dir;
		if ($handle = opendir($dir))
		{
		    while (false !== ($file = readdir($handle))) 
		    {
		    	if ($file!="." && $file!="..")
		    	{
			    	if (is_dir($dir.$file) && $arParams["DIR"]==true)
			    	{
			    		$iReturn[]=$file;
			    	}
			    	if (is_file($dir.$file) && $arParams["FILE"]==true)
			    	{
			    		$iReturn[]=$file;
			    	}
		    	}
		    }
		    closedir($handle); 
		} 
		return $iReturn;
	}
	function makeSelector($selector,$dir,$arParams)
	{
		if (!empty($selector["class"])) $selector["class"]=" class=\"".$selector["class"]."\"";
			else $selector["class"]="";
		if (!empty($selector["id"])) $selector["id"]=" id=\"".$selector["id"]."\"";
			else $selector["id"]="";
			
		$list=filesystem::getFileList($dir,$arParams);
		$iReturn='<select name="'.$selector["name"].'"'.$selector["id"].$selector["class"].'>';
			if ($selector["value"]=="") $s=" selected";
				else $s="";
			$iReturn.='<option value=""'.$s.'>'.$val.'</option>';
		foreach($list as $val)
		{
			if ($selector["value"]==$val) $s=" selected";
				else $s="";
			$iReturn.='<option value="'.$val.'"'.$s.'>'.$val.'</value>';
		}
		$iReturn.="</select>";
		return $iReturn;
	}
}
//$arr=filesystem::makeSelector(array("value"=>"static"),"/eva2/modules/",array("DIR"=>true));
//echo $arr; 
?>