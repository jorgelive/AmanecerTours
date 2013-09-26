<div id="main">
    <div id="header">
        <div class="column col_1">
			<div class="ind">
                <div id="cse-search-form" style="width: 100%;">cargando</div>
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
		</div><!-- col_1  -->
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
                $('#header .col_2 .ind a').addClass('ui-widget ui-corner-bottom ui-state-default');
				$('#header .col_2 .ind a').hover(
                    function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus');},
                    function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default');}
                );
            });
            </script>
        </div><!-- col_2  -->
		<div class="clear"></div>
	</div><!-- header  -->
	<div id="logo">
        <img src="/img/comun/logo.png" />
    </div><!-- logo  -->
	<?php
    echo $this->element('menu');
	?>
    <script type="text/javascript">
    $(function (){
        $('a.ajaxLink').click(function() {
			var url = this.href;
			$.ajax({
				url: url
				,dataType: 'json'
				,success: function(respuesta) {
					if (respuesta.success){
						if(respuesta.hasOwnProperty('data')){
							if(respuesta.data.Paginasnoticia){
								var data=respuesta.data.Paginasnoticia;
								var noticia = $('<div class="popContenedor"/>');
            					noticia.append($('<p><?php __('titulo');?>: <strong>'+data.title+'</strong></p>'));
								noticia.append($('<p><?php __('fecha');?>: <strong>'+data.fecha+'</strong></p>'));
								noticia.append($('<div class="contenido" id="C_'+data.id+'"><div class="texto">'+data.contenido+'</div></div>'));
								if(data.imagen){
									noticia.find('#C_'+data.id).append($('<img src="/thumbs/index/?src='+data.imagen.path+'&w=200" />'))
									noticia.find('#C_'+data.id).find('.texto').css({
										width:270
										,display:'block'
										,float:'left'
									})
								}
								noticia.find('#C_'+data.id).append($('<div class="clear" />'))
								noticia.dialog({
									title: data.title
									,modal: true
									,width: 550
									,height: 550
									,resizable: false
								});
							}
						}
					}else{
						this.error();
					}
				}
				,error: function() {
					alert("An error has been detected");
				}
			});
            return false;
        });
		<?php if(isset($actual)&&is_numeric($actual)){?>
			$('a#boton<?php echo $actual;?>').click();
		<?php }?>
		
    });
    </script>
    <div id="content">
        <div id="cse" style="width:100%;"></div>
        <div class="row_1">
            <div class="column col_1">
				<div class="indent">
                    <?php
						if(!empty($noticias)){
							foreach($noticias as $noticia):
							?>
							<a id="boton<?php echo $noticia['Paginasnoticia']['id'];?>" class="divVinculo ajaxLink" href="/paginasnoticias/detalle/<?php echo $noticia['Paginasnoticia']['id'];?>/idioma:<?php echo Configure::read('Config.language');?>">
                            <div class="padding ui-widget-content ui-corner-all">
								<h3><?php echo $noticia['Paginasnoticia']['title'];?></h3>
                                <p><?php __('fecha');?>: <?php echo $noticia['Paginasnoticia']['fecha'];?></p>
								
                                <span class="contenido">
								<?php 
									if(isset($noticia['Paginasnoticia']['imagen'])&&!empty($noticia['Paginasnoticia']['imagen'])){
										$size='110';
										$crop='C';
										?>
										<img src="/thumbs/index/?src=<?php echo $noticia['Paginasnoticia']['imagen']['path'];?>&h=<?php echo $size;?>&w=<?php echo $size;?>&zc=<?php echo $crop;?>" />
										<?php
									}
									echo $noticia['Paginasnoticia']['contenido_resumen'];
								?>
                                </span>
							</div>
                            </a>
							<?php
						endforeach;
					}
					?>
                    &nbsp;
            	</div><!-- indent -->
			</div><!-- col_1 -->
            <div class="column col_2">
				<div class="indent">
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
            	</div><!-- indent -->
			</div><!-- col_2 -->
			<div class="clear"></div>
		</div><!-- row_1 -->
	</div><!-- content -->
</div><!-- main  -->