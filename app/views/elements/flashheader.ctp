<div id="flashHeader"></div>
<script type="text/javascript">
    $(document).ready(function(){
        var stageH = 400/1440*$('#flashHeader').width();
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
        var stageH = 400/1440*$('#flashHeader').width();
        $('#logo').css({'top':stageH-10})
        $('#flashHeader').height(stageH);
    });
</script>