<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $title_for_layout; ?></title>
    <meta name="Author" content="Jorge Luis Gómez Valencia" lang="es">
    <?php 
    echo $html->charset('UTF-8')."\n";
	echo $html->css('/js/jquery-ui-1.10.3/amanecer-theme/jquery-ui-1.10.3')."\n";
	echo $html->css('/js/fg.menu/fg.menu.css')."\n";
	echo $html->css('website')."\n";
	echo $html->css($this->layout)."\n";
	echo $html->css('tinyMCE_content')."\n";
	echo $javascript->link('Jg.scripts')."\n";
	echo $javascript->link('jquery-1.9.1')."\n";
	echo $javascript->link('jquery-ui-1.10.3/jquery-ui-1.10.3.min')."\n";
	echo $javascript->link('fg.menu/fg.menu')."\n";
	echo $javascript->link('swfobject')."\n";
	echo $javascript->link('jquery.swfobject')."\n";
    ?>
</head>
<body>
    <div id="main">
    <?php
        echo $this->element('header');
        echo $this->element('flashheader');
        echo $content_for_layout;
        echo $this->element('footer');
        echo $this->element('sql_dump');
    ?>
    </div>
</body>
</html>