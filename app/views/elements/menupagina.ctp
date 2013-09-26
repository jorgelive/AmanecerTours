<?php
extract ($data);
if($Pagina['publicado']=='si'){
	if(!empty($Pagina['predeterminado'])){
		echo '<a href="/paginas/detalle/'.$Pagina['id'].'/idioma:'.Configure::read('Config.language').'" class="ui-corner-all">'.$Pagina['title'].'</a>';
	}else{
		echo '<a href="#">'.$Pagina['title'].'</a>';
	}
}
?>