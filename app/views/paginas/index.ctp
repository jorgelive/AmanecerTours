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

            <div class="textRow">
                <?php
                $textColWidth=100;
                if(isset($pagina['Paginasopcional'])&&!empty($pagina['Paginasopcional']['duracion'])){
                    $textColWidth=90;
                ?>
                <div style="width:10%"class="duracionCol column">

                    <div class="duracion">
                        <div class="duracionNumero"><?php echo $pagina['Paginasopcional']['duracion'];?></div>
                        <div class="duracionDias"><?php echo __('dias');?></div>
                        <div class="clear">&nbsp;</div>
                    </div>
                    <div class="clear"></div>

                </div>
                <?php
                }
                ?>
                <div style="width:<?php echo $textColWidth; ?>%" class="textCol column">

                    <?php
                    if(!isset($pagina['Pagina']['isStart'])){
                        ?>
                        <h1><?php echo $pagina['Pagina']['title'];?></h1>
                        <?php
                    }
                    ?>


                    <?php
                    if($pagina['Pagina']['texto']='si'){
                        ?>
                        <div class="mceContentBody texto"><?php echo $pagina['Paginastexto']['contenido'];?></div>
                        <div class="clear"></div>
                        <?php
                        unset($pagina['Paginastexto']);
                    }
                    ?>
                </div>

            </div>
            <div class="clear"></div>

            <?php
            $tipos=Configure::read('Default.tipos');
            $nroPredeterminado = 0;
            $tabPredeterminado = array();
            foreach($pagina['Pagina'] as $key => $valor):
                if($key=='multiple'&&$valor=='si'){
                    $tabPredeterminado['multiple']=$nroPredeterminado;
                    foreach ($pagina['Paginasmultiple'] as $subkey => $subvalor):
                       $tabs[$key.$subkey]=$pagina['Paginasmultiple']{$subkey};
                       $nroPredeterminado++;
                   endforeach;
                }elseif(in_array($key,array('video','adjunto','contacto'))&&($valor=='si'/*||$valor==1*/)){
                    $tabs[$key]=$pagina['Paginas'.$key];
                    $tabPredeterminado{$key}=$nroPredeterminado;
                    $nroPredeterminado++;
                }
            endforeach;
            if(isset($tabs)&&!empty($tabs)){

            ?>
            <div id="tabs">
                <ul>
                    <?php
                    foreach ($tabs as $key => $tab):
                        if(substr($key, 0, 8)=="multiple") {
                        ?>
                            <li><a href="#tab-<?php echo $key;?>"><?php echo $tab['title'];?></a></li>
                        <?php
                        }else{
                        ?>
                            <li><a href="#tab-<?php echo $key;?>"><?php echo $tipos{$key};?></a></li>
                        <?php
                        }
                    endforeach;
                    ?>
                </ul>

                <?php
                //print_r($tabs);
                foreach ($tabs as $key => $tab):
                    ?>
                    <div id="tab-<?php echo $key;?>">
                        <?php
                        if(substr($key, 0, 8)=="multiple") {
                            ?>
                            <div class="mceContentBody"><?php echo $tab['contenido'];?></div>
                            <div class="clear"></div>
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
                $(function() {
                    $("#tabs").tabs().tabs("option", "active", <?php echo $tabPredeterminado{$pagina['Pagina']['predeterminado']};?> );
                });
            </script>
            <?php
            }
            ?>
            &nbsp;
        </div><!--col_1 -->
        <div class="column col_2">

        </div><!--col_2 -->
        <div class="clear"></div>
    </div><!--row_2 -->
    <div class="row_3">
        <div class="column col_1">
            <?php
            if(!empty($mostrarInicios)){
                foreach($mostrarInicios as $mostrarinicio):
                    ?>
                    <a class="divVinculo" href="/paginas/index/<?php echo $mostrarinicio['Pagina']['id'].'/idioma:'.Configure::read('Config.language') ;?>">
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
    </div><!--row_3 -->

</div><!-- content  -->
