<?php
define("CATS_BASE","/autoresize"); //относительный путь к папке, куда будем скидывать отресайзенные изображения
define("CATS_DIR_CHMOD",0777); //Права на папки
define("CATS_FILE_CHMOD",0777); //права на файлы
define("CATS_JPG_QUALITY",95); //Качество JPEG сжатия



/*
 * разрешенные форматы, находятся тут:
 * catsMagick::isresizeAllowed
 *
 * В .htaccess должно быть следующее ():

	<IfModule mod_rewrite.c>
		RewriteEngine On

		#должна быть равна CATS_BASE но без первого слеша и с последним слешем
		RewriteCond %{REQUEST_FILENAME} autoresize/

		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-l
		RewriteCond %{REQUEST_FILENAME} !-d

		#путь к  скрипту ресайза
		RewriteRule ^(.*)$ /imagick/resizelib/autoresize.php [L]
	</IfModule>
 */

?>