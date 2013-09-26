<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $title_for_layout; ?></title>
    <meta name="Author" content="Jorge Luis Gómez Valencia" lang="es">
    <?php 
    echo $html->charset('UTF-8')."\n";
    echo $html->css('/js/ext-3.4.1/resources/css/ext-all')."\n";
	echo $html->css('/js/ext-3.4.1/resources/css/xtheme-gray')."\n";
	echo $html->css('/js/Ext.ux.form.FileUploadField')."\n";
	echo $html->css('/js/Ext.ux.grid.FilterRow/Ext.ux.grid.FilterRow')."\n";
	echo $html->css('/js/Ext.ux.grid.RowEditor')."\n";
	echo $html->css('jg.extjs')."\n";
	echo $javascript->link('jquery-1.9.1')."\n";
	echo $javascript->link('Jg.scripts')."\n";
    
    ?>
</head>
<body>
<div id="loading-div">
    <div id="center">
        <img src="/img/comun/logo.png" align="absmiddle" />
        <div class="loading-indicator">Cargando...</div>
    </div>
</div>
<?php
	echo $javascript->link('ext-3.4.1/adapter/jquery/ext-jquery-adapter')."\n";
    echo $javascript->link('ext-3.4.1/ext-all')."\n";
	echo $javascript->link('ext-3.4.1/locale/ext-lang-es')."\n";
	echo $javascript->link('Ext.override.form.Field')."\n";
	echo $javascript->link('Ext.override.form.ComboBox')."\n";
	echo $javascript->link('Ext.override.PagingToolbar')."\n";
	echo $javascript->link('Ext.ux.form.ServerValidator')."\n";
	echo $javascript->link('Ext.ux.form.FileUploadField')."\n";
	echo $javascript->link('Ext.ux.grid.FilterRow/Ext.ux.grid.FilterRow')."\n";
	echo $javascript->link('tiny_mce/tiny_mce')."\n";
	echo $javascript->link('Ext.ux.TinyMCE')."\n";
	echo $javascript->link('tiny_mce/plugins/imanager/interface/common')."\n";
	echo $javascript->link('Ext.ux.grid.RowEditor')."\n";
	echo $javascript->link('Ext.ux.grid.CheckColumn')."\n";
	echo $javascript->link('Ext.util.Format')."\n";
	echo $javascript->link('Ext.ux.dd.GridDragDropRowOrder')."\n";
	echo $javascript->link('swfobject')."\n";
	echo $javascript->link('jquery.swfobject')."\n";
?>
<?php
echo $content_for_layout;
?>
<script>
	$("#loading-div").css('display','none')
</script>
</body>
</html>