<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Badge_model extends HotCMS_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->config('badge/badge', TRUE);
        $this->tables = $this->config->item('tables', 'badge');
    }

    /**
     * Counts all badges
     * @param  array  filters, including search keyword, sorting field, and customized filter criteria
     * @return int
     */
    public function badge_count($filters = FALSE) {
        if (is_array($filters)) {
            if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
                $this->db->like('name', $filters['keyword']);
            }

            if (array_key_exists('status', $filters) && $filters['status'] > '') {
                if (is_array($filters['status'])) {
                    $this->db->where_in('status', $filters['status']);
                } else {
                    $this->db->where('status', $filters['status']);
                }
            }
        }
        //$this->db->where('site_id', $this->site_id);
        return $this->db->count_all_results($this->tables['badge']);
    }

    /**
     * Lists all badges from DB
     * @param  array  filters, including search keyword, sorting field, and customized filter criteria
     * @param  int  page number
     * @param  int  per page
     * @return array of objects
     */
    public function badge_list($filters = FALSE, $page_num = 1, $per_page = 0) {
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
            //$this->db->limit($per_page, $offset);
        }
        //if ($type_id > 0) {
        //  $this->db->where('n.quiz_type_id', (int)$type_id);
        //}
        //if ($live_only) {
        //  $this->db->where('n.status', 1);
        //}
        if (is_array($filters)) {
            if (array_key_exists('keyword', $filters) && $filters['keyword'] > '') {
                $this->db->like('n.name', $filters['keyword']);
            }
            if (array_key_exists('type_id', $filters) && $filters['type_id'] > '') {
                if (is_array($filters['type_id'])) {
                    $this->db->where_in('n.quiz_type_id', $filters['type_id']);
                } else {
                    $this->db->where('n.quiz_type_id', $filters['type_id']);
                }
            }
            if (array_key_exists('status', $filters) && $filters['status'] > '') {
                if (is_array($filters['status'])) {
                    $this->db->where_in('n.status', $filters['status']);
                } else {
                    $this->db->where('n.status', $filters['status']);
                }
            }
            $sortable_fields = array('name', 'status', 'create_timestamp');
            if (array_key_exists('sort_by', $filters) && $filters['sort_by'] > '' && in_array($filters['sort_by'], $sortable_fields)) {
                if (array_key_exists('sort_direction', $filters) && strtoupper($filters['sort_direction']) == 'DESC') {
                    $sort_direction = 'DESC';
                } else {
                    $sort_direction = 'ASC';
                }
                $this->db->order_by($filters['sort_by'], $sort_direction);
            } else {
                $this->db->order_by('sequence', 'ASC');
            }
        } else {
            $this->db->order_by('sequence', 'ASC');
        }
        $query = $this->db->select('*')
                ->order_by('name', 'DESC')
                ->get($this->tables['badge']);
        return $query->result();
    }

    public function insert() {
        $time = now();
        $this->db->set('name', $this->input->post('name'));
        $this->db->set('description', $this->input->post('description'));
        $this->db->set('status', 0);
        $this->db->set('create_timestamp', $time);
        $this->db->set('update_timestamp', $time);

        $inserted = $this->db->insert($this->tables['badge']);
        if ($inserted) {
            $badge_id = $this->db->insert_id();
            return $badge_id;
        }
        return FALSE;
    }

    public function badge_update($badge_id) {
        $this->db->set('name', $this->input->post('name'));
        $this->db->set('description', $this->input->post('description'));
        $this->db->set('activity_feed_description', $this->input->post('feed_description'));
        $this->db->set('status', $this->input->post('status'));
        $this->db->set('award_type', $this->input->post('award_type'));
        $this->db->set('award_amount', $this->input->post('award_amount'));
        $this->db->where('id', $badge_id);
        $this->db->update($this->tables['badge']);
    }

    /**
     * Get single badge from DB
     * @param  int  order id
     * @param  bool  $active_only when set to true, returns active badges only
     * @return object with one row
     */
    public function badge_load($id, $active_only = TRUE) {
        if ($active_only) {
            $this->db->where('status', 1);
        }
        $query = $this->db->select('b.*')
                ->where('b.id', $id)
                ->get($this->tables['badge'] . ' b');
        return $query->row();
    }

    /**
     * Get single badge from DB by badge name
     * @param  int  order id
     * @param  bool  $active_only when set to true, returns active badges only
     * @return object with one row
     */
    public function badge_load_by_name($name) {

        $this->db->where('status', 1);
        $query = $this->db->select('b.*')
                ->where('lower(b.name)', $name)
                ->get($this->tables['badge'] . ' b');
        return $query->row();
    }

    /**
     * Update icon image
     * @param  int  badge id
     * @param  int  asset id
     * @return object with one row
     */
    public function update_icon_image($badge_id, $asset_id) {

        $query = $this->db->set('icon_image_id', $asset_id)
                ->where('id', $badge_id)
                ->update($this->tables['badge']);
        return TRUE;
    }

    /**
     * Update big image
     * @param  int  order id
     * @param  bool  $active_only when set to true, returns active badges only
     * @return object with one row
     */
    public function update_big_image($badge_id, $asset_id) {

        $query = $this->db->set('big_image_id', $asset_id)
                ->where('id', $badge_id)
                ->update($this->tables['badge']);
        return TRUE;
    }

    /**
     * Store sequence in database (duplicated for each module with sortable
     * @TODO move it to main model, fix return value 
     * @param  string  table
     * @param  int  item id
     * @param  int  item sequence
     */
    public function save_badge_sequence($table, $id, $sequence) {
        $this->db->set('sequence', $sequence);
        $this->db->where('id', $id);
        $this->db->update($table);
    }

    /**
     * Check user badge
     * 
     * @param int user_id
     * @param string badge name
     * 
     * @return bool true when user has the badge, false when user hasn't
     */
    public function check_user_badge($user_id, $badge_name) {
        //get badge id
        $badge_id = $this->get_badge_id_by_name($badge_name);
        //var_dump($badge_id);
        $query = $this->db->select('id')
                ->where('ref_table', 'badge')
                ->where('ref_id', $badge_id)
                ->where('user_id', $user_id)
                ->get($this->tables['points']);

        if (!empty($query->row()->id)) {
            return true;
        }

        $query2 = $this->db->select('id')
                ->where('ref_table', 'badge')
                ->where('ref_id', $badge_id)
                ->where('user_id', $user_id)
                ->get($this->tables['draws']);
        if (!empty($query2->row()->id)) {
            return true;
        }

        return false;
    }

    /**
     * Get badge id by name
     * @param  string  badge name
     * @return int badge id
     */
    public function get_badge_id_by_name($name) {

        $query = $this->db->select('b.id')
                ->where('lower(b.name)', $name)
                ->get($this->tables['badge'] . ' b');
        $result = (!empty($query->row()->id)) ? $query->row()->id : 0;
        return $result;
    }

    /**
     * Get 10 user id with most  quizzes for last month
     * @return array user_id
     */
    public function get_users_for_keener() {
        //get timestamps for last month
        //date_default_timezone_set('PST');
        $this_month = date('m');
        $this_year = date('Y');
        $last_month = $this_month - 1;
        if ($last_month == 0)
            $last_month = 12;
        if ($this_month == 1) {
            $last_year = $this_year - 1;
        } else {
            $last_year = $this_year;
        }

        $start = gmmktime(0, 0, 0, $last_month, 1, $last_year);
        $end = gmmktime(0, 0, 0, $this_month, 1, $this_year) - 1;

        $query = sprintf('SELECT count( user_id ) AS quiz_number, user_id
        FROM `quiz_history`
        WHERE `create_timestamp`
        BETWEEN %s
        AND %s
        GROUP BY user_id
        ORDER BY quiz_number DESC LIMIT 10',$start, $end);

        $users = $this->db->query($query)->result();

        return $users;
    }

}

?>