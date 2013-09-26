<?php
class ExtHelper extends AppHelper {
	
	var $viewportTypes=array('north'=>'Panel','south'=>'Panel','west'=>'Panel','east'=>'Panel','center'=>'TabPanel');
	
	var $viewport = array('north'=>array(
									'id'=>'north'
									,'region'=>'north'
									,'height'=>50
									,'colapseMode'=>'mini'
									,'collapsible' =>true
									,'autoscroll'=>true)
						  ,'west'=>array(
									'id'=>'west'
									,'region'=>'west'
									,'width'=>200
									,'minWidth'=>150
									,'maxWidth'=>600
									,'collapsible'=>true
									,'autoScroll'=>true
									,'split'=>true)
						  ,'south'=>array(
									'id'=>'south'
									,'region'=>'south'
									,'height'=>100
									,'collapsible'=>true
									,'autoScroll'=>true)
						  ,'east'=>array(
									'id'=>'east'
									,'region'=>'east'
									,'width'=>200
									,'collapsible'=>true
									,'autoScroll'=>true)
						  ,'center'=>array(
									'id'=>'center'
									,'region'=>'center'
									,'bodyStyle'=>'background-color:#f0f0f0;'
									,'id'=>'center'
									,'deferredRender'=>false
									,'activeTab'=>0
									,'enableTabScroll'=>true
									,'autoLayout'=>true)
						  );
	
	function viewPort($items=array()){
		foreach($items as $key => $item):
			$centralPresente=false;
			if ($item||is_array($item)){
				if ($key=='center'){$centralPresente=true;}
				$itemsToExt{$key}=array_merge($this->viewport[$key],$item);
			}
		endforeach;
		if ($centralPresente==false){$itemsToExt['center']=$this->viewport['center'];}
		
		foreach ($itemsToExt as $key=>$viewport):
			if (is_array($viewport)){
				$extitem{$key}='new Ext.'.$this->viewportTypes{$key}.'({itemId:\''.$key.'\'';
				foreach($viewport as $name=>$item):
					if (is_bool($item)&&$item==true){$item='true';}
					elseif (is_bool($item)&&$item==false){$item='false';}
					elseif (is_numeric($item)){}
					elseif (is_array($item)){$item='['.implode(',',$item).']';}
					else{$item='\''.$item.'\'';}
					$extitem{$key}.=','.$name.':'.$item;	
				endforeach;
				$extitem{$key}.='})';
			}
		endforeach;
		
		?>
        var viewPort = new Ext.Viewport({
			id:'viewPort'
			,layout:'border'
			,border:false
			,items:[<?php echo implode(',',$extitem) ;?>
        	]
        });
        <?php
	}
}
?>