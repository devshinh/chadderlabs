<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MenuItem {
		
	 public $first;
	 public $current;
	 public $last;
	 public $index;
	 public $link;
	 public $display;
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

}

/* End of file MenuItem.php */