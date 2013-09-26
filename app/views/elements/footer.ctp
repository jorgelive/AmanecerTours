<div id="footer_main">
    <div id="footer">
        <div class="indent">
            <?php echo Configure::read('Empresa.nombre');?> &copy; <?php echo date('Y');?>	  
            <br class="clear" />
        </div><!-- indent -->
    </div><!-- footer -->
</div><!-- footer_main -->
<div id="bottom-bar">
    <ul>
        <li title="<?php __('inicio');?>"><a href="/"><img src="/img/icons/home.png" alt="" /></a></li>
    </ul>
    <span class="jx-separator-left"></span>
    <ul>        
        <li title="<?php __('redes sociales');?>"><a href="#"><img src="/img/icons/globe-network.png" alt="<?php __('redes');?>" /></a>
            <ul>
                <li><a href="http://www.facebook.com/pages/Andean-Ways/177941451039"><img src="/img/icons/balloon-facebook-left.png" alt="" />&nbsp;&nbsp;&nbsp;Facebook</a></li>
                <li><a href="https://twitter.com/andeanways"><img src="/img/icons/balloon-twitter-left.png" alt="" />&nbsp;&nbsp;&nbsp;Twitter</a></li>
                <li><a href="http://pe.linkedin.com/in/andeanways"><img src="/img/icons/card-address.png" alt="" />&nbsp;&nbsp;&nbsp;Linkedin</a></li>
            </ul>
        </li>
    </ul>
    <span class="jx-separator-left"></span>        
    <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com/pages/AndeanWays/175815079117812&amp;layout=standard&amp;show_faces=false&amp;width=500&amp;action=like&amp;font=segoe+ui&amp;colorscheme=light&amp;height=40" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:40px;" allowTransparency="true"></iframe>
    <ul class="jx-bar-button-right">
        <li title="<?php __('feeds');?>"><a href="#"><img src="/img/icons/feed.png" alt="" /></a>
        </li>
    </ul>
    <span class="jx-separator-right"></span>
</div><!-- bottom-bar -->
<script type="text/javascript">
    $(document).ready(function() {
		$("#bottom-bar").jixedbar({
			showToolbarText:"<?php __('mostrar barra');?>"
			,hideToolbarText:"<?php __('ocultar barra');?>"
		});
		$('body').find('li:last-child').addClass("last-item"); 
    }); 
</script>