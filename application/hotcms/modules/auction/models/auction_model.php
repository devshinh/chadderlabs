<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Name:  Auction Model
 *
 * Author: jeffrey@hottomali.com
 *
 * Created:  12.12.2011
 * Last updated:  03.21.2012
 *
 * Description:  Auction module.
 */
class Auction_model extends HotCMS_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->config('auction/auction', TRUE);
        $this->load->config('auction/email_notification', TRUE);
        $this->load->library('email');
        $this->tables = $this->config->item('tables', 'auction');
    }

    /**
     * Get an auction object from slug
     * @param  string  auction slug or ID, or leave empty for the latest active auction
     */
    public function get_auction($identifier = '') {
        if ($identifier > '') {
            $key = (is_numeric($identifier) ? 'id' : 'slug');
            $query = $this->db->select()
                    ->where($key, $identifier)
                    ->where('active', 1)
                    ->where('site_id', $this->site_id)
                    ->where('UNIX_TIMESTAMP(opening_time) < ', time())
                    ->get($this->tables['auction']);
        } else {
            // pick a default, which is the last created and enabled auction
            $query = $this->db->select()
                    ->where('active', 1)
                    ->where('site_id', $this->site_id)
                    ->where('UNIX_TIMESTAMP(opening_time) < ', time())
                    ->order_by('create_date DESC')
                    ->limit(1)
                    ->get($this->tables['auction']);
        }
        $result = $query->row();
        return $result;
    }

    /**
     * Get a random auction slug for showcase purpose
     */
    public function get_random_itemslug() {
        $query = $this->db->select('i.slug')
                ->join($this->tables['auction'] . ' AS a', 'i.auction_id = a.id')
                ->where('i.active', 1)
                ->where('a.site_id', $this->site_id)
                ->where('UNIX_TIMESTAMP(a.opening_time) < ', time())
                ->order_by('', 'random')
                ->limit(1)
                ->get($this->tables['auction_item'] . ' AS i');
        if ($query->num_rows > 0) {
            $result = $query->row()->slug;
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * Get an auction category object from slug
     */
    public function get_category($slug) {
        $query = $this->db->select()
                ->where('slug ', $slug)
                ->where('active', 1)
                ->where('site_id', $this->site_id)
                ->get($this->tables['auction_category']);
        return $query->row();
    }

    /**
     * Get an auction item object
     * @param string  Item ID or Slug
     * @param int     Auction ID
     * @param int     Category ID
     */
    public function get_item($item_guid, $auction_id = 0, $category_id = 0) {
        $this->db->select()
                ->where(is_numeric($item_guid) ? 'id' : 'slug', $item_guid)
                ->where('active', 1);
        if ($auction_id > 0) {
            $this->db->where('auction_id', $auction_id);
        }
        if ($category_id > 0) {
            $this->db->where('category_id', $category_id);
        }
        $query = $this->db->get($this->tables['auction_item']);
        return $query->row();
    }

    /**
     * List auction items
     */
    public function list_items($auction_id, $category_id = 0) {
        if (!($auction_id > 0)) {
            return FALSE;
        }
        $this->db->select()
                ->where('active', 1)
                ->where('auction_id', $auction_id)
                ->order_by('sequence');
        if ($category_id > 0) {
            $this->db->where('category_id', $category_id);
        }
        $query = $this->db->get($this->tables['auction_item']);
        $result = $query->result();
        return $result;
    }

    /**
     * List auction items images -> just first image for item
     */
    public function list_items_images($item_id) {
        if (!($item_id > 0)) {
            return FALSE;
        }
        $this->db->select()
                ->join('asset AS a', 'i_a.asset_id = a.id')
                ->where('i_a.item_id', $item_id)
                ->order_by('i_a.sequence')
                ->limit(1);

        $query = $this->db->get($this->tables['auction_item_asset'] . ' AS i_a');
        return $result = $query->row();
    }

    /**
     * List a user's auction items
     */
    public function list_user_items($user_id, $auction_id) {
        if (!($auction_id > 0 && $user_id > 0)) {
            return FALSE;
        }

        $this->db->select('b.amount, b.highest, a.*')
                ->join($this->tables['auction_item'] . ' a', 'b.item_id=a.id')
                ->where('b.user_id', $user_id)
                ->where('b.status', 1)
                ->where('a.active', 1)
                ->where('a.auction_id', $auction_id)
                ->order_by('a.sequence');
        $query = $this->db->get($this->tables['auction_bid'] . ' b');

        $result = $query->result();
        return $result;
    }

    /**
     * List user bids on all items including winning and losing
     */
    public function list_user_bids($user_id, $auction_id) {
        if (!($auction_id > 0 && $user_id > 0)) {
            return FALSE;
        }
        $query = $this->db->distinct()->select('auction_item.name, auction_bid.create_date, auction_item.slug, auction_bid.amount, auction_bid.highest')
                ->join('user', 'user.id = auction_bid.user_id')
                ->join('auction_item', 'auction_bid.item_id = auction_item.id')
                ->where('status', 1)
                ->where('auction_bid.user_id', $user_id)
                ->order_by('auction_bid.id', 'DESC')
                ->get($this->tables['auction_bid']);
        $result = $query->result();
        return $result;
    }

    /**
     * List auction item assets - images, documents, and videos
     */
    public function list_item_assets($item_id) {
        if (!($item_id > 0)) {
            return FALSE;
        }
        $query = $this->db->select('a.*')
                ->join($this->tables['auction_item_asset'] . ' ia', 'ia.asset_id=a.id')
                ->where('a.site_id', $this->site_id)
                ->where('ia.item_id', $item_id)
                ->order_by('ia.sequence')
                ->get($this->tables['asset'] . ' a');
        $assets = $query->result();
        if (is_array($assets) && count($assets) > 0) {
            foreach ($assets as $asset) {
                switch ($asset->type) {
                    case 2: // document
                        $asset->html = '<a href="/asset/upload/document/' . $asset->file_name . '.' . $asset->extension . '" title="' . $asset->name . '">' . $asset->name . '</a>';
                        break;
                    case 3: // video
                        // todo: add default video player
                        $asset->html = '<a href="/asset/upload/video/' . $asset->file_name . '.' . $asset->extension . '" title="' . $asset->name . '">' . $asset->name . '</a>';
                        break;
                    case 1: // image
                    default:
                        $asset->html = '<img src="/asset/upload/image/' . $asset->file_name . '.' . $asset->extension . '" alt="' . $asset->name . '" />';
                        $asset->html_tmb_200 = '<img src="/asset/upload/image/tmb_200/' . $asset->file_name . '.' . $asset->extension . '" alt="' . $asset->name . '" />';
                }
            }
        }
        return $assets;
    }

    /**
     * List auction item bids
     */
    public function list_item_bids($item_id) {
        if ($item_id < 1) {
            return FALSE;
        }
        $query = $this->db->select('u.username,p.first_name, p.last_name,b.*')
                ->join($this->tables['user'] . ' u', 'u.id=b.user_id', 'RIGHT OUTER')
                ->join($this->tables['user_profile'] . ' p', 'u.id=p.user_id', 'RIGHT OUTER')
                ->where('b.item_id', $item_id)
                ->where('b.status', 1)
                ->order_by('create_date DESC')
                ->limit(5)
                ->get($this->tables['auction_bid'] . ' b');
        $bids = $query->result();
        return $bids;
    }

    /**
     * List auction categories
     * @param bool Exclude empty categories from the list
     */
    public function list_categories($exclude_empty = TRUE) {
        //$this->output->enable_profiler();
        if ($exclude_empty) {
            $query = $this->db->select('c.*')
                    ->distinct(TRUE)
                    ->join($this->tables['auction_item'] . ' i', 'i.category_id = c.id')
                    ->where('c.active', 1)
                    ->group_by('c.id')
                    ->order_by('c.sequence')
                    ->get($this->tables['auction_category'] . ' c');
        } else {
            $query = $this->db->select()
                    ->where('active', 1)
                    ->order_by('sequence')
                    ->get($this->tables['auction_category']);
        }
        $result = $query->result();
        return $result;
    }

    /**
     * Check a new bid to see if it's valid
     * @param string  Item ID or Slug
     * @param string  Bidding amount
     */
    public function validate_bid($item_guid, $amount) {
        $amount = (float) $amount;
        if ($amount <= 0) {
            return -1; // bid amount is required
        }
        // get item information
        $item = self::get_item($item_guid);
        if (!$item) {
            return -2; // item not found
        }
        if ($amount < $item->minimum_bid) {
            return -3; // min bid not met
        }
        if ($amount > 99999999.99) {
            return -5; //maximum bid amount for Decimal(10,2)
        }
        // get previous high bid
        $query = $this->db->select()
                ->where('item_id', $item->id)
                ->where('highest', 1)
                ->where('status', 1)
                ->get($this->tables['auction_bid']);
        $high_bid = $query->row();
        if ($high_bid && $amount < ($high_bid->amount + $item->minimum_increment)) {
            return -4; // min increament not met
        }

        return 1;
    }

    /**
     * Place a bid
     */
    public function place_bid($item_id, $user_id, $amount) {
        // check if this is a valid bid
        $valid_status = self::validate_bid($item_id, $amount);
        if ($valid_status != 1) {
            return $valid_status;
        }
        // update previous highest bid
        $data = array(
            'highest' => 0,
        );
        $this->db->update($this->tables['auction_bid'], $data, array('item_id' => $item_id, 'highest' => 1));
        // insert new bid
        $data = array(
            'item_id' => $item_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'highest' => 1,
            'status' => 1,
        );
        $result = $this->db->insert($this->tables['auction_bid'], $data);
        $this->email_last_bidder($item_id, $user_id);
        return $result;
    }

    private function email_last_bidder($item_id, $user_id) {
        //check for people who have been outbid.
        //coudn't get active record to do extra fields properly, so fell back
        /*
          $query = $this->db->distinct()
          ->select('auction_bid.user_id, member.email, auction_item.name', 'member_profile.first_name', 'auction_item.slug as item_slug', 'auction.slug as auction_slug', 'auction_category.slug as category_slug')
          ->join('member','member.id = auction_bid.user_id')
          ->join('member_profile', 'member.id = member_profile.user_id')
          ->join('auction_item','auction_bid.item_id = auction_item.id')
          ->join('auction', 'auction_item.auction_id = auction.id')
          ->join('auction_category','auction_category.id = auction_item.category_id')
          ->where('auction_bid.item_id', $item->id)
          ->where('auction_bid.highest', 0)
          ->where('auction_bid.status', 1)
          ->order_by('auction_bid.id','DESC')
          ->limit(1)
          ->get($this->tables['auction_bid']);
         */
        $sql = "SELECT `auction_bid`.`user_id`, `user`.`email`, `auction_item`.`name` , `auction_item`.`slug` AS `item_slug`, `user_profile`.`first_name`, `auction`.`slug` AS `auction_slug`, `auction_category`.`slug` AS `category_slug`
FROM (`auction_bid`)
JOIN  `user` ON  `user`.`id` =  `auction_bid`.`user_id`
JOIN  `user_profile` ON  `user`.`id` =  `user_profile`.`user_id`
JOIN  `auction_item` ON  `auction_bid`.`item_id` =  `auction_item`.`id`
JOIN  `auction` ON  `auction_item`.`auction_id` =  `auction`.`id`
JOIN  `auction_category` ON  `auction_category`.`id` =  `auction_item`.`category_id`
WHERE  `auction_bid`.`item_id` = ?
AND  `auction_bid`.`highest` =0
AND  `auction_bid`.`status` =1
ORDER BY  `auction_bid`.`id` DESC
LIMIT 1
    ";
        $query = $this->db->query($sql, array($item_id));
        //die($this->db->last_query());
        $results = $query->result();

        foreach ($results as $row) {
            //var_dump($row);
            $member_email = $row->email;
            $product_name = $row->name;
            $first_name = $row->first_name;
            $item_link = 'gallery/' . $row->auction_slug . '/' . $row->category_slug . '/' . $row->item_slug;

            // send email
            $data = array(
                'email' => $member_email,
                'product_name' => $product_name,
                'first_name' => $first_name,
                'item_link' => $item_link
            );

            //die($this->config->item('email_templates', 'email_notification').$this->config->item('email_outbid', 'email_notification'));
            //$message = $this->load->view($this->config->item('email_templates', 'email_notification').$this->config->item('email_outbid', 'email_notification'), $data, true);
            $message = sprintf($this->config->item('outbid_notice', 'auction'), $first_name, $product_name, $item_link);
            $subject = "You've Been Outbid!";
            if ($user_id == $row->user_id) {
                $subject = "You've Outbid Yourself!";
            }
            //$this->email->clear();
            $config['mailtype'] = "html";
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->from($this->config->item('admin_email', 'email_notification'), $this->config->item('site_title', 'email_notfication'));
            $this->email->to($member_email);
            $this->email->subject($subject);
            $this->email->message($message);

            return $this->email->send();
        }
    }

    public function get_all_auctions() {
        $query = $this->db->select()
                ->order_by('create_date DESC')
                ->get($this->tables['auction']);

        $result = $query->result();
        return $result;
    }

    public function get_auction_by_id($id) {
        $query = $this->db->select()
                ->where('id', $id)
                ->get($this->tables['auction']);

        $result = $query->row();
        return $result;
    }

    /**
     * Get an auction bids
     * @param int     Auction ID
     *
     * @return array of bids
     */
    public function get_auction_bids($id, $page_num = 1, $per_page = 100) {
        $per_page = (int) $per_page;
        $page_num = (int) $page_num;
        if ($page_num < 1) {
            $page_num = 1;
        }
        $offset = ($page_num - 1) * $per_page;
        if ($offset < 0) {
            $offset = 0;
        }
        $query = $this->db->select('b.*, i.name, i.auction_id as auction_id, u.first_name, u.last_name')
                ->join($this->tables['auction_item'] . ' i', 'i.id = b.item_id')
                ->join($this->tables['user_profile'] . ' u', 'b.user_id = u.user_id')
                ->where('i.auction_id', $id)
                ->order_by('b.create_date DESC')
                ->limit($per_page, $offset)
                ->get($this->tables['auction_bid'] . ' b');

        $result = $query->result();

        return $result;
    }

    /**
     * Highest bids?
     * @param int  item ID
     *
     * @return 1/0
     */
    public function highest_bid($id) {

        $query = $this->db->select('highest')
                ->where('id', $id)
                ->get($this->tables['auction_bid']);

        $result = $query->row();
        return $result->highest;
    }

    /**
     * set auction bid as highest
     * @param int item ID
     */
    public function set_as_highest_bid($id) {

        $query = $this->db->select('item_id')
                ->where('id', $id)
                ->get($this->tables['auction_bid']);
        $item_id = $query->row()->item_id;
        //find scond highest bid
        $query = $this->db->select()
                ->where('item_id', $item_id)
                ->where('highest !=', 1)
                ->order_by('amount DESC')
                ->limit(1)
                ->get($this->tables['auction_bid']);

        $bid_id = $query->row()->id;

        $this->db->set('highest', 1);
        $this->db->where('id', $bid_id);
        $this->db->update($this->tables['auction_bid']);
    }

    /**
     * Delete an auction bid
     * @param int item ID
     */
    public function delete_bid($id) {

        // delete bid
        $this->db->where('id', $id);
        $this->db->delete($this->tables['auction_bid']);
    }

    /**
     * count all bids for pagination
     * @return int
     */
    public function count_all_bids() {
        return $this->db->count_all($this->tables['auction_bid']);
    }

    public function update($id) {
        self::_setElement();

        $this->db->set('update_date', 'CURRENT_TIMESTAMP', false);
        $this->db->where('id', $id);
        $this->db->update($this->tables['auction']);
    }

    private function _setElement() {
        // assign values
        $this->db->set('site_id', $this->session->userdata('siteID'));

        $this->db->set('name', $this->input->post('name'));
        $this->db->set('opening_time', $this->input->post('opening_time'));
        $this->db->set('closing_time', $this->input->post('closing_time'));
        $this->db->set('active', $this->input->post('active') ? 1 : 0 );
    }

}

?>