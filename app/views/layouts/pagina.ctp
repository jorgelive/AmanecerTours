<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $title_for_layout." - ". Configure::read('Empresa.nombre'); ?></title>
    <meta name="Author" content="Jorge Luis Gómez Valencia"/>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" type="image/gif" href="animated_favicon1.gif" />
<?php
    echo $html->charset('UTF-8')."\n";
	echo $html->css('/js/jquery-ui-1.10.3/amanecer-theme/jquery-ui-1.10.3')."\n";
	echo $html->css('/js/fg.menu/fg.menu')."\n";
    echo $html->css('/js/jquery.colorbox/style/colorbox')."\n";
	echo $html->css($this->layout)."\n";
	echo $html->css('tinyMCE_content')."\n";
	echo $javascript->link('Jg.scripts')."\n";
	echo $javascript->link('jquery-1.9.1')."\n";
	echo $javascript->link('jquery-ui-1.10.3/jquery-ui-1.10.3.min')."\n";
    echo $javascript->link('jquery.form')."\n";
	echo $javascript->link('fg.menu/fg.menu')."\n";
	echo $javascript->link('swfobject')."\n";
	echo $javascript->link('jquery.swfobject')."\n";
    echo $javascript->link('jquery.colorbox/jquery.colorbox')."\n";
    ?>
</head>
<body>
    <?php
    echo $content_for_layout;
    ?>

</body>
</html>