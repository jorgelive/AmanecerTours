<div id="header">
    <div class="column col_1">
        <img class="logo" src="/img/comun/logo.png" width="265" height="89" />
    </div><!-- col_1 -->
    <div class="column col_2">
        <div class="row_1">
            <a href="http://mail.amanecertours.com/"><img src="/img/comun/webmail.png" alt="<?php echo __('correo');?>" /></a>
            <a href="/paginas/administracion"><img src="/img/comun/admin.png" alt="<?php echo __('administracion');?>" /></a>

            <span>&nbsp;&nbsp;&nbsp;</span>
            <?php
            foreach(Configure::read('Empresa.languageList') as $key => $value):
                ?>
                <a href="/paginas/index/idioma:<?php echo $key;?>/"><img src="/img/comun/<?php echo $key;?>.png" alt="<?php echo $value;?>" /></a>
                <?php
            endforeach;
            ?>
            <span>&nbsp;&nbsp;&nbsp;</span>
            <a href="https://www.facebook.com/pages/Amanecer-tours/1469015949991135"><img src="/img/comun/facebook.png" alt="Facebook" /></a>
          </div><!-- row_1  -->
        <div class="row_2">
            <?php
            echo $this->element('menuPrincipal',$menuPrincipal);
            ?>
        </div><!-- row_2  -->
    </div><!-- col_2 -->
    <div class="clear"></div>
</div><!-- header  -->
