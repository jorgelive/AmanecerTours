<div id="header">
    <div class="column col_1">
        <img class="logo" src="/img/comun/logo.png" width="265" height="89" />
    </div><!-- col_1 -->
    <div class="column col_2">
        <div class="row_1">


        </div><!-- row_1  -->
        <div class="row_2">
            <?php
            echo $this->element('menu');
            ?>
        </div><!-- row_2  -->
    </div><!-- col_2 -->
    <div class="clear"></div>
</div><!-- header  -->

<div id="flashHeader"></div>
<script type="text/javascript">
    $(document).ready(function(){
        var stageH = 400/1900*$('#flashHeader').width();
        $('#logo').css({'top':stageH-10})
        $('#flashHeader').height(stageH);
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


            <a><?php echo $pagina['Pagina']['title'];?></a>


            <?php
            if($pagina['Pagina']['texto']='si'){
                ?>
                <div class="mceContentBody"><?php echo $pagina['Paginastexto']['contenido'];?></div>
                <div class="clear"></div>
                <?php
                unset($pagina['Paginastexto']);
            }
            ?>

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
                }elseif(in_array($key,array('video','adjunto','contacto'))&&$valor=='si'){
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
</div><!-- content  -->
