<?php echo $content_for_layout;
echo "\n";
if(Configure::read('debug')!=0){
	echo '/*'.$this->element('sql_dump').'*/';
}
?>
