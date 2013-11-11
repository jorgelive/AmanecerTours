<div id="imageHeader">
    <div class="headerContent">
    <?php
        $height='400';
        $width='1440';
        $crop='C';
        $i=1;
            foreach($cabeceras as $cabecera):
                $url='#';
                if(!empty($cabecera['Paginascabecera']['url'])){
                    if($cabecera['Paginascabecera']['externo']==1){
                        $url='http://'.$cabecera['Paginascabecera']['url'];
                    }else{
                        $cabecera['Paginascabecera']['url']=explode(':',$cabecera['Paginascabecera']['url'],3);
                        $reversed=array_reverse($cabecera['Paginascabecera']['url']);
                        if(is_numeric($reversed[0])){
                            if(isset($cabecera['Paginascabecera']['url'][2])){
                                $url='/'.$cabecera['Paginascabecera']['url'][0].'/'.$cabecera['Paginascabecera']['url'][1].'/'.$cabecera['Paginascabecera']['url'][2].'/idioma:'.Configure::read('Config.language');
                            }elseif(isset($cabecera['Paginascabecera']['url'][1])){
                                $url='/paginas/'.$cabecera['Paginascabecera']['url'][0].'/'.$cabecera['Paginascabecera']['url'][1].'/idioma:'.Configure::read('Config.language');
                            }else{
                                $url='/paginas/index/'.$cabecera['Paginascabecera']['url'][0].'/idioma:'.Configure::read('Config.language');
                            }
                        }else{
                            $url='/'.implode('/',$cabecera['Paginascabecera']['url']);
                        }
                    }

                }
            ?>
                 <a href="<?php echo $url;?>" style="display:none;">
                    <div class="headerItem">
                        <img class="ui-corner-all" src="/thumbs/index/?src=<?php echo $cabecera['Paginascabecera']['imagen']['path'];?>&h=<?php echo $height;?>&w=<?php echo $width;?>&zc=<?php echo $crop;?>" />
                        <?php
                            if(!empty($cabecera['Paginascabecera']['title'])&&!empty($cabecera['Paginascabecera']['texto']))
                            {
                            ?>
                                <div class="overlay back">&nbsp;</div>
                                <div class="overlay front">
                                    <?php
                                    if(!empty($cabecera['Paginascabecera']['title']))
                                    {
                                        echo '<h2>'.$cabecera['Paginascabecera']['title'].'</h2>';
                                    }
                                    if(!empty($cabecera['Paginascabecera']['texto']))
                                    {
                                        echo $cabecera['Paginascabecera']['texto'];
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>
                    </div>
                </a>
            <?php
            $i++;
            endforeach;
            ?>
    </div>
    <div class="headerPaging ui-corner-all">
        <?php
        $i=1;
        foreach($cabeceras as $cabecera):
            if($i!=1){
                echo "&nbsp;|&nbsp;";
            }
            ?>

            <a class="divVinculo" rel="<?php echo $i;?>" href="#"><?php echo $i;?></a>

            <?php
            $i++;
        endforeach;
        ?>
    </div>

</div><!--imageHeader -->

<script type="text/javascript">
    $(document).ready(function(){

        $(".headerPaging").show();

        var rotateHeader = function(){
            if($("div#imageHeader div.headerContent a").length<actual){
                actual=1;
            }
            $("div#imageHeader div.headerPaging a.current").removeClass("current");
            $("div#imageHeader div.headerPaging a[rel="+actual+"]").addClass('current');
            $("div#imageHeader div.headerContent a.current").fadeOut(1000);
            $("div#imageHeader div.headerContent a.current").removeClass("current");
            $("div#imageHeader div.headerContent a:eq(" + (actual-1) + ")").fadeIn(1000);
            $("div#imageHeader div.headerContent a:eq(" + (actual-1) + ")").addClass('current');
            actual++;
        };
        var rotateHeaderSwitch = function(){
            playHeader = setInterval(function(){
                rotateHeader();
            }, 5000); // este es el valor que define la velocidad (7 segundos)
        };
        var playHeader;
        var actual=1;
        rotateHeader();
        rotateHeaderSwitch();
        $("div#imageHeader div.headerContent a").hover(function() {clearInterval(playHeader);}, function() {rotateHeaderSwitch();});
        $("div#imageHeader div.headerPaging a").click(function() {
            clearInterval(playHeader);
            actual=$(this).attr('rel');
            rotateHeader();
            return false;
        });
        $("div#imageHeader div.headerPaging").hover(function() {clearInterval(playHeader);}, function() {rotateHeaderSwitch();});


        var stageH = 400/1440*$('div#imageHeader').width();
        $('div#imageHeader').height(stageH);
        $('div#imageHeader div.headerContent').height(stageH);
        $('div#imageHeader div.headerContent a').height(stageH);
        $('div#imageHeader div.headerContent a div.headerItem').height(stageH);
        $('div#imageHeader div.headerContent a div.headerItem img').height(stageH);

    });
    $(window).resize(function(){
        var stageH = 400/1440*$('div#imageHeader').width();
        $('div#imageHeader').height(stageH);
        $('div#imageHeader div.headerContent').height(stageH);
        $('div#imageHeader div.headerContent a').height(stageH);
        $('div#imageHeader div.headerContent a div.headerItem').height(stageH);
        $('div#imageHeader div.headerContent a div.headerItem img').height(stageH);
    });
</script>