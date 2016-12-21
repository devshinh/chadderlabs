<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_item {
	 public $id;	
	 public $first;
	 public $current;
	 public $last;
	 public $index;
	 public $link;
	 public $title;
	 public $page_id;
	 public $sub_menu;
	 
    public function __construct(){
        $sub_menu = array();
    }	 
	 
	 public function render_css_classes()
	 {
	 	$classes = $this->first ? 'first ': '';
		$classes .= $this->last ? 'last ': '';
		$classes .= $this->current ? 'current': '';
		return trim($classes);				
	 }
	 	 
	 // class methods 
	 
	 public static function parse_menu($menu_data) {
		$menu_items = array();
		foreach($menu_data as $menu_frontend_item)
		{
			$menu_items[] = self::instantiate_from_jstree_json($menu_frontend_item);
		}
		return $menu_items;
	 }
	 	 
	 public static function instantiate_from_database($menu_record)
	 {
	 	$object = new self;
		$object->id = $menu_record->id;
		$object->title = $menu_record->title;
		$object->page_id = $menu_record->page_id;
		$object->enabled = $menu_record->hidden == '0';
		return $object;
	 }

	 public static function build_menu_item_array($menu_item_records,$model_menu_item)
	 {
	 	$menu_items = array();
		foreach($menu_item_records as $menu_item_record) {
			$menu_item = self::instantiate_from_database($menu_item_record);		
			$sub_menu_records = $model_menu_item->get_all_root_menu_items_by_menu_id($menu_item_record->menu_group_id,$menu_item_record->id);
			$menu_item->sub_menu = self::build_menu_item_array($sub_menu_records,$model_menu_item);
			$menu_items[] = $menu_item;		
		}
		return $menu_items; 	
	 }
	 
	 private static function instantiate_from_jstree_json($menu_frontend_item)
	 {
		$object = new self;
		/*
		echo "<pre>";
		var_dump($menu_frontend_item->attr);
		echo "</pre>";
		die();
		*/
		//var_dump($menu_frontend_item);
		//die();
		$object->title = $menu_frontend_item->data;
		$object->id = str_replace("menu_item_","",$menu_frontend_item->attr->id); 
		if(isset($menu_frontend_item->children)) {
			foreach($menu_frontend_item->children as $menu_child_item)
			{
				//echo 'child found <br>';
				$object->sub_menu[] = self::instantiate_from_jstree_json($menu_child_item);
			}
	 	} 	
		return $object;		
	 }
}

/* End of file MenuItem.php */
