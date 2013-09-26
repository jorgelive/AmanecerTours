<?php
if (!isset($ok)){
	echo $form->create('Paginasformulario',array('action'=>'form'));
	echo $form->input('Paginasformulario.destinatario',array('type'=>'hidden'));
	echo $form->input('Paginasformulario.cco',array('type'=>'hidden'));
	echo $form->input('Paginasformulario.name',array('label'=>__('contacto_nombre',true)));
	echo $form->input('Paginasformulario.email',array('label'=>__('contacto_email',true)));
	echo $form->input('Paginasformulario.title',array('label'=>__('contacto_titulo',true)));
	echo $form->input('Paginasformulario.contenido',array('label'=>__('contacto_detalle',true),'rows'=>5,'cols'=>20));
	echo $form->submit(__('contacto_enviar',true));
	echo $form->end();
	?>
    <script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$('#PaginasformularioFormForm').ajaxForm({
			target: '#contactForm'
		});
		$("#accordion").accordion("resize");
	});
	</script>
    <?php
}
?>