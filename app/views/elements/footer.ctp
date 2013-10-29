<div id="footer">

    <div class="row_1 w100"></div>
    <div class="row_2 w100 padding">
        <?php
        $i=0;
        foreach($menuInferior as $item):
            if($i>0){
                echo '&nbsp;&nbsp;|&nbsp;&nbsp;';

            }
            if($item['Pagina']['publicado']==1&&$item['Pagina']['vigencia']=='ok'&&$item['Pagina']['notempty']==1){
                echo '<a href="/paginas/index/'.$item['Pagina']['id'].'/idioma:'.Configure::read('Config.language').'" class="ui-corner-all">'.$item['Pagina']['title'].'</a>';
            }

            $i++;
        endforeach;
        ?>
     </div>
    <div class="row_3 w100 padding">
        <h2><?php echo Configure::read('Empresa.nombre');?></h2>
        <p><?php echo Configure::read('Footer.linea1');?></p>
        <p><?php echo Configure::read('Footer.linea2');?></p>
    </div>

    <br class="clear" />
</div><!-- footer -->

