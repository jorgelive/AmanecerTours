<div id="<?php echo (isset($pagina['Pagina']['isStart'])?'start':'nostart');?>">
    <?php
    echo $this->element('header',$menu);
    echo $this->element('flashheader');
    ?>
    <div id="content">
        <div class="row_1 w100">
            <div class="column col_1">

            </div><!--col_1 -->
            <div class="column col_2">

            </div><!--col_2 -->
            <div class="column col_3">

            </div><!--col_3 -->
            <div class="clear"></div>
        </div><!--row_1 -->

        <div class="row_2 w100">
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

                    </div><!--duracionCol -->
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
                    </div><!--textCol -->
                    <div class="clear">&nbsp;</div>
                </div><!--textRow -->


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
                                    ?>
                                    <ul id="<?php echo $key.'-'.$pagina['Pagina']['id'];?>">
                                    <?php
                                    foreach($tab as $adjunto):
                                        ?>
                                        <li>
                                            <a href="<?php echo $adjunto['adjunto']['path'];?>">
                                                <img src="<?php echo $adjunto['adjunto']['icon'];?>" />
                                                <p><?php echo $adjunto['title'];?></p>
                                            </a>
                                        </li>
                                    <?php
                                    endforeach;
                                    ?>
                                    </ul>
                                <?php
                                }elseif($key=='contacto'){
                                ?>
                                    <div id="contactForm">
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $.ajax({
                                                    type: "POST"
                                                    ,url: "/paginasformularios/form"
                                                    ,data: "idioma=<?php echo Configure::read('Config.language');?>&destinatario=<?php echo Security::cipher($pagina['Paginascontacto']['destinatario'], Configure::read('Security.cipherSeed'));?>&cco=<?php echo Security::cipher($pagina['Paginascontacto']['cco'], Configure::read('Security.cipherSeed'));?>&title=<?php echo __('acerca de').': '.$pagina['Pagina']['title'];?>"
                                                    ,success: function(respuesta){
                                                        $("div#content div.row_2 div.column div#tabs.ui-tabs div#tab-contacto.ui-tabs-panel div#contactForm").html(respuesta);
                                                    }
                                                });
                                            });
                                        </script>

                                    </div><!-- contactForm  -->

                                <?php
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

                            var anchoTotal=$("div#content div.row_2 div.col_1 div#tabs.ui-tabs ul").width();

                            var cantidadTab= $("div#content div.row_2 div.col_1 div#tabs.ui-tabs ul.ui-tabs-nav li a" ).size();

                            $("div#content div.row_2 div.col_1 div#tabs.ui-tabs ul.ui-tabs-nav li a" ).width((anchoTotal-29*cantidadTab)/cantidadTab);
                        });
                    </script>
                <?php
                }
                ?>
                &nbsp;
            </div><!--col_1 -->
            <div class="column col_2">
                <?php
                if(!empty($mostrarRelateds)&&!isset($pagina['Pagina']['isStart'])){
                    $height='70';
                    $width='70';
                    $crop='C';
                    foreach($mostrarRelateds as $mostrarRelated):
                        ?>
                        <a class="divVinculo" href="/paginas/index/<?php echo $mostrarRelated['Pagina']['id'].'/idioma:'.Configure::read('Config.language') ;?>">
                            <div class="ui-widget-content ui-corner-all">
                                <img src="/thumbs/index/?src=<?php echo $mostrarRelated['Paginasopcional']['imagenpath'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
                                <span class="texto"><?php echo $mostrarRelated['Pagina']['title'];?></span>
                                <div class="clear"></div>
                            </div>
                        </a>
                    <?php
                    endforeach;
                }
                ?>
                <?php
                if(isset($pagina['Pagina']['imagen'])&&$pagina['Pagina']['imagen']=='si'&&!empty($pagina['Paginasimagen'])){
                    ?>
                    <div class="imagenes">
                    <?php
                    foreach($pagina['Paginasimagen'] as $imagen):
                        $height='150';
                        $width='220';
                        $crop='C';
                        ?>

                            <div class="item">
                                <img src="/thumbs/index/?src=<?php echo $imagen['imagen']['path'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
                                <div class="clear"></div>
                            </div>
                        <?php
                    endforeach;
                    ?>
                    </div>
                    <?php
                }
                ?>
                &nbsp;

            </div><!--col_2 -->
            <div class="clear"></div>
        </div><!--row_2 -->
        <div class="row_3 w100">
            <div class="column col_1">
                <?php
                if(!empty($mostrarInicios)&&isset($pagina['Pagina']['isStart'])){
                    $height='450';
                    $width='682';
                    $crop='C';
                    ?>
                    <div class="rotatorView">
                        <div class="rotatorWindow" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px;">
                            <div class="rotatorContent">
                                <?php
                                foreach($mostrarInicios as $mostrarinicio):
                                    ?>
                                    <a href="/paginas/index/<?php echo $mostrarinicio['Pagina']['id'].'/idioma:'.Configure::read('Config.language') ;?>">
                                        <div class="ui-widget-content ui-corner-all rotatorItem" style="width:<?php echo $width-14;?>px;height:<?php echo $height-14;?>px;">
                                            <?php
                                            if(isset($mostrarinicio['Paginastexto']['contenido'])&&!empty($mostrarinicio['Paginastexto']['contenido'])){
                                                ?>
                                                <img src="/thumbs/index/?src=<?php echo $mostrarinicio['Paginasopcional']['imagenpath'];?>&h=<?php echo $height-14;?>&w=<?php echo $width-14;?>&zc=<?php echo $crop;?>" />
                                                <div class="back">&nbsp;</div>
                                                <div class="front">
                                                    <h2><?php echo $mostrarinicio['Pagina']['title'];?></h2>
                                                    <?php
                                                    echo substr_replace($mostrarinicio['Paginastexto']['resumen'],'...',-4,0);
                                                    ?>
                                                </div>
                                                <?php
                                            }else{
                                                if($mostrarinicio['Pagina']['predeterminado']=='video'){
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
                                            }
                                            ?>
                                        </div>
                                    </a>
                                <?php
                                endforeach;
                                ?>
                            </div>
                        </div>
                        <div class="rotatorPaging">
                            <?php
                            $i=1;
                            $height='70';
                            $width='70';
                            $crop='C';
                            foreach($mostrarInicios as $mostrarInicio):
                                ?>
                                <a class="divVinculo" rel="<?php echo $i;?>" href="#">
                                    <div class="ui-widget-content ui-corner-all">
                                        <img src="/thumbs/index/?src=<?php echo $mostrarInicio['Paginasopcional']['imagenpath'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
                                        <span class="texto"><?php echo $mostrarInicio['Pagina']['title'];?></span>
                                        <div class="clear"></div>
                                    </div>
                                </a>
                                <?php
                                $i++;
                            endforeach;
                            ?>
                        </div>
                    </div><!--rotatorView -->
                    <script type='text/javascript'>
                        //<![CDATA[
                        $(document).ready(function() {
                            $(".rotatorPaging").show();
                            $(".rotatorPaging a:first").addClass("active");
                            var itemHeight = $(".rotatorWindow").height();
                            var itemSum = $(".rotatorContent div").size();
                            var contentHeight = itemHeight * itemSum;
                            $(".rotatorContent").css({'height' : contentHeight});
                            rotate = function(){
                                var triggerID = $active.attr("rel") - 1;
                                var contentPosition = triggerID * itemHeight;
                                $(".rotatorPaging a").removeClass('active');
                                $active.addClass('active');
                                $(".rotatorContent").animate({top: -contentPosition}, 500 );
                            };
                            rotateSwitch = function(){
                                play = setInterval(function(){
                                    $active = $('.rotatorPaging a.active').next();
                                    if ( $active.length === 0) {$active = $('.rotatorPaging a:first');}
                                    rotate();
                                }, 7000); // este es el valor que define la velocidad (7 segundos)
                            };
                            rotateSwitch();
                            $(".rotatorContent a").hover(function() {clearInterval(play);}, function() {rotateSwitch();});
                            $(".rotatorPaging a").click(function() {$active = $(this);clearInterval(play);rotate();rotateSwitch();return false;});
                        });
                        //]]>
                    </script>
                <?php
                }
                ?>
                &nbsp;
            </div><!--col_1 -->
            <div class="column col_2">

            </div><!--col_2 -->
            <div class="clear"></div>
        </div><!--row_3 -->
    </div><!-- content  -->
    <?php
    echo $this->element('footer');
    echo $this->element('sql_dump');
    ?>
</div><!-- start nostart  -->


