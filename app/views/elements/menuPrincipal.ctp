
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
            foreach($menu['items'] as $grupo):
                if($grupo{'settings'}{'tipo'}=='extractTree'){
                    $modelo=Inflector::singularize($grupo{'settings'}{'controlador'});
                    foreach ($grupo['contenido'] as $item):
                        $currentClass='';
                        if(isset($item[$modelo]['ancestor'])&&array_key_exists($grupo{'settings'}{'controlador'},$menu['current'])&&$menu['current']{$grupo{'settings'}{'controlador'}}==$item[$modelo]['ancestor']){
                                $currentClass='ui-state-active';
                        }

                        if(!empty($item['children'])){
                            if(empty($item[$modelo]['predeterminado'])){
                                echo '<a class="'.$currentClass.'" id="menu'.$item[$modelo]['id'].'" href="#">'.$item[$modelo][$grupo{'settings'}['textField']].'</a>';
                            }else{
                                echo '<a class="'.$currentClass.'" id="menu'.$item[$modelo]['id'].'" href="/'.strtolower($grupo{'settings'}{'controlador'}).'/'.$grupo{'settings'}['accion'].'/'.$item[$modelo]['id'].'/idioma:'.Configure::read('Config.language').'">'.$item[$modelo][$grupo{'settings'}['textField']].'</a>';
                            }
                            echo '<div id="contentMenu'.$item[Inflector::singularize($grupo{'settings'}{'controlador'})]['id'].'" class="hidden">';
                            echo $tree->generate($item['children'],array('alias'=>$grupo{'settings'}['textField'],'element' => 'menu'.strtolower($modelo)));
                            echo '</div>';
                            ?>
                            <script type="text/javascript">
                                $(function(){
                                    $('#<?php echo 'menu'.$item[$modelo]['id'];?>').menu({
                                        content: $('#<?php echo 'contentMenu'.$item[$modelo]['id'];?>').html()
                                        ,backLink: false
                                        ,crumbDefaultText: '<?php __('escoja');?>'
                                        ,topLinkText: '<?php echo $item[$modelo][$grupo{'settings'}['textField']];?>'
                                    });
                                });
                            </script>
                        <?php

                        }else{
                            echo '<a class="'.$currentClass.' fg-button ui-widget ui-state-default" href="/'.strtolower($grupo{'settings'}{'controlador'}).'/'.$grupo{'settings'}{'accion'}.'/'.$item[$modelo]['id'].'/idioma:'.Configure::read('Config.language').'">'.$item[$modelo][$grupo{'settings'}['textField']].'</a>';
                        }
                    endforeach;
                }elseif($grupo{'settings'}['tipo']=='boton'){
                    echo '<a class="fg-button ui-widget ui-state-default" href="'.$settings['url'].'/idioma:'.Configure::read('Config.language').'">'.$settings['texto'].'</a>';
                }
            endforeach;
        ?>
	</div><!-- menu -->