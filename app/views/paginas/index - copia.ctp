<div id="main">
	<div style="visibility:hidden;" id="logo">
		<h1><?php echo Configure::read('Empresa.nombre').' '.__('slogan',true)  ;?></h1>
		<?php echo $this->Session->flash(); ?>
	</div><!-- logo  -->
	
	<div id="header">
		<div class="column col_1">
			<div class="ind">
				<div id="cse-search-form" style="width: 100%;"><?php __('cargando');?></div>
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
		</div><!-- col_1 -->
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
					$('#header .col_2 .ind a').addClass('ui-widget ui-state-default ui-corner-bottom');
					$('#header .col_2 .ind a').hover(
						function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus');},
						function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default');}
					);
				});
			</script>
		</div><!-- col_2 -->
		<div class="clear"></div>
	</div><!-- header  -->
	<?php
    echo $this->element('menu');
    ?>
    <div id="flashHeader"></div>
    <script type="text/javascript">
		$(document).ready(function(){
			var stageH = 400/1900*$('#flashHeader').width();
			$('#logo').css({'top':stageH-10})
			$('#flashHeader').height(stageH);
			$('#flashHeader').css({'background-color':'#ccc'});
			$('#flashHeader').flash({
				swf: '/flash/preview.swf?t=' + Date.parse(new Date())
				,width: '100%'
				,height: '100%'
				,flashvars: {
					pathToFiles: ""
					,xmlPath: "/paginascabeceras/listarxml"
				}
				,params: {
					bgcolor: "#ffffff"
					,menu: "false"
					,scale: 'noScale'
					,wmode: "opaque"
					,allowfullscreen: "true"
					,allowScriptAccess: "always"
				}
				,expressInstaller: '/flash/expressInstall.swf'
			});
		});
		$(window).resize(function(){
			var stageH = 400/1900*$('#flashHeader').width();
			$('#logo').css({'top':stageH-10})
			$('#flashHeader').height(stageH);
		});
	</script>
	<div id="content">
		<div id="cse" style="width:100%;"></div>
		<div class="row_1">
			<div class="column col_1">
				<?php
                    if(!empty($mostrarInicios)){
                        foreach($mostrarInicios as $mostrarinicio):
                        ?>
                        <a class="divVinculo" href="/paginas/detalle/<?php echo $mostrarinicio['Pagina']['id'].'/idioma:'.Configure::read('Config.language') ;?>">
                            <div class="padding ui-widget-content ui-corner-all p-<?php echo $mostrarinicio['Pagina']['predeterminado'];?>">
                                <h3><?php echo $mostrarinicio['Pagina']['title'];?></h3>
                                <?php 
                                if($mostrarinicio['Pagina']['predeterminado']=='texto'){
                                    if(isset($mostrarinicio['Paginasopcional']['idfoto'])){
                                        $size='120';
                                        $crop='C';
                                        ?>
                                        <img src="/thumbs/index/?src=<?php echo $mostrarinicio['Paginastexto']['contenido_imagenes']{$mostrarinicio['Paginasopcional']['idfoto']}['url'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                                    <?php
                                    }
                                    echo substr_replace($mostrarinicio['Paginastexto']['resumen'],'...',-4,0);
                                    ?>
                                <?php
                                }elseif($mostrarinicio['Pagina']['predeterminado']=='imagen'){
                                    $size='120';
                                    $crop='C';
                                    for($i=0;$i<4;$i++){
                                        if(isset($mostrarinicio['Paginasimagen']{$i})){
                                            ?>
                                            <img src="/thumbs/index/?src=<?php echo $mostrarinicio['Paginasimagen']{$i}['imagen']['path'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                                            <?php
                                        }
                                    }
                                }elseif($mostrarinicio['Pagina']['predeterminado']=='video'){
                                    for($i=0;$i<4;$i++){
                                        if(isset($mostrarinicio['Paginasvideo']{$i})){
                                            ?>
                                            <img src="<?php echo $mostrarinicio['Paginasvideo']{$i}['codigo']['p_img'];?>" />
                                            <?php
                                        }
                                    }
                                }elseif($mostrarinicio['Pagina']['predeterminado']=='adjunto'){
                                    for($i=0;$i<4;$i++){
                                        if(isset($mostrarinicio['Paginasadjunto']{$i})){
                                            ?>
                                            <div class="adjuntoContainer">
                                                <img src="<?php echo $mostrarinicio['Paginasadjunto']{$i}['adjunto']['icon'];?>" />
                                                <p><?php echo $mostrarinicio['Paginasadjunto']{$i}['title'];?></p>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </a>
                    <?php
                    endforeach;
                }
                ?>
                &nbsp;
			</div><!--col_1 -->
			<div class="column col_2">
                <div class="padding ui-widget-header ui-corner-top">
                    <h3><?php echo __('ofertas');?></h3>
                </div>
                <div class="padding ui-widget-content ui-corner-bottom">
                    <ul>
                        <?php
                        if(!empty($ofertas)){
                            $i=0;
                            foreach($ofertas as $oferta):
                            ?>
                                <li class="ui-widget-content ui-corner-all">
                                    <a href="/paginas/detalle/<?php echo $oferta['Pagina']['id'].'/idioma:'.Configure::read('Config.language');?>">
                                        <h3><?php echo $oferta['Pagina']['title'];?></h3>
                                        <?php
                                        if($oferta['Pagina']['texto']=='si'){
                                            if(isset($oferta['Paginasopcional']['idfoto'])){
                                                $size='100';
                                                $crop='C';
                                                if($i%2==0){$class='left';}else{$class='right';}
                                                ?>
                                                    <img class="<?php echo $class;?>" src="/thumbs/index/?src=<?php echo $oferta['Paginastexto']['contenido_imagenes']{$oferta['Paginasopcional']['idfoto']}['url'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                                                <?php
                                                $i++;
                                            }
                                            echo $oferta['Paginastexto']['resumen'];
                                        }
                                        ?>
                                        <div class="clear"></div>
                                        <ul class="clear ofertas">
                                        <?php 
                                        foreach($oferta['Paginasoferta'] as $item):
                                        ?>
                                            <li class="oferta">
                                                <h4 class="titulo">
                                                	<?php echo $item['title'];?>
                                                </h4>
                                                <div class="clear notas">
                                                    <h5><?php echo __('notas');?>:</h5>
                                                    <div class="texto mceContentBody">
                                                        <?php echo $item['notas'];?>
                                                    </div>
                                                </div>
                                                <div class="clear condiciones">
                                                    <h5><?php echo __('condiciones');?>:</h5>
                                                    <div class="texto mceContentBody">
                                                        <?php echo $item['condiciones'];?>
                                                    </div>
                                                </div>
                                                <h5 class="clear precio">
													<?php echo __('desde');?> : <?php echo $item['precio'];?>
                                                </h5>
                                                <div class="clear"></div>
                                            </li>
                                        <?php
                                        endforeach;
                                        ?>
                                        </ul>
                                    </a>
                                    
                                    <script type="text/javascript">
                                        $('#content .row_1 .col_2 .ui-widget-content ul li a').hover(
                                            function(){
                                                $(this).find('ul').fadeIn();
                                            }
                                            ,function(){
                                                $(this).find('ul').fadeOut('slow',function() {
                                                    $(this).css({'display':'none'})
                                                });
                                            }
                                        );
                                        
                                    </script>
                                    
                                    <div class="clear"></div>
                                </li>
                            <?php
                            endforeach;
                        }
                        ?>
                    </ul>
                </div>
			</div><!--col_2 -->
			<div class="column col_3">
                <div class="padding ui-widget-content ui-corner-all">
                    <ul>
                        <?php
                        if(!empty($enlaces)){
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
                        }
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
                    $(document).ready(function() {
                        doFlashEnlace();
                    });
                    $(window).resize(function() {
                        doFlashEnlace();
                    });
                    </script>
                </div>
			</div><!--col_3 -->
			<div class="clear"></div>
		</div><!--row_1 -->
		<div class="row_2 bg_gray">
			<div class="column col_1 bg_col">
				<div class="indent">
					<?php
					if(!empty($noticias)){
					?>
						<table>
							<tbody>
								<tr>
								<?php
								foreach($noticias as $noticia):
								?>
									<td>
										<h2><span><?php echo $noticia['Paginasnoticia']['fecha'];?></span> <?php echo $noticia['Paginasnoticia']['title'];?></h2>
										<?php
										if(isset($noticia['Paginasnoticia']['imagen']['path'])&&!empty($noticia['Paginasnoticia']['imagen']['path'])){
											$size=80;
											$crop='C';
											$imagen='<img src="/thumbs/index/?src='.$noticia['Paginasnoticia']['imagen']['path'].'&h='.$size.'&w='.$size.'&zc='.$crop.'" />';
											
											echo $imagen;
										}
										$leermas=' <a class="link" href="/paginasnoticias/index/'.$noticia['Paginasnoticia']['id'].'/idioma:'.Configure::read('Config.language').'">'.__('ampliar noticia',true).'</a>';
										echo substr_replace($noticia['Paginasnoticia']['contenido_resumen'], $leermas, -4, 0);
										?>
                  
                  					</td>
               					 <?php
								endforeach;
								?>
								</tr>
							</tbody>
						</table>
						<script type="text/javascript">
                            var numeroNoticias=$('#content .row_2 .col_1 .indent table tr td').length;
                            var anchoNoticia=Math.round(($('#content .row_2 .col_1 .indent')[0].clientWidth/2)+2);
                            var anchoTabla= numeroNoticias*anchoNoticia;
                            $('#content .row_2 .col_1 .indent table').css('width',anchoTabla);
                            $('#content .row_2 .col_1 .indent table tr td').css('width',anchoNoticia);
                            
                            var movernoticia = function(ancho,noticia,total){
                                var delay = 25000;
                                $("#content .row_2 .col_1 .indent table").animate({ 
                                  left: "-"+((noticia*ancho)+2)+"px"
                                }, 1500 );
                                noticia++;
                                if(noticia==total-1){noticia=0;}
                                setTimeout("movernoticia("+ancho+","+noticia+","+total+");",delay);
                            }
                            $(document).ready(function() {
                                movernoticia(anchoNoticia,0,numeroNoticias);
                            });
                        </script>
					<?php
                    }
                    ?>
				</div><!--indent -->
			</div><!--col_1 -->
            <div class="column col_2">
                <div class="indent" style="visibility:hidden; height:120px;">
                    <?php
                    if(!empty($testimonios)){
                    ?>
                        <ul>
                            <?php
                            foreach($testimonios as $testimonio):
                            ?>
                            <li>
                                <div class="clear">
                                    <?php
                                    if(isset($testimonio['Paginastestimonio']['imagen']['path'])){
                                        $size='120';
                                        $crop='C';
                                        ?>
                                        <img src="/thumbs/index/?src=<?php echo $testimonio['Paginastestimonio']['imagen']['path'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
                      
                                        <?php
                                    }
                                    $leermas=' <a class="link" href="/paginastestimonios/index/'.$testimonio['Paginastestimonio']['id'].'/idioma:'.Configure::read('Config.language').'">'.__('leer mas',true).'</a>';
                                    echo substr_replace($testimonio['Paginastestimonio']['contenido_resumen'],$leermas,-4,0);
                                    ?>
                                </div>
                                <div class="clear aright">
                                    <p>
                                        <strong><?php echo $testimonio['Paginastestimonio']['name'];?></strong><br />
                                        <?php echo __('nacionalidad').': '.$testimonio['Paginastestimonio']['nacionalidad'];?>
                                    </p>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <?php
                            endforeach;
                            ?>
                        </ul>
                        <script type="text/javascript">
                            var movertestimonio = function(altura,testimonio,total){
                                var delay = 30000;
                                $("#content .row_2 .col_2 .indent ul").animate({ 
                                  top: "-"+testimonio*altura+"px"
                                }, 1500 );
                                testimonio++;
                                if(testimonio==total){testimonio=0;}
                                setTimeout("movertestimonio("+altura+","+testimonio+","+total+");",delay);
                            }
                            $(window).load(function() {
                                var commentMaxHeight=0;
                                var numeroTestimonios=$('#content .row_2 .col_2 .indent ul li').length;
                                $('#content .row_2 .col_2 .indent ul li').each(
                                    function(nroItem,valotItem){
                                        if(commentMaxHeight<valotItem.clientHeight){
                                            commentMaxHeight=valotItem.clientHeight;
    
                                        }
                                    }
                                );
                                $('#content .row_2 .col_2 .indent ul li').css('height',commentMaxHeight)
                                $('#content .row_2 .col_2 .indent').css({'visibility':'visible','height':commentMaxHeight});
                                movertestimonio(commentMaxHeight,0,numeroTestimonios);
                            });
                        </script>
                    <?php
                    }
                    ?>
                </div><!--indent -->
            </div><!--col_2 -->
			<div class="clear"></div> 
        </div><!--row_2 -->
    </div><!-- content  -->
</div><!-- main  -->