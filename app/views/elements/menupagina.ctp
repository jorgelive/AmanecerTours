<?php
extract ($data);
if($Pagina['publicado']==1){
	if($Pagina['notempty']==1){
		echo '<a href="/paginas/index/'.$Pagina['id'].'/idioma:'.Configure::read('Config.language').'" class="ui-corner-all">'.$Pagina['title'].'</a>';
	}else{
		echo '<a href="#">'.$Pagina['title'].'</a>';
	}
}
?>