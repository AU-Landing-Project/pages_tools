<?php

	function pages_tools_route_pages_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!empty($return_value) && is_array($return_value)){
			$page = elgg_extract("segments", $result);
			
			switch($page[0]){
				case "export";
					if(isset($page[1])){
						$result = false;
						set_input("page_guid", $page[1]);
						
						include(dirname(dirname(__FILE__)) . "/pages/export.php");
					}
					break;
			}
		}
		
		return $result;
	}
	
	function pages_tools_entity_menu_hook($hook, $type, $return_value, $params){
		$result = $return_value;
		
		if(!empty($params) && is_array($params)){
			$entity = elgg_extract("entity", $params);
			
			if(!empty($entity) && (elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page"))){
				elgg_load_css("lightbox");
				elgg_load_js("lightbox");
				
				$result[] = ElggMenuItem::factory(array(
					"name" => "export",
					"text" => elgg_view_icon("download"),
					"title" => elgg_echo("export"),
					"href" => "pages/export/" . $entity->getGUID(),
					"class" => "pages-tools-lightbox",
					"priority" => 500
				));
			}
		}
		
		return $result;
	}