<?php
if (isset($class)){
	if($class=='error'){
		$class=' ui-state-error';
		$icon='<span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>';
		$titulo='<strong>'.__('alerta',true).':</strong> ';
	}elseif($class=='ok'){
		$class=' ui-state-highlight';
		$icon='<span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>';
		$titulo='<strong>'.__('informacion',true).':</strong> ';
	}else{
		$class=' '.$class;
		$icon='';
		$titulo='';
	}
}else{
	$class='';
	$icon='';
	$titulo='';
}
?>
<div id="flashMessage" class="ui-widget ui-corner-all<?php echo $class;?>"> 
    <p>
    <?php echo $icon;?>
    <?php echo $titulo;?><?php echo isset($message) ? $message : NULL;?>
    </p>
</div>
