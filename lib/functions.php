<?php

	function pages_tools_is_valid_page(ElggObject $entity){
		$result = false;
		
		if(!empty($entity)){
			if(elgg_instanceof($entity, "object", "page_top") || elgg_instanceof($entity, "object", "page")){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function pages_tools_get_ordered_children(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			$options = array(
				"type" => "object",
				"subtype" => "page",
				"limit" => false,
				"metadata_name_value_pairs" => array("parent_guid" => $page->getGUID())
			);
			
			if($children = elgg_get_entities_from_metadata($options)){
				$result = array();
				
				foreach($children as $child){
					$order = $child->order;
					if(empty($order)){
						$order = $child->time_created;
					}
					
					while(array_key_exists($order, $result)){
						$order++;
					}
					
					$result[$order] = $child;
				}
				
				ksort($result);
			}
		}
		
		return $result;
	}
	
	/**
	 * Render the index for every page below the provided page
	 * 
	 * @param ElggObject $page
	 * @return boolean
	 */
	function pages_tools_render_index(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			if($children = pages_tools_get_ordered_children($page)){
				$result = "";
				
				foreach($children as $child){
					$result .= "<li>" . elgg_view("output/url", array("text" => $child->title, "href" => "#page_" . $child->getGUID(), "title" => $child->title));
					
					if($child_index = pages_tools_render_index($child)){
						$result .= "<ul>" . $child_index . "</ul>";
					}
					
					$result .= "</li>";
				}
			}
		}
		
		return $result;
	}
	
	function pages_tools_render_childpages(ElggObject $page){
		$result = false;
		
		if(!empty($page) && pages_tools_is_valid_page($page)){
			if($children = pages_tools_get_ordered_children($page)){
				$result = "";
				
				foreach($children as $child){
					$result .= "<h3>" . elgg_view("output/url", array("text" => $child->title, "href" => false, "name" => "page_" . $child->getgUID())) . "</h3>";
					$result .= elgg_view("output/longtext", array("value" => $child->description));
					$result .= "<p style='page-break-after:always;'></p>";
					
					if($child_pages = pages_tools_render_childpages($child)){
						$result .= $child_pages;
					}
				}
			}
		}
		
		return $result;
	}