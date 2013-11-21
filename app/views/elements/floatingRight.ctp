<div id="floatingRight" class="desplegado">
    <div class="left">
        <a href="#"class="boton ui-corner-top fg-button ui-widget ui-state-default">
            <?php echo __('ocultar');?>&nbsp;<?php echo __('informacion_util');?>
        </a>
    </div>
    <div class="right">
        <div class="contenedor">
            <div class="background">
                &nbsp;
            </div>
            <div class="content">

                <?php
                foreach($enlaces as $enlace):
                    $url='#';
                    if(!empty($enlace['Paginasenlace']['url'])){
                        if($enlace['Paginasenlace']['externo']==1){
                            if(substr($enlace['Paginasenlace']['url'], 0, 4)=='http'){
                                $url=$enlace['Paginasenlace']['url'];
                            }else{
                                $url='http://'.$enlace['Paginasenlace']['url'];
                            }
                        }else{
                            $enlace['Paginasenlace']['url']=explode(':',$enlace['Paginasenlace']['url'],3);
                            $reversed=array_reverse($enlace['Paginasenlace']['url']);
                            if(is_numeric($reversed[0])){
                                if(isset($enlace['Paginasenlace']['url'][2])){
                                    $url='/'.$enlace['Paginasenlace']['url'][0].'/'.$enlace['Paginasenlace']['url'][1].'/'.$enlace['Paginasenlace']['url'][2].'/idioma:'.Configure::read('Config.language');
                                }elseif(isset($enlace['Paginasenlace']['url'][1])){
                                    $url='/paginas/'.$enlace['Paginasenlace']['url'][0].'/'.$enlace['Paginasenlace']['url'][1].'/idioma:'.Configure::read('Config.language');
                                }else{
                                    $url='/paginas/index/'.$enlace['Paginasenlace']['url'][0].'/idioma:'.Configure::read('Config.language');
                                }
                            }else{
                                $url='/'.implode('/',$enlace['Paginasenlace']['url']);
                            }
                        }

                    }
                    $crop='C';
                    if($enlace['Paginasenlace']['imagen']['width']>$enlace['Paginasenlace']['imagen']['height']){
                        $height='110';
                        $width='190';

                    }else{
                        $height='180';
                        $width='130';
                    }
                    ?>
                    <a class="item" href="<?php echo $url;?>">
                        <img class="ui-corner-all" src="/thumbs/index/?src=<?php echo $enlace['Paginasenlace']['imagen']['path'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
                        <span class="ui-corner-all texto"><?php echo $enlace['Paginasenlace']['title'];?></span>
                    </a>
                    <?php
                endforeach;

                ?>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function() {
        var changeHeight = function () {
            $('div#floatingRight').css({'height' : $(window).height()});
        }

        var mostrarOcultar = function (){
            if($('div#floatingRight').hasClass( "desplegado" )){
                $('div#floatingRight').removeClass('desplegado')
                $('div#floatingRight').addClass('colapsado');
                $('div#floatingRight').animate({right: "-210px"}, 1000, 'easeOutBounce');
                $('div#floatingRight div.left a.boton').html('<?php echo __('mostrar');?>&nbsp;<?php echo __('informacion_util');?>');

            }else{
                $('div#floatingRight').removeClass('colapsado');
                $('div#floatingRight').addClass('desplegado');
                $('div#floatingRight').animate({right: "0"}, 1000, 'easeOutBounce');
                $('div#floatingRight div.left a.boton').html('<?php echo __('ocultar');?>&nbsp;<?php echo __('informacion_util');?>');
            }
        }

        $("div#floatingRight div.left a.boton").click(function() {
            mostrarOcultar();
            clearTimeout(playMostrarOcultar);
            return false;
        });

        var playMostrarOcultar = setTimeout(function(){
            mostrarOcultar();
        }, 7000);

        changeHeight();
        playMostrarOcultar;
        $(window).resize(changeHeight);
    });
</script>