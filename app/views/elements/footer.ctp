<div id="footer">

    <div class="row_1 w100"></div>
    <div class="row_2 w100 padding">
        <?php
        foreach($menuinferior as $item):
            if($item['publicado']==1&&$item['vigencia']=='ok'&&$item['']){
            print_r($menuInferior);
            }
        endforeach;
        ?>
     </div>
    <div class="row_3 w100 padding">
        <?php echo Configure::read('Empresa.nombre');?> &copy; <?php echo date('Y');?>
    </div>

    <br class="clear" />
</div><!-- footer -->

