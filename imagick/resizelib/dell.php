<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vallefor
 * Date: 09.12.11
 * Time: 9:54
 */
include_once "catsMagick.php";
include_once "config.php";
$cat=new catsMagick();
$cat->dellOldFiles(43200); //30 дней
?>
