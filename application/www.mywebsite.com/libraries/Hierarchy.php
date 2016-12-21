<?php if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Hierarchy {

	public function __construct() {

		$this->CI =& get_instance();
  }

  /*
   * Method:      sort
   * Description: Sort array according to parent id
   * Parameters:  array   $aHierarchy List of elements to be sorted (reference)
   *              string  $primary    Primary key
   */
  public function sort( &$aHierarchy, $primary ) {

    // if valid...
    if (isset( $aHierarchy, $primary )) {

      // initialize variables
      $aParent = array();

      // if not array...
      foreach ($aHierarchy as $row) { $aParent[$row->nParentID][] = $row; }

      // reset/load array
      $aHierarchy = array();
      $this->load( $aHierarchy, $aParent, $primary );

      // set disabled flag
      $aDisabled = array();
      foreach ($aHierarchy as &$row) {

        // set flag
        if (empty( $row->dActive ) || !empty( $aDisabled[$row->nParentID] )) {
          $aDisabled[$row->{$primary}] = true;
          $row->parentDisabled         = true;
        }
      }
    }
  }

  /*
   * Method:      load
   * Description: Recursively load hierarchy (support method of sort())
   * Parameters:  array  $aHierarchy  Data array (reference)
   *              array  $aSorted     Parent array (reference)
   *              string $primary     Primary key
   *              int    $parentID    Parent id
   *              int    $level       Level
   */
  public function load( &$aHierarchy, &$aParent, $primary, $parentID = 0, $level = 0 ) {

    // if parent...
    if (!empty( $aParent ) && !empty( $aParent[$parentID] )) {

      // increment level
      $level++;

      // loop parent array...
      while ($row = array_shift( $aParent[$parentID] )) {

        // append to sorted array
        $row->nLevel = $level - 1;
        $aHierarchy[$row->{$primary}] = $row;

        // if parent...
        if (!empty( $aParent[$row->{$primary}] )) {
          $aHierarchy[$row->{$primary}]->bHasChildren = true;
          $this->load( $aHierarchy, $aParent, $primary, $row->{$primary}, $level );
        }
      }
    }
  }
}
?>
