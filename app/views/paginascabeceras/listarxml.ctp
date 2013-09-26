<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<banner width = "" height = ""
	startWith = "1" 
	random = "false"
	backgroundColor = "0xffffff" 
	backgroundTransparency = "100"
	cellWidth = "50"
	cellHeight = "50"
	showMinTime = "0.2"
	showMaxTime = "1.5"
	blur = "50"
	netTime = "0.5"
	alphaNet = "80"
	netColor = "0x000000"
	overColor = "0x473C31"
	normalColor = "0x000000"
	selectedTextColor = "0xffffff"
	selectedButtonAlpha = "70"
	controllerVisible = "true"
	controllerBackgroundVisible = "true"
	prevNextVisible = "true"
	playBtVisible = "true"
	autoPlay = "true"
	navigationButtonsColor = "0x1a1a1a"
	controllerDistanceX = "10"
	controllerDistanceY = "10"
	controllerHeight = "27"
	distanceBetweenControllerElements = "10"
	distanceBetweenThumbs = "3"
	itemNumberSize = "12"
	captionY = "10"
	captionX = "10"			
	captionWidth = "390"
	buttonText = "<?php __('ver mas');?>"
	btnNormalColor = "0xffffff"
	btnOverColor = "0x999999"
	readMoreBackAlpha = "80"
	readMoreBackColor = "0x473C31"
	paddingX = "20"
	paddingY = "15"
	btnSpacingW = "50"
	btnSpacingH = "5"
	loaderColor = "0x000000">
	<?php
    foreach ($result as $item):
    ?>
    <item>
    <?php
        foreach($item as $propiedad => $valor):
			if(empty($valor)){
				?>
				<<?php echo $propiedad;?> />
				<?php
			}elseif(in_array($propiedad,array('path','title','caption'))){
				?>
				<<?php echo $propiedad;?>><![CDATA[<?php echo $valor;?>]]></<?php echo $propiedad;?>>
				<?php
			}else{
				?>
				<<?php echo $propiedad;?>><?php echo $valor;?></<?php echo $propiedad;?>>
				<?php
			}
        endforeach;
    ?>
    </item>
    <?php
    endforeach;
    ?>
</banner>