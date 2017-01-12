<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_model extends HotCMS_Model {
  function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->config('target/target', TRUE);
    $this->tables = $this->config->item('tables', 'target');
  }

  /**
   * Count how many rows in database matched the filters.
   * @param  array $filters for where statements
   * @return int   0 to number of resulting rows
   */
  function target_count($filters) {
    if (is_array($filters)) {
// haven't yet implemented keyword/search function.
    }
    return $this->db->count_all_results($this->tables["target"]);
  }
  
  /**
   * Delete a target in target tables by row id.
   * @param  int $target_id
   * @return int numeric result representing delete success or error
   */
  function target_delete($target_id) {
    if ($this->get_quizzes($target_id) != array()) {
      return 1;
    } elseif ($this->get_labs($target_id) != array()) {
      return 2;
    } elseif ($this->db->delete($this->tables["target_organization_type"], array("target_id" => $target_id)) === FALSE) {
      return 3;
    } elseif ($this->db->delete($this->tables["target_organization_category"], array("target_id" => $target_id)) === FALSE) {
      return 4;
    } elseif ($this->db->delete($this->tables["target_job_title"], array("target_id" => $target_id)) === FALSE) {
      return 5;
    } elseif ($this->db->delete($this->tables["target_organization"], array("target_id" => $target_id)) === FALSE) {
      return 6;
    } elseif ($this->db->delete($this->tables["target_store"], array("target_id" => $target_id)) === FALSE) {
      return 7;
    } elseif ($this->db->delete($this->tables["target"], array("id" => $target_id)) === FALSE) {
      return 8;
    }
    return 0;
  }

  /**
   * Insert a new target into all target tables
   * @param  array $new_target post data from user
   * @return mix   the new targe row id or FALSE if failed when inserting into one of the table 
   */
  function target_insert($new_target) {
    $now = time();
    $this->db->set("name", $new_target["name"]);
    if (array_key_exists("description", $new_target)) {
      $this->db->set("description",  $new_target["description"]);
    }
    $this->db->set("site_id", $new_target["site_id"]);
    $this->db->set("update_timestamp", $now);
    $this->db->set("create_timestamp", $now);
    $this->db->set("editor_id", (int) ($this->session->userdata('user_id')));
    $this->db->set("creator_id", (int) ($this->session->userdata('user_id')));
    if ($this->db->insert($this->tables["target"])) {
      $new_target_id = $this->db->insert_id();
      if (array_key_exists("job_titles", $new_target) && ( !empty($new_target["job_titles"]))) {
        foreach ($new_target["job_titles"] as $job_title_id) {
          $this->db->set("target_id", $new_target_id);
          $this->db->set("job_title_id", $job_title_id);
          $this->db->insert($this->tables["target_job_title"]);
        }
      }
      if (array_key_exists("organizations", $new_target) && ( !empty($new_target["organizations"]))) {
        $organizations = explode(",", $new_target["organizations"]);
        foreach ($organizations as $organization_id) {
          $this->db->set("target_id", $new_target_id);
          $this->db->set("organization_id", $organization_id);
          $this->db->insert($this->tables["target_organization"]);
        }
      }
      if (array_key_exists("categories", $new_target) && ( !empty($new_target["categories"]))) {
        foreach ($new_target["categories"] as $category_id) {
          $this->db->set("target_id", $new_target_id);
          $this->db->set("organization_category_id", $category_id);
          $this->db->insert($this->tables["target_organization_category"]);
        }
      }
      if (array_key_exists("types", $new_target) && ( !empty($new_target["types"]))) {
        foreach ($new_target["types"] as $type_id) {
          $this->db->set("target_id", $new_target_id);
          $this->db->set("organization_type_id", $type_id);
          $this->db->insert($this->tables["target_organization_type"]);
        }
      }
      if (array_key_exists("stores", $new_target) && ( !empty($new_target["stores"]))) {
        $stores = explode(",", $new_target["stores"]);
        foreach ($stores as $store_id) {
          $this->db->set("target_id", $new_target_id);
          $this->db->set("store_id", $store_id);
          $this->db->insert($this->tables["target_store"]);
        }
      }
      return $new_target_id;
    }
    return FALSE;
  }

  /**
   * Get number of rows from target table
   * @param  array $filters  for where or sorting statements
   * @param  int   $page_num limit number
   * @param  int   $per_page offset number
   * @return array rows in target table
   */
  function target_list($filters = FALSE, $page_num = 1, $per_page = 0) {
    $site_id = $this->session->userdata("siteID");
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    $per_page = (int) $per_page;
    $page_num = (int) $page_num;
    if ($page_num < 1) {
      $page_num = 1;
    }
    $offset = ($page_num - 1) * $per_page;
    if ($offset < 0) {
      $offset = 0;
    }
    if ($per_page > 0) {
      $this->db->limit($per_page, $offset);
    }
    if (is_array($filters)) {
      if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
        $sort_direction = 'DESC';
      }
      else {
        $sort_direction = 'ASC';
      }
      switch (strtolower($filters["sort_by"])) {
        case "site":
          $this->db->order_by("site_id", $sort_direction);
          break;
        case "description":
          $this->db->order_by("description", $sort_direction);
          break;
        case "update_timestamp":
          $this->db->order_by("update_timestamp", $sort_direction);
          break;
        case "name":
        default :
          $this->db->order_by("name", $sort_direction);
      }
    }
    else {
      $this->db->order_by('name', 'ASC');
    }
    $results = $this->db->get_where($this->tables["target"])->result();
    foreach ($results as &$result) {
      $result->site = $this->db->get_where($this->tables["site"], array("id" => $result->site_id))->row()->name;
    }
    return $results;
  }

  /**
   * Get a single target by id.
   * May or may not includes details.
   * @param  int    $target_id  row id in target table
   * @param  bool   $no_details determine details are needed
   * @return object a target
   */
  function target_load($target_id, $no_details = TRUE) {
    $target = $this->db->get_where($this->tables["target"], array("id" => $target_id))->row();
    if ($no_details) {
      return $target;
    }
    $target_job_titles = $this->db->get_where($this->tables["target_job_title"], array("target_id" => $target_id))->result();
    if ( !empty($target_job_titles)) {
      $target->job_titles = $target_job_titles;
    }
    $target_categories = $this->db->get_where($this->tables["target_organization_category"], array("target_id" => $target_id))->result();
    if ( !empty($target_categories)) {
      $target->categories = $target_categories;
    }
    $target_organizations = $this->db->get_where($this->tables["target_organization"], array("target_id" => $target_id))->result();
    if ( !empty($target_organizations)) {
      $target->organizations = $target_organizations;
    }
    $target_typies = $this->db->get_where($this->tables["target_organization_type"], array("target_id" => $target_id))->result();
    if ( !empty($target_typies)) {
      $target->types = $target_typies;
    }
    $target_stores = $this->db->get_where($this->tables["target_store"], array("target_id" => $target_id))->result();
    if ( !empty($target_stores)) {
      $target->stores = $target_stores;
    }
    return $target;
  }

  /**
   * Update a target by row id in target table.
   * First delete all details, and then inset posted data.
   * @param  int   $id   row id in target table
   * @param  array $data posted data to update
   * @return bool  TRUE if updated all detailes; otherwise, FALSE
   */
  function target_update($id, $data) {
    $this->db->set("name", $data["name"]);
    if (array_key_exists("description", $data)) {
      $this->db->set("description",  $data["description"]);
    }
    $this->db->set("site_id", $data["site_id"]);
    $this->db->set("update_timestamp", time());
    $this->db->set("editor_id", (int) ($this->session->userdata('user_id')));
    $this->db->where("id", $id);
    $updated = ($this->db->update($this->tables["target"]) !== FALSE);
    if ($updated) {
      if ($this->db->delete($this->tables["target_job_title"], array("target_id" => $id)) === FALSE) {
        $updated = FALSE;
      } elseif  (array_key_exists("job_titles", $data) && ( !empty($data["job_titles"]))) {
        foreach ($data["job_titles"] as $job_title_id) {
          $this->db->set("target_id", $id);
          $this->db->set("job_title_id", $job_title_id);
          if ($this->db->insert($this->tables["target_job_title"]) === FALSE) {
            $updated = FALSE;
            break;
          }
        }
      }
    }
    if ($updated) {
      if ($this->db->delete($this->tables["target_organization"], array("target_id" => $id)) === FALSE) {
        $updated = FALSE;
      } elseif (array_key_exists("organizations", $data) && ( !empty($data["organizations"]))) {
        $organizations = explode(",", $data["organizations"]);
        foreach ($organizations as $organization_id) {
          $this->db->set("target_id", $id);
          $this->db->set("organization_id", $organization_id);
          if ($this->db->insert($this->tables["target_organization"]) === FALSE) {
            $updated = FALSE;
            break;
          }
        }
      }
    }
    if ($updated) {
      if ($this->db->delete($this->tables["target_organization_category"], array("target_id" => $id)) === FALSE) {
        $updated = FALSE;
      } elseif (array_key_exists("categories", $data) && ( !empty($data["categories"]))) {
        foreach ($data["categories"] as $category_id) {
          $this->db->set("target_id", $id);
          $this->db->set("organization_category_id", $category_id);
          if ($this->db->insert($this->tables["target_organization_category"]) === FALSE) {
            $updated = FALSE;
            break;
          }
        }
      }
    }
    if ($updated) {
      if ($this->db->delete($this->tables["target_organization_type"], array("target_id" => $id)) === FALSE) {
        $updated = FALSE;
      } elseif (array_key_exists("types", $data) && ( !empty($data["types"]))) {
        foreach ($data["types"] as $type_id) {
          $this->db->set("target_id", $id);
          $this->db->set("organization_type_id", $type_id);
          if ($this->db->insert($this->tables["target_organization_type"]) === FALSE) {
            $updated = FALSE;
            break;
          }
        }
      }
    }
    if ($updated) {
      if ($this->db->delete($this->tables["target_store"], array("target_id" => $id)) === FALSE) {
        $updated = FALSE;
      } elseif (array_key_exists("stores", $data) && ( !empty($data["stores"]))) {
        $stores = explode(",", $data["stores"]);
        foreach ($stores as $store_id) {
          $this->db->set("target_id", $id);
          $this->db->set("store_id", $store_id);
          if ($this->db->insert($this->tables["target_store"]) === FALSE) {
            $updated = FALSE;
            break;
          }
        }
      }
    }
    return $updated;
  }

  /**
   * Get organization cateories in an array of row id as key and name as value
   * @return array all organization categories
   */
  function get_category_options() {
    $categories = $this->db->order_by("name", "asc")->get($this->tables["organization_category"])->result();
    $category_options = array();
    foreach ($categories as $category) {
      $category_options[$category->id] = $category->name;
    }
    return $category_options;
  }

  /**
   * Get user job titles in an array of row id as key and name as value
   * @return array all user job titles
   */
  function get_job_title_options() {
    $job_titles = $this->db->order_by("name", "asc")->get($this->tables["job_title"])->result();
    $job_title_options = array();
    foreach ($job_titles as $job_title) {
      $job_title_options[$job_title->id] = $job_title->name;
    }
    return $job_title_options;
  }

  /**
   * Get rows in organization/retailer table with matching categories and types
   * @param  array $categories row ids in organization/retailer category table
   * @param  array $types      row ids in organization type table
   * @param  bool  $and        weather matching both filters or one of both if both filters are selected
   * @param  int   $org_id     row id in retailer/organization table
   * @return array rows of organizations/retailers
   */
  function get_organizations($categories = FALSE, $types = FALSE, $and = TRUE, $org_id = 0) {
    $organizations = FALSE;
    if (is_array($categories) && ( !empty($categories))) {
      $organizations = $this->get_organizations_by_category($categories, $org_id);
    }
    if (is_array($types) && ( !empty($types))) {
      $organizations2 = $this->get_organizations_by_type($types, $org_id);
      if ( ! empty($organizations2)) {
        if (empty($organizations)) {
          $organizations = $organizations2;
        } elseif ($and) {
          foreach ($organizations as $key => $organization) {
            if ( !in_array($organization, $organizations2)) {
              unset($organizations[$key]);
            }
          }
        } else {
          foreach ($organizations2 as $organization) {
            if ( !in_array($organization, $organizations)) {
              $organizations[] = $organization;
            }
          }
        }
      }
    }
    if (empty($categories) && empty($types) && empty($organizations)) {
      $organizations = $this->db->order_by("name", "asc")->get($this->tables["organization"])->result();
    }
    return $organizations;
  }

  /**
   * Get rows in organization/retailer table by matching categories
   * @param  array $categories row ids in organization/retailer category table
   * @param  int   $org_id     row id in retailer/organization table
   * @return array rows of organizations/retailers
   */
  function get_organizations_by_category($categories = FALSE, $org_id = 0) {
    if ($org_id > 0) {
      $this->db->where("o.id", $org_id);
    }
    return $this->db->select("o.*")->distinct()->from($this->tables["organization"]." o")->join($this->tables["organization_in_category"]." c", "c.retailer_id = o.id")->where_in("c.category_id", $categories)->order_by("o.name", "asc")->get()->result();
  }

  /**
   * Get rows in organization/retailer table by matching types
   * @param  array $types  row ids in organization type table
   * @param  int   $org_id row id in retailer/organization table
   * @return array rows of organizations/retailers
   */
  function get_organizations_by_type($types = FALSE, $org_id = 0) {
    if ($org_id > 0) {
      $this->db->where("o.id", $org_id);
    }
    return $this->db->select("o.*")->distinct()->from($this->tables["organization"]." o")->join("organization_in_type c", "c.organization_id = o.id")->where_in("c.type_id", $types)->order_by("o.name", "asc")->get()->result();
  }

  /**
   * Get rows in quiz table by target id.
   * Quizzes may or may not be published/actived
   * @param  int   $target_id row id in target table
   * @param  bool  $active    determine limiting quiz rows to published ones or not
   * @return array rows of matching quizzes
   */
  function get_quizzes($target_id, $active = TRUE) {
    if ($active) {
      $this->db->where("status", "1");
    }
    return $this->db->get_where($this->tables["quiz"], array("target_id" => $target_id))->result();
  }

  /**
   * Get rows in lab/training table by target id.
   * Quizzes may or may not be published/actived
   * @param  int   $target_id row id in target table
   * @param  bool  $active    determine limiting lab/training rows to published ones or not
   * @return array rows of matching labs/trainings
   */
  function get_labs($target_id, $active = TRUE) {
    if ($active) {
      $this->db->where("status", "1");
    }
    return $this->db->get_where($this->tables["lab"], array("target_id" => $target_id))->result();
  }

  /**
   * Get sites/subdomains in an array of row id as key and name as value.
   * @return array all sites
   */
  function get_sites() {
    return $this->db->get_where($this->tables["site"], array("active" => 1))->result();
  }

  /**
   * Get rows in (retailer) store table by matching their organization/retailer's categories or types.
   * @param  array $categories row ids in organization/retailer category table
   * @param  array $types      row ids in organization type table
   * @param  bool  $and        whether mactching both or one of two when both filters are selected
   * @param  int   $org_id     row id in retailer/organization table
   * @return array matching rows in (retailer) store table  
   */
  function get_stores($categories = FALSE, $types = FALSE, $and = TRUE, $org_id = 0) {
    $organizations = $this->get_organizations($categories, $types, $and, $org_id);
    $locations = array();
    $this->load->helper("array");
    foreach ($organizations as $organization) {
      $organization_locations = $this->db->order_by("store_name", "asc")->get_where($this->tables["store"], array("retailer_id" => $organization->id))->result();
      if (is_array($organization_locations) && ( !empty($organization_locations))) {
        array_objects_merge($locations, $organization_locations);
      }
    }
    if ( !empty($locations)) {
      $locations = object_array_sort($locations, "store_name");
    }
    return $locations;
  }

  /**
   * Get array of organizations > provinces/states > stores.
   * Result matches require organization types and categories.
   * With organization(s) and store(s) are pre-selected.
   * @param  array  $types         row ids in organization type table
   * @param  array  $categories    row ids in organization/retailer category table
   * @param  string $organizations row ids in organization/retailer table
   * @param  string $stores        row ids in (retailer) store table
   * @return array  can be convert to json that jstree library can use
   */
  function get_stores_jstree($types = FALSE, $categories = FALSE, $organizations = "", $stores = "") {
    $jstree = array();
    $all_organizations = $this->get_organizations($categories, $types);
    $selected_organizations = explode(",", $organizations);
    $selected_stores = explode(",", $stores);
    foreach ($all_organizations as $organization) {
      $node_org = array(
          "data" => $organization->name,
          "attr" => array(
              "id" => "org_".$organization->id,
              "data-id" => $organization->id,
              "class" => "nodeOrg"
          ),
          "children" => array()
      );
      $checked = in_array($organization->id, $selected_organizations);
      $all_states = TRUE;
      $some_states = FALSE;
      $organization->states = $this->db->order_by("province_name", "asc")->get_where($this->tables["state"], array("country_code" => $organization->country_code))->result();
      foreach ($organization->states as $state) {
        $node_state = array(
            "data" => $state->province_name,
            "attr" => array(
                "id" => $node_org["attr"]["id"]."_pro_".$state->province_code
            ),
            "children" => array()
        );
        $all_stores = TRUE;
        $some_stores = FALSE;
        $state->stores = $this->db->order_by("store_name", "asc")->get_where($this->tables["store"], array("province" => $state->province_code, "retailer_id" => $organization->id))->result();
        if ( !empty($state->stores)) {
          foreach ($state->stores as $store) {
            $node_store = array(
                "data" => $store->store_name,
                "attr" => array(
                    "id" => "store_".$store->id,
                    "data-id" => $store->id,
                    "class" => "nodeStore"
                )
            );
            if ($checked) {
              $node_store["attr"]["class"] .= " jstree-checked";
            } elseif (in_array($store->id, $selected_stores)) {
              $node_store["attr"]["class"] .= " jstree-checked";
              $some_stores = TRUE;
            } else {
              $node_store["attr"]["class"] .= " jstree-unchecked";
              $all_stores = FALSE;
            }
            $node_state["children"][] = $node_store;
          }
          if ($checked) {
            $node_state["attr"]["class"] = "jstree-checked";
          } elseif ($all_stores) {
            $node_state["attr"]["class"] = "jstree-checked";
            $some_states = TRUE;
          } elseif ($some_stores) {
            $node_state["attr"]["class"] = "jstree-undetermined";
            $node_state["state"] = "open";
            $some_states = TRUE;
            $all_states = FALSE;
          } else {
            $node_state["attr"]["class"] = "jstree-unchecked";
            $all_states = FALSE;
          }
          $node_org["children"][] = $node_state;
        }
      }
      if ( !empty($node_org["children"])) {
        if ($checked OR $all_states) {
          $node_org["attr"]["class"] .= " jstree-checked";
        } elseif ($some_states) {
          $node_org["attr"]["class"] .= " jstree-undetermined";
          $node_org["state"] = "open";
        } else {
          $node_org["attr"]["class"] .= " jstree-unchecked";
        }
        $jstree[] = $node_org;
      }
    }
    return $jstree;
  }

  /**
   * Get organization types in an array of row id as key and name as value.
   * @return array all organization types
   */
  function get_type_options() {
    $types = $this->db->order_by("name", "asc")->get($this->tables["organization_type"])->result();
    $type_options = array();
    foreach ($types as $type) {
      $type_options[$type->id] = $type->name;
    }
    return $type_options;
  }

  /**
   * Get target ids by user account.
   * @param  int    $user_id
   * @return string list of target ids seperated by comma
   */
  function get_target_by_account($user_id = 0) {
    if (empty($user_id)) {
      $user_id = $this->session->userdata("user_id");
    } 
    $quiz_target_ids = $this->db->select("quiz.target_id")->distinct()->join($this->tables["site"],'site.id = quiz.site_id')->where('site.hidden', 0)->get_where($this->tables["quiz"], array("quiz.status" => "1"))->result();

    $user_targetted_ids = array();
    foreach($quiz_target_ids as $target_id) {
      if (self::is_user_targetted($target_id->target_id, $user_id)) {
        $user_targetted_ids[] = $target_id->target_id;
      }
    }
    return $user_targetted_ids;
  }

  /**
   * Determind user is targetted.
   * @param  int  $target_id row id in target table
   * @param  int  $user_id   row id in user table
   * @return bool TRUE if user's store, employer or job title match target condition, or FALSE otherwise
   */
  function is_user_targetted($target_id, $user_id) {
    $target = self::target_load($target_id, FALSE);
    $user_profile = $this->db->get_where($this->tables["user_profile"], array("user_id" => $user_id))->row();
    $targetted = FALSE;
    $not_set = !isset($target->stores);
    if ( !$not_set) { // Has set targetted store(s).
      foreach ($target->stores as $target_store) {
        if ($target_store->store_id == $user_profile->store_id) {
          $targetted = TRUE;
          break;
        }
      }
    }
    if ($not_set OR ( !$targetted)) { // Either has not set targgetted store(s), or user's store is not one of the targetted store(s)
      $not_set = !isset($target->organizations);
      if ( !$not_set) {
        foreach ($target->organizations as $target_organization) {
          if ($target_organization->organization_id == $user_profile->retailer_id) {
            $targetted = TRUE;
            break;
          }
        }
      }
    }
    if ($not_set) { // It's TRUE when all organizations and stores are not specifically targetted
      $category_matched = FALSE;
      $type_matched = FALSE;
      $is_category_targetted = isset($target->categories);
      $is_type_targetted = isset($target->types);
      if ($is_category_targetted) {
        $category_ids = array();
        foreach ($target->categories as $category) {
          $category_ids[] = $category->organization_category_id;
        }
        $category_matched = ($this->db->where_in("category_id", $category_ids)->get_where($this->tables["organization_in_category"], array("retailer_id" => $user_profile->retailer_id))->num_rows() > 0);
        
      }
      if ($is_type_targetted) {
        $type_ids = array();
        foreach ($target->types as $type) {
          $type_ids[] = $type->organization_type_id;
        }
        $type_matched = ($this->db->where_in("type_id", $type_ids)->get_where($this->tables["organization_in_type"], array("organization_id" => $user_profile->retailer_id))->num_rows() > 0);
      }
      if ($is_category_targetted && $is_type_targetted) {
        $targetted = ($category_matched && $type_matched);
      } elseif ($is_category_targetted) {
        $targetted = $category_matched;
      } elseif ($is_type_targetted) {
        $targetted = $type_matched;
      }
    }
    return $targetted;
  }

  /**
   * Get targets for a spesific sub-domain/site.
   * @param  int   $site_id    row id in site table
   * @param  bool  $get_detail determine to get what are targeted
   * @param  int   $org_id     row id in retailer/organization table
   * @return array found targets
   */
  function get_targets_by_site($site_id = 1, $get_detail = FALSE, $org_id = 0) {
    if ($site_id > 1) {
      $this->db->where("site_id", $site_id);
    }
    try {
      $targets = $this->db->get($this->tables["target"])->result();
    } catch (Exception $ex) {
      $targets = array();
    }
    if (is_object_array($targets) && $get_detail) {
      foreach ($targets as $key => $target) {
        if ($org_id > 0) {
          $this->db->where("l.retailer_id", $org_id);
        }
        $locations = $this->db->select("l.*, s.province_name")->distinct()->join($this->tables["store"]." l", "l.id=tl.store_id")->join($this->tables["state"]." s", "s.province_code=l.province")->get_where($this->tables["target_store"]." tl", array("tl.target_id" => $target->id))->result();
        if (is_array($locations) && ( !empty($locations))) {
          $targets[$key]->locations = $locations;
        }
        if ($org_id > 0) {
          $this->db->where("o.id", $org_id);
        }
        $organizations = $this->db->select("o.*")->join($this->tables["organization"]." o", "o.id=to.organization_id")->where("to.id !=", 130)->get_where($this->tables["target_organization"]." to", array("to.target_id" => $target->id))->result();
        if (is_array($organizations) && ( !empty($organizations))) {
          $targets[$key]->organizations = $organizations;
        }
        $categories = $this->db->select("c.*")->join($this->tables["organization_category"]." c", "c.id=tc.organization_category_id")->get_where($this->tables["target_organization_category"]." tc", array("tc.target_id" => $target->id))->result();
        if (is_array($categories) && ( !empty($categories))) {
          $targets[$key]->categories = $categories;
        }
        $types = $this->db->select("t.*")->join($this->tables["organization_type"]." t", "t.id=tt.organization_type_id")->get_where($this->tables["target_organization_type"]." ttl", array("tt.target_id" => $target->id))->result();
        if (is_array($types) && ( !empty($types))) {
          $targets[$key]->types = $types;
        }
      }
    }
    return $targets;
  }

  /**
   * Get targeted organizations by a site or targets.
   * @param  int   $site_id          row id in site table
   * @param  array $targets          rows in target table
   * @param  bool  $include_location determine result includes targetted location's organization
   * @return array found organization objects
   */
  function get_targeted_orgnizations($site_id = 1, $targets = FALSE, $include_location = TRUE) {
    if ( !is_object_array($targets)) {
      $targets = $this->get_targets_by_site($site_id, TRUE);
    }
    $organizations = array();
    if (is_object_array($targets)) {
      foreach ($targets as $target) {
        if (isset($target->organizations)) {
          $organizations = array_objects_merge($organizations, $target->organizations);
        }
        if (isset($target->locations) && $include_location) {
          $organization_ids = array();
          foreach ($target->locations as $location) {
            if ( !in_array($location->retailer_id, $organization_ids)) {
              $organization_ids[] = $location->retailer_id;
            }
          }
          if ( !empty($organization_ids)) {
            $location_organizations = $this->db->where_in("id", $organization_ids)->get($this->tables["organization"])->result();
            if (is_object_array($location_organizations)) {
              $organizations = array_objects_merge($organizations, $location_organizations);
            }
          }
        }
        if ( !(isset($target->organizations) OR isset($target->locations)) && (isset($target->categories) OR isset($target->types))) {
          $category_ids = array();
          $type_ids = array();
          if (isset($target->categories)) {
            foreach($target->categories as $category) {
              $category_ids[] = $category->id;
            }
          }
          if (isset($target->types)) {
            foreach($target->types as $type) {
              $type_ids[] = $type->id;
            }
          }
          $tagged_organizations = $this->get_organizations($category_ids, $type_ids);
          if (is_object_array($tagged_organizations)) {
             $organizations = array_objects_merge($organizations, $tagged_organizations);
          }
        }
      }
    }
    if ( !empty($organizations)) {
      $organizations = object_array_sort($organizations, "name");
    }
    return $organizations;
  }

  /**
   * Get targeted locations by a site or targets.
   * @param  int   $site_id              row id in site table
   * @param  array $targets              rows in target table
   * @param  bool  $include_organization determine result includes targetted organization's locations
   * @param  int   $org_id               row id in retailer/organization table
   * @return array found location objects
   */
  function get_targeted_locations($site_id = 1, $targets = FALSE, $include_organization = TRUE, $org_id = 0) {
    if ( !is_object_array($targets)) {
      $targets = $this->get_targets_by_site($site_id, TRUE);
    }
    $locations = array();
    if (is_object_array($targets)) {
      foreach ($targets as $target) {
        if (isset($target->locations)) {
          if ($org_id > 0) {
              $targeted_locations = array();
              foreach ($target->locations as $targeted_location) {
                  if ( $targeted_location->retailer_id == $org_id) {
                      $targeted_locations[] = $targeted_location;
                  }
              }
              if ( !empty($targeted_locations)) {
                  $locations = array_objects_merge($locations, $targeted_locations);
              }
          } else {
              $locations = array_objects_merge($locations, $target->locations);
          }
        }
        if (isset($target->organizations) && $include_organization) {
          if ($org_id > 0) {
            $organization_ids = array($org_id);
          } else {
            $organization_ids = array();
            foreach ($target->organizations as $organization) {
              $organization_ids[] = $organization->id;
            }
          }
          if ( !empty($organization_ids)) {
            $organization_locations = $this->db->select("l.*, s.province_name")->distinct()->join($this->tables["state"]." s", "s.province_code=l.province")->where_in("l.retailer_id", $organization_ids)->get($this->tables["store"]." l")->result();
            if (is_object_array($organization_locations)) {
              $locations = array_objects_merge($locations, $organization_locations);
            }
          }
        }
        if ( !(isset($target->organizations) OR isset($target->locations)) && (isset($target->categories) OR isset($target->types))) {
          $category_ids = array();
          $type_ids = array();
          if (isset($target->categories)) {
            foreach($target->categories as $category) {
              $category_ids[] = $category->id;
            }
          }
          if (isset($target->types)) {
            foreach($target->types as $type) {
              $type_ids[] = $type->id;
            }
          }
          $tagged_locations = $this->get_stores($category_ids, $type_ids, TRUE, $org_id);
          if (is_object_array($tagged_locations)) {
             $locations = array_objects_merge($locations, $tagged_locations);
          }
        }
      }
    }
    if ( !empty($locations)) {
      $locations = object_array_sort($locations, "store_name");
    }
    return $locations;
  }

  /**
   * Get targeted members by a site, or targets, or locations
   * @param  int   $site_id   row id in site table
   * @param  array $targets   rows in target table
   * @param  array $locations rows in retailer_store table
   * @return array found member objects
   */
  function get_targeted_members($site_id = 1, $targets = FALSE, $locations = FALSE) {
    if ( !is_object_array($locations)) {
      $locations = $this->get_targeted_locations($site_id, $targets);
    }
    $members = array();
    if (is_object_array($locations) ) {
      $location_ids = array();
      foreach ($locations as $location) {
        $location_ids[] = $location->id;
      }
      if ( !empty($location_ids)) {
        $members = $this->db->select("u.*, up.*")->distinct->where_in("up.store_id", $location_ids)->join($this->tables["user_profile"]," up", "up.user_id = u.id", "left outer")->join($this->tables["user_role"]," ur", "ur.user_id = u.id", "left outer")->join($this->tables["role"]," r", "r.id = ur.role_id", "left outer")->order_by("up.screen_name", "asc")->get_where($this->tables["user"]." u", array("r.system" => 3))->result();
        if ( !is_object_array($members)) {
          $members = FALSE;
        }
      }
    }
    return $members;
  }

  /**
   * Get trained members by a site, or targets, or locations, or targeted members
   * @param  int   $site_id          row id in site table
   * @param  array $targets          rows in target table
   * @param  array $locations        rows in retailer_store table
   * @param  array $targeted_members targeted user objects with role.system = 3
   * @return array found member objects
   */
  function get_trained_members($site_id = 1, $targets = FALSE, $locations = FALSE, $targeted_members = FALSE) {
    if ( !is_object_array($targeted_members)) {
      $targeted_members = $this->get_targeted_members($site_id, $targets, $locations);
    }
    if (is_object_array($targeted_members)) {
      foreach ($targeted_members as $key => $targeted_member) {
        if ($this->db->get_where($this->tables["quiz_history"], array("user_id" => $targeted_member->user_id))->num_rows() <= 0) {
          unset($targeted_members[$key]);
        }
      }
    }
    return $targeted_members;
  }

  /**
   * Get targeted organization's locations which must be targeted by same site.
   * @param  int   $org_id  row id in retailer/organization table
   * @param  int   $site_id row id in site table
   * @return array matched rows in retailer_store/location table
   */
  function get_targeted_organization_locations($org_id = 0, $site_id = 1) {
    $targets = $this->get_targets_by_site($site_id, TRUE, $org_id);
    return $this->get_targeted_locations($site_id, $targets, TRUE, $org_id);
  } 
}
