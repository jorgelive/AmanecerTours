<script type="text/javascript">
<?php echo $this->element('extBlankImage');?>
Ext.onReady(function() {
 
	Ext.QuickTips.init();
	<?php echo $this->element('extBarra');?>
	tree = new Ext.tree.TreePanel({ //global
		autoScroll:true
		,animate:true
		,enableDD:true
		,containerScroll: true
		,rootVisible: false
		,enableDD:false
		,loader: new Ext.tree.TreeLoader({
			dataUrl:'<?php echo $html->url('/acos/getnodes/') ?>'
		})
	});	
	
	var root = new Ext.tree.AsyncTreeNode({
		text:'Secciones',
		draggable:false,
		id:'root'
	});
	
	tree.setRootNode(root);
	root.expand();
	
	<?php
	$items=array(
				'north'=>array('title'=>$title_for_layout.' - '.Configure::read('Empresa.nombre'),'items'=>array('barra'))
				,'west'=>array('title'=>'Secciones','items'=>array('tree'))
			);
	echo $ext->viewport($items);
	?>

	
});
</script>