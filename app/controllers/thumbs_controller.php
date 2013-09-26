<?php 
class ThumbsController extends AppController
{
    var $name = 'Thumbs';
    var $uses = NULL;
    var $layout = NULL;
    var $autoRender = false;
    
    function index(){
		Configure::write('debug', 0);
		unset($this->params['url']['url']);
		if(isset($this->params['url']['src'])&&!empty($this->params['url']['src'])){
			$this->params['url']['src'] = substr_replace($this->params['url']['src'], WWW_ROOT, 0, 1);
			if (isset($this->params['url']['fltr'])){
				foreach($this->params['url']['fltr'] as $key => $filter):
					$filter=explode('|',$filter);
					if ($filter[0]=='over'){
						$filter[1]=WWW_ROOT.'img/Masks/'.$filter[1];
					}
					if(is_file($filter[1])){
						$filter=implode('|',$filter);
						$this->params['url']['fltr'][$key]=$filter;
					}else{
						die('No existe la máscara: '.$filter[1]);
						unset($this->params['url']['fltr'][$key]);
					}
				endforeach;	
			}
			if(!is_readable($this->params['url']['src'])){
				die('No existe el archivo fuente: '.$this->params['url']['src']);
			}
		}else{
			die('No se envio el parametro de la accion');
		}
		
		uses ('folder');
		$folder = &new Folder(CACHE.'thumbs',false);
		if ($folder!==false) {
			$folder = &new Folder(CACHE.'thumbs', true, 0777);
		}
		
		app::import('Vendor','phpthumb',array('file'=>'phpThumb'.DS.'phpthumb.class.php'));
		$phpThumb = new phpThumb();
		$allowedParameters = array('src', 'new', 'w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'bgt', 'fltr', 'xto', 'ra', 'ar', 'aoe', 'far', 'iar', 'maxb', 'down', 'phpThumbDebug', 'hash', 'md5s', 'sfn', 'dpi', 'sia', 'nocache');
		foreach ($this->params['url'] as $key => $value):

			if (in_array($key, $allowedParameters)) {
				$phpThumb->setParameter($key, $value);
				
			} else {
				
				$phpThumb->ErrorImage('Parametro prohibido: '.$key);
			}
		endforeach;
		$phpThumb->config_imagemagick_path = '/usr/bin/convert';
		$phpThumb->config_prefer_imagemagick = true;
		$phpThumb->config_error_die_on_error = true;
		$phpThumb->config_document_root = '';
		$phpThumb->config_temp_directory = TMP;
		$phpThumb->config_cache_directory = CACHE.'thumbs'.DS;
		$phpThumb->config_cache_disable_warning = false;
		$cacheFilename = md5($_SERVER['REQUEST_URI']);
		$phpThumb->cache_filename = $phpThumb->config_cache_directory.$cacheFilename;
		if(!is_file($phpThumb->cache_filename)){
			if ($phpThumb->GenerateThumbnail()) {
				$phpThumb->RenderToFile($phpThumb->cache_filename);
			} else {
				die('Fallo: '.$phpThumb->error);
			}
		}
		$modified  = filemtime($phpThumb->cache_filename);
		if (headers_sent()) {
			$phpThumb->ErrorImage('Encabezados ya enviados ('.basename(__FILE__).' line '.__LINE__.')');
			exit;
		}
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $modified).' GMT');
		if (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] && ($modified == strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) && @$_SERVER['SERVER_PROTOCOL']) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			exit;
		}elseif(is_file($phpThumb->cache_filename)){
			if ($cachedImage = getimagesize($phpThumb->cache_filename)) {
				header('Content-Type: '.$cachedImage['mime']);
			} elseif (eregi('\.ico$', $phpThumb->cache_filename)) {
				header('Content-Type: image/x-icon');
			}
			readfile($phpThumb->cache_filename);
			exit;
		}
		
	}
}
?>
