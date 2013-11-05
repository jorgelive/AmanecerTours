	<div id="menu">
			<script type="text/javascript">
            $(function(){
                $('.fg-button').hover(
                    function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus');},
                    function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default');}
                );
            });
            </script>
            <?php
            foreach(Configure::read('Menu') as $var => $settings):
                if($settings['tipo']=='boton'){
                    echo '<a class="fg-button ui-widget ui-state-default" href="'.$settings['url'].'/idioma:'.Configure::read('Config.language').'">'.$settings['texto'].'</a>';
                }elseif(array_key_exists('menu'.$var,$this->viewVars)){
                    foreach ($this->viewVars['menu'.$var] as $item):
                        if(!empty($item['children'])){
                        	if($var=='Pagina'&&empty($item[$var]['predeterminado'])){
								echo '<a id="menu'.$item[$var]['id'].'" href="#">'.$item[$var][$settings['textField']].'</a>';
							}else{
								echo '<a id="menu'.$item[$var]['id'].'" href="/'.strtolower(Inflector::pluralize($var)).'/'.$settings['accion'].'/'.$item[$var]['id'].'/idioma:'.Configure::read('Config.language').'">'.$item[$var][$settings['textField']].'</a>';
							}
							echo '<div id="contentMenu'.$item[$var]['id'].'" class="hidden">';
							echo $tree->generate($item['children'],array('alias'=>$settings['textField'],'element' => 'menu'.strtolower($var)));
							echo '</div>';
							?>
                            <script type="text/javascript">    
							$(function(){
								$('#<?php echo 'menu'.$item[$var]['id'];?>').menu({
									content: $('#<?php echo 'contentMenu'.$item[$var]['id'];?>').html()
									,backLink: false
									,crumbDefaultText: '<?php __('escoja');?>'
									,topLinkText: '<?php echo $item[$var][$settings['textField']];?>'
								});
							});
							</script>
                            <?php
							
						}else{
							echo '<a class="fg-button ui-widget ui-state-default" href="/'.strtolower(Inflector::pluralize($var)).'/'.$settings['accion'].'/'.$item[$var]['id'].'/idioma:'.Configure::read('Config.language').'">'.$item[$var][$settings['textField']].'</a>';
						}
                    endforeach;
                }
            endforeach;
            ?>
	</div><!-- menu -->