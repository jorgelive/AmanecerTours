<div id="main">
    <div id="header">
        <div class="column col_1">
			<div class="ind">
                <div id="cse-search-form" style="width: 100%;">cargando</div>
                <script src="http://www.google.com/jsapi" type="text/javascript"></script>
                <script type="text/javascript">
					google.load('search', '1', {language : '<?php echo substr(Configure::read('Config.language'),0,2);?>', style : google.loader.themes.BUBBLEGUM});
					google.setOnLoadCallback(function() {
						var customSearchControl = new google.search.CustomSearchControl('009881817308642712426:uwa3h-4zegq');
						customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
						var options = new google.search.DrawOptions();
						options.setSearchFormRoot('cse-search-form');
						options.setAutoComplete(true);
						customSearchControl.draw('cse', options);
					}, true);
					$('#header .col_1 .ind .gsc-search-button input').hover(
						function () {
						 	$(this).addClass("over");
						},
						function () {
						 	$(this).removeClass("over");
						}
					)
				  
                </script>
            </div><!-- ind  -->
		</div><!-- col_1  -->
        <div class="column col_2">
			<div class="ind">
            	<a class="adminBotones" href="http://correo.andeanways.com/"><?php echo __('correo');?></a>
                <a class="adminBotones" href="/paginas/administracion"><?php echo __('administracion');?></a>
                <a class="idiomaBotones" href="http://extra.andeanways.com/webim/client.php?locale=<?php echo substr(Configure::read('Config.language'),0,2);?>"><?php echo __('soporte');?></a>
                <?php
                foreach(Configure::read('Empresa.languageList') as $key => $value):
				?>
                <a class="idiomaBotones" href="/paginas/index/idioma:<?php echo $key;?>/"><?php echo $value;?></a>
                <?php
                endforeach;
				?>
            </div><!-- ind  -->
            <script type="text/javascript">    
            $(function(){
                $('#header .col_2 .ind a').addClass('ui-widget ui-corner-bottom ui-state-default');
				$('#header .col_2 .ind a').hover(
                    function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus');},
                    function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default');}
                );
            });
            </script>
        </div><!-- col_2  -->
		<div class="clear"></div>
	</div><!-- header  -->
	<div id="logo">
        <img src="/img/comun/logo.png" />
    </div><!-- logo  -->
	<?php
    echo $this->element('menu');
	?>
	<div id="content">
		<div id="cse" style="width:100%;"></div>
        <div class="row_1">
            <div class="column col_1">
                <div class="indent">
                    <?php
					$tipos=Configure::read('Default.tipos');
					foreach($pagina['Pagina'] as $key => $valor):
					if(in_array($key,array('texto','imagen','video','adjunto'))&&$valor=='si'){
						$tabs[$key]=$pagina['Paginas'.$key];
					}
					endforeach;
					?>
                    <div id="tabs">
                        <ul>
                            <li class="pageTitle"><a><?php echo $pagina['Pagina']['title'];?></a></li>
							<?php
							foreach ($tabs as $key => $tab):
							?>
                            	<li><a href="#tab-<?php echo $key;?>"><?php echo $tipos{$key};?></a></li>
                            <?php
							endforeach;
							?>
                        </ul>
                        <?php
						foreach ($tabs as $key => $tab):
						?>
                        <div id="tab-<?php echo $key;?>">
                            <?php
							if($key=='texto'){
							?>
								<div class="mceContentBody"><?php echo $tab['contenido'];?></div>
                                <div class="clear"></div>
                            <?php
							}elseif($key=='imagen'){
							?>
								<ul id="<?php echo $key.'-'.$pagina['Pagina']['id'];?>">
								<?php
                                foreach($tab as $imagen):
                                $thumbCrop='C';
                                $thumbSize=200;
                                ?>
                                    <li>
                                        <a href="<?php echo $imagen['imagen']['path'];?>" rel="Galeria[galeria1]" title="">
                                            <img class="backgroundImage" alt="<?php echo $imagen['title'];?>" src="/thumbs/index/?src=<?php echo $imagen['imagen']['path'];?>&h=<?php echo $thumbSize?>&w=<?php echo $thumbSize?>&zc=<?php echo $thumbCrop;?>" />
                                            <img class="mask" src="/css/images/mascaraFoto.png" />
                                        </a>
                                    </li>
								<?php
                                endforeach;
                                ?>
                                </ul>
                                <div class="clear"></div>
                                <script type="text/javascript">
									jQuery(document).ready(function($) {
										$("#<?php echo $key.'-'.$pagina['Pagina']['id'];?> a[rel^='Galeria']").prettyPhoto({
											animationSpeed:'slow'
											,theme:'light_square'
											,slideshow:2000
											,autoplay_slideshow: false
										});
									});
								</script>
							<?php
							}elseif($key=='video'){
							?>
								<ul id="<?php echo $key.'-'.$pagina['Pagina']['id'];?>">
								<?php
                                foreach($tab as $video):
                                ?>
                                    <li>
                                        
                                        <a href="<?php echo $video['codigo']['url'];?>" rel="Video[galeria1]" title="<?php echo $video['descripcion'];?>">
                                            <img class="backgroundImage" alt="" src="<?php echo $video['codigo']['m_img'];?>" />
                                            <img class="mask" src="/css/images/mascaraVideo.png" />
                                        </a>
                                    </li>
								<?php
                                endforeach;
                                ?>
                                </ul>
                                <div class="clear"></div>
                                <script type="text/javascript">
									jQuery(document).ready(function($) {
										$("#<?php echo $key.'-'.$pagina['Pagina']['id'];?> a[rel^='Video']").prettyPhoto({
											animationSpeed:'slow'
											,theme:'light_square'
											,slideshow:2000
										});
									});
								</script>
							<?php
							}elseif($key=='adjunto'){
								foreach($tab as $adjunto):
								?>
                                    <a href="<?php echo $adjunto['adjunto']['path'];?>">
                                    <div class="adjuntoContainer">
                                    <img src="<?php echo $adjunto['adjunto']['icon'];?>" />
                                    <p><?php echo $adjunto['title'];?></p>
                                    </div>
                                    </a>
                                <?php
								endforeach;
							}
							?>
                        </div>
						<?php
						endforeach;
						?>
                    </div><!-- tabs  -->
                    <script>
						<?php
						if($pagina['Pagina']['predeterminado']=='texto'){
							$predeterminado=0;
						}elseif($pagina['Pagina']['predeterminado']=='imagen'){
							$predeterminado=1;
						}elseif($pagina['Pagina']['predeterminado']=='video'){
							$predeterminado=2;
						}elseif($pagina['Pagina']['predeterminado']=='adjunto'){
							$predeterminado=3;
						}
						?>
						$(function() {
							$("#content .indent #tabs").tabs().tabs("select",<?php echo $predeterminado;?> );
						});
					</script>
            	</div><!-- indent  -->
			</div><!-- col_1  -->
            <div class="column col_2">
				<script>
					$(function() {
						$("#accordion").accordion();
					});
				</script>
                <div class="indent">
                    <div id="accordion">
                        <?php
                        if($pagina['Pagina']['oferta']=='si'){
                        ?>
                        <h3><a href="#"><?php __('ofertas');?></a></h3>
                        <div id="ofertas">
                            <ul>
                            <?php
                            foreach ($pagina['Paginasoferta'] as $oferta):
                                ?>
                                <li class="oferta">
                                    <h4 class="titulo">
                                    	<?php echo $oferta['title'];?>
                                    </h4>
                                    <div class="clear notas">
                                        <h5><?php echo __('notas');?>:</h5>
                                        <span class="texto">
                                        <?php echo $oferta['notas'];?>
                                        </span>
                                    </div>
                                    <div class="clear condiciones">
                                        <h5><?php echo __('condiciones');?>:</h5>
                                        <span class="texto">
                                        <?php echo $oferta['condiciones'];?>
                                        </span>
                                    </div>
                                    <h5 class="clear precio">
										<?php echo __('desde');?> : <?php echo $oferta['precio'];?></h5>
                                    <div class="clear"></div>
                                </li>
                                <?php
                            endforeach;
                            ?>
                            </ul>
                        </div><!-- ofertas  -->
                        <?php
                        }
                        ?>
                        <?php
                        if($pagina['Pagina']['contacto']=='si'){
                        ?>
                        <h3><a href="#"><?php __('contacto_form');?></a></h3>
                        <div id="contactForm">
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $.ajax({
                                        type: "POST"
                                        ,url: "/paginasformularios/form"
                                        ,data: "idioma=<?php echo Configure::read('Config.language');?>&destinatario=<?php echo $pagina['Paginascontacto']['destinatario'];?>&cco=<?php echo $pagina['Paginascontacto']['cco'];?>&title=<?php echo __('acerca de').': '.$pagina['Pagina']['title'];?>"
                                        ,success: function(respuesta){
                                            $("#content .row_1 .col_2 .indent #contactForm").html(respuesta);
                                            $("#accordion").accordion("resize");
                                        }
                                    });
                                }); 
                            </script>
                        </div><!-- contactForm  -->
                        
                        <?php
                        }
                        if(!empty($enlaces)){
                            ?>
                            <h3><a href="#"><?php __('enlaces');?></a></h3>
                            <div>
                                <ul>
                                <?php
                                foreach($enlaces as $enlace):
                                	if(!empty($enlace['Paginasenlace']['externo'])){
										$enlace['Paginasenlace']['url']='http://'.$enlace['Paginasenlace']['url'];
										$target=' onclick="this.target=\'_blank\'" ';
									}else{
										
										$enlace['Paginasenlace']['url']=explode(':',$enlace['Paginasenlace']['url'],3);
										$reversed=array_reverse($enlace['Paginasenlace']['url']);
										if(is_numeric($reversed[0])){
											if(isset($enlace['Paginasenlace']['url'][2])){
												$enlace['Paginasenlace']['url']='/'.$enlace['Paginasenlace']['url'][0].'/'.$enlace['Paginasenlace']['url'][1].'/'.$enlace['Paginasenlace']['url'][2].'/idioma:'.Configure::read('Config.language');
											}elseif(isset($enlace['Paginasenlace']['url'][1])){
												$enlace['Paginasenlace']['url']='/paginas/'.$enlace['Paginasenlace']['url'][0].'/'.$enlace['Paginasenlace']['url'][1].'/idioma:'.Configure::read('Config.language');
											}else{
												$enlace['Paginasenlace']['url']='/paginas/detalle/'.$enlace['Paginasenlace']['url'][0].'/idioma:'.Configure::read('Config.language');
											}
										}else{
											$enlace['Paginasenlace']['url']='/'.implode('/',$$enlace['Paginasenlace']['url']);
										}
										$target='';
									}
									if(!empty($enlace['Paginasenlace']['imagen'])){
										$width=200;
										$tipo=explode('.',$enlace['Paginasenlace']['imagen']['path']);
										$tipo=array_reverse($tipo);
										$tipo=$tipo[0];
										if($tipo!='swf'){
										?>
										<li>
											<a href="<?php echo $enlace['Paginasenlace']['url'];?>"<?php echo $target;?>><img src="/thumbs/index/?src=<?php echo $enlace['Paginasenlace']['imagen']['path'];?>&w=<?php echo $width;?>"></a>
											<div class="clear"></div>
										</li>
										<?php
										}else{
										
										?>
										<li style="position:relative;">
												<a style="text-decoration:none; position:absolute; display:block; width:100%; height:100%; top:0px; left:0px;" href="<?php echo $enlace['Paginasenlace']['url'];?>"<?php echo $target;?>>&nbsp;</a>
											<div class="flashswf" style="margin-left:1%"><?php echo $enlace['Paginasenlace']['imagen']['path'];?></div>
										</li>    
										<?php
										}
									}else{
									?>	
										<li>
											<a class="link" href="<?php echo $enlace['Paginasenlace']['url'];?>"<?php echo $target;?>><?php echo $enlace['Paginasenlace']['title'];?></a>
											<div class="clear"></div>
										</li>
									<?php
									}
								endforeach;
                                ?>
                                </ul>
                                <script>
								var doFlashEnlace=function(){
									$('.flashswf').each(function(index){
										var width='98%';
										if($(this).html().substr(0,4)!="<obj"){
											$(this).flash({
												swf: $(this).html()
												,width: width
												,params: {
													bgcolor: "#ffffff"
													,menu: "false"
													,scale: 'noScale'
													,wmode: "opaque"
													,allowfullscreen: "true"
													,allowScriptAccess: "always"
												}
											})
											
										}
										width=$(this).width();
										$(this).flash(
											function() {
												movieH=this.TGetProperty('/', 9);
												movieV=this.TGetProperty('/', 8);
												aspectRatio =  movieH/movieV;
												$(this).height(width*aspectRatio);
											}
										);
									});
								}
								$("#accordion").bind( "accordionchange", function(event, ui) {
									doFlashEnlace();
									$("#accordion").accordion('resize');
								});
								$(document).ready(function() {
								});
								$(window).resize(function() {
									doFlashEnlace();
									$("#accordion").accordion('resize');
								});
								</script>
                            </div>
                            <?php
                        }
                        ?>
                    </div><!-- accordion  -->
                </div><!-- indent  -->
			</div><!-- col_2  -->
			<div class="clear"></div>
		</div><!-- row_1  -->
		<?php
        if(isset($pagina['items'])&&!empty($pagina['items'])){
		?>
		<div class="row_2">
            <div class="itemsContainer bg_gray">
                <div class="indent">
                <?php
                    foreach($pagina['items'] as $item):
                    ?>
                    <a class="divVinculo" href="/paginas/detalle/<?php echo $item['Pagina']['id'].'/idioma:'.Configure::read('Config.language') ;?>">
                        <div class="padding ui-widget-content ui-corner-all p-<?php echo $item['Pagina']['predeterminado'];?>">
                            
                            <h3><?php echo $item['Pagina']['title'];?></h3>
                            <?php 
                            if($item['Pagina']['predeterminado']=='texto'){
                                if(isset($item['Paginasopcional']['idfoto'])){
                                    $size='120';
                                    $crop='C';
                                    ?>
                                    <img src="/thumbs/index/?src=<?php echo $item['Paginastexto']['contenido_imagenes']{$item['Paginasopcional']['idfoto']}['url'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                                    <?php
                                }
        
                                $item['Paginastexto']['resumen']=substr_replace($item['Paginastexto']['resumen'], $leermas, -4, 0);
                                echo $item['Paginastexto']['resumen']
                                ?>
                            <?php
                            }elseif($item['Pagina']['predeterminado']=='imagen'){
                                $size='120';
                                $crop='C';
                                for($i=0;$i<4;$i++){
                                    if(isset($item['Paginasimagen']{$i})){
                                        ?>
                                        <img src="/thumbs/index/?src=<?php echo $item['Paginasimagen']{$i}['imagen']['path'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                                        <?php
                                    }
                                }
                            }elseif($item['Pagina']['predeterminado']=='video'){
                                for($i=0;$i<4;$i++){
                                    if(isset($item['Paginasvideo']{$i})){
                                        ?>
                                        <img src="<?php echo $item['Paginasvideo']{$i}['codigo']['p_img'];?>" />
                                        <?php
                                    }
                                }
                            }elseif($item['Pagina']['predeterminado']=='adjunto'){
                                for($i=0;$i<4;$i++){
                                    if(isset($item['Paginasadjunto']{$i})){
                                        ?>
                                        <div class="adjuntoContainer">
                                        <img src="<?php echo $item['Paginasadjunto']{$i}['adjunto']['icon'];?>" />
                                        <p><?php echo $item['Paginasadjunto']{$i}['title'];?></p>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </a><!-- div_vinculo  -->
                    <?php
                    endforeach;
                ?>
                </div><!-- indent  -->
            </div><!-- itemsContainer  -->
            <?php
            }
            ?>
        </div><!-- row_2  -->
	</div><!-- content  -->
</div><!-- main  -->