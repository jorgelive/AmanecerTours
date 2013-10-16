	<div id="content">
		<div class="row_1">
			<div class="column col_1">
				
			</div><!--col_1 -->
			<div class="column col_2">
                
			</div><!--col_2 -->
			<div class="column col_3">
                
			</div><!--col_3 -->
			<div class="clear"></div>
		</div><!--row_1 -->
		<div class="row_2">
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
                                        $height='120';
										$width='180';
                                        $crop='C';
                                        ?>
                                        <img src="/thumbs/index/?src=<?php echo $mostrarinicio['Paginastexto']['contenido_imagenes']{$mostrarinicio['Paginasopcional']['idfoto']}['url'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
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
                
            </div><!--col_2 -->
			<div class="clear"></div> 
        </div><!--row_2 -->
    </div><!-- content  -->
