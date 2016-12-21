<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_widget extends Widget {
    function run($args = array()) {
        $this->load->config('target/target', TRUE);
        $this->load->model("site/site_model");
        $this->load->model("target/target_model");
        $this->load->model("account/account_model");
        $this->load->model("retailer/retailer_model");
         
        $data = array();
        $data['environment'] = $this->config->item('environment'); 
        
        $module_title = 'Report';

        if (is_array($args)) {
          $site_id = $this->account_model->get_admin_site($this->session->userdata("user_id"));
          if ($site_id > 0) {
            $report_args = explode("--", $args['slug']);
            $page_number = 0;
            if ((strcasecmp($report_args[0], "site") === 0) && (($site_id == $report_args[1]) OR ($site_id == 1))) {
              $page_number = 1;
              $site_id = $report_args[1];
            } elseif (strcasecmp($report_args[0], "organization") === 0) {
              $page_number = 2;
              $organization_id = $report_args[1];
              $site_id = $report_args[2];
            } elseif (strcasecmp($report_args[0], "location") === 0) {
              $page_number = 3;
              $location_id = $report_args[1];
              $site_id = $report_args[2];
            } elseif (strcasecmp($report_args[0], "member") === 0) {
              $page_number = 4;
              $user_id = $report_args[1];
              $site_id = $report_args[2];
            }
            if ($page_number > 0) {
              $data["report_site"] = $this->site_model->get_site_by_id($site_id);
            }
            if ($page_number === 1) {
              $site_targets = $this->target_model->get_targets_by_site($site_id, TRUE);
              $targeted_orgs = $this->target_model->get_targeted_orgnizations($site_id, $site_targets);
              $summary["retailers"] = count($targeted_orgs);
              $targeted_locations = $this->target_model->get_targeted_locations($site_id, $site_targets);
              $summary["locations"] = count($targeted_locations);
              $targeted_members = $this->target_model->get_targeted_members($site_id, $site_targets, $targeted_locations);
              $summary["total_members"] = count($targeted_members);
              $active_labs = $this->site_model->get_active_labs($site_id);
              $summary["active_labs"] = count($active_labs);
              $active_quizzes = $this->site_model->get_active_quizzes($site_id);
              $summary["active_quizzes"] = count($active_quizzes);
              $site_training_time = $this->account_model->get_site_training_time($site_id);
              $summary["training_hours"] = number_format(($site_training_time / 3600), 2)." hrs";

              $orgs_breakdown = array();
              foreach ($targeted_orgs as $targeted_org) {
                $orgs_breakdown[$targeted_org->id] = array(
                    "id" => $targeted_org->id,
                    "organization" => $targeted_org->name,
                    "locations" => 0,
                    "total_members" => 0,
                    "members_trained" => 0,
                    "quizzes_taken" => 0,
                    "training_time" => 0,
                    "total_quiz_score" => 0,
                    "avg_quiz_score" => 0,
                    "points_awarded" => 0,
                    "cost" => 0
                );
              }
              foreach ($targeted_locations as $targeted_location) {
                $orgs_breakdown[$targeted_location->retailer_id]["locations"]++;
              }
              $target_member_ids = array();
              foreach ($targeted_members as $targeted_member) {
                $orgs_breakdown[$targeted_member->retailer_id]["total_members"]++;
                $target_member_ids[$targeted_member->user_id] = $targeted_member->user_id;
                if ($this->account_model->is_user_trained($targeted_member->user_id, $site_id)) {
                  $orgs_breakdown[$targeted_member->retailer_id]["training_time"] += $this->account_model->get_user_training_time($site_id, $targeted_member->user_id);
                  $orgs_breakdown[$targeted_member->retailer_id]["members_trained"]++;
                }
              }
              $points_balance = $this->site_model->get_point_balance($site_id, 0, 0, 0, 0, FALSE, FALSE, $target_member_ids);
              $summary["points_balance"] = $points_balance;
              $completed_quizzes = $this->site_model->get_completed_quizzes($site_id, $target_member_ids);
              $summary["quizzes_completed"] = count($completed_quizzes);
              foreach ($completed_quizzes as $completed_quiz) {
                $taken_by_user = $this->account_model->get_user($completed_quiz->user_id);
                $orgs_breakdown[$taken_by_user->retailer_id]["quizzes_taken"]++;
                if ($completed_quiz->correct_percent > 0) {
                  $orgs_breakdown[$taken_by_user->retailer_id]["total_quiz_score"] += $completed_quiz->correct_percent;
                }
                if ($completed_quiz->points_earned > 0) {
                  $orgs_breakdown[$taken_by_user->retailer_id]["points_awarded"] += $completed_quiz->points_earned;
                  $orgs_breakdown[$taken_by_user->retailer_id]["cost"] += $this->site_model->get_withdrew_points_worth($site_id, $completed_quiz->create_timestamp);
                }
              }
              $trained_members = 0;
              $total_score = 0;
              $points_awarded = 0;
              $cost = 0;
              foreach ($orgs_breakdown as $id => $org) {
                if ($org["quizzes_taken"] > 0) {
                  $orgs_breakdown[$id]["avg_quiz_score"] = number_format(($org["total_quiz_score"] / $org["quizzes_taken"]), 2)."%";
                }
                $orgs_breakdown[$id]["training_time"] = number_format(($org["training_time"] / 3600), 2)." hrs";
                if ($org["total_quiz_score"] > 0) {
                  $total_score += $org["total_quiz_score"];
                }
                unset($orgs_breakdown[$id]["total_quiz_score"]);
                if ($org["members_trained"] > 0) {
                  $trained_members += $org["members_trained"];
                }
                if ($org["points_awarded"] > 0) {
                  $points_awarded += $org["points_awarded"];
                }
                if (((float) $org["cost"]) > 0) {
                  $cost += ((float) $org["cost"]);
                  $orgs_breakdown[$id]["cost"] = "$".number_format(((float) $org["cost"]), 2);
                }
              }
              $summary["members_trained"] = $trained_members;
              $summary["avg_quiz_score"] = number_format(($total_score / $summary["quizzes_completed"]), 2)."%";
              $summary["points_awarded"] = $points_awarded;
              $summary["cost"] = "$".number_format(((float) $cost), 2);

              $data["summary"] = $summary;
              $data["orgs_breakdown"] = array_2d_sort($orgs_breakdown, "organization");
            } elseif ($page_number === 2) {
              $report_org = $this->retailer_model->retailer_load($organization_id);
              $targeted_locations = $this->target_model->get_targeted_organization_locations($organization_id, $site_id);
              $locs_breakdown = array();
              foreach ($targeted_locations as $targeted_location) {
                $locs_breakdown[$targeted_location->id] = array(
                    "id" => $targeted_location->id,
                    "location" => $targeted_location->store_name,
                    "state" => $targeted_location->province_name,
                    "city" => $targeted_location->city,
                    "total_members" => 0,
                    "members_trained" => 0,
                    "quizzes_taken" => 0,
                    "training_time" => 0,
                    "total_quiz_score" => 0,
                    "avg_quiz_score" => 0,
                    "points_awarded" => 0,
                    "cost" => 0
                );
              }
              $targeted_members = $this->target_model->get_targeted_members($site_id, FALSE, $targeted_locations);
              $target_member_ids = array();
              foreach ($targeted_members as $targeted_member) {
                $locs_breakdown[$targeted_member->store_id]["total_members"]++;
                $target_member_ids[$targeted_member->user_id] = $targeted_member->user_id;
                if ($this->account_model->is_user_trained($targeted_member->user_id, $site_id)) {
                  $locs_breakdown[$targeted_member->store_id]["training_time"] += $this->account_model->get_user_training_time($site_id, $targeted_member->user_id);
                  $locs_breakdown[$targeted_member->store_id]["members_trained"]++;
                }
              }
              $completed_quizzes = $this->site_model->get_completed_quizzes($site_id, $target_member_ids);
              foreach ($completed_quizzes as $completed_quiz) {
                $taken_by_user = $this->account_model->get_user($completed_quiz->user_id);
                $locs_breakdown[$taken_by_user->store_id]["quizzes_taken"]++;
                if ($completed_quiz->correct_percent > 0) {
                  $locs_breakdown[$taken_by_user->store_id]["total_quiz_score"] += $completed_quiz->correct_percent;
                }
                if ($completed_quiz->points_earned > 0) {
                  $locs_breakdown[$taken_by_user->store_id]["points_awarded"] += $completed_quiz->points_earned;
                  $locs_breakdown[$taken_by_user->store_id]["cost"] += $this->site_model->get_withdrew_points_worth($site_id, $completed_quiz->create_timestamp);
                }
              }
              foreach ($locs_breakdown as $id => $loc) {
                if ($loc["quizzes_taken"] > 0) {
                  $locs_breakdown[$id]["avg_quiz_score"] = number_format(($loc["total_quiz_score"] / $loc["quizzes_taken"]), 2);
                }
                $locs_breakdown[$id]["training_time"] = number_format(($loc["training_time"] / 3600), 2)." hrs";
                unset($locs_breakdown[$id]["total_quiz_score"]);
                if (((float) $loc["cost"]) > 0) {
                  $locs_breakdown[$id]["cost"] = "$".number_format(((float) $loc["cost"]), 2);
                }
              }
              $data["report_org"] = $report_org;
              $data["locs_breakdown"] = array_2d_sort($locs_breakdown, "members_trained", "desc");
            } elseif ($page_number === 3) {
              $report_loc = $this->retailer_model->store_load($location_id);
              $report_org = $this->retailer_model->retailer_load($report_loc->retailer_id);
              $targeted_members = $this->target_model->get_targeted_members($site_id, FALSE, array($report_loc));
              $users_breakdown = array();
              $target_member_ids = array();
              foreach ($targeted_members as $targeted_member) {
                $users_breakdown[$targeted_member->user_id] = array(
                    "id" => $targeted_member->user_id,
                    "member" => $targeted_member->screen_name,
                    "date_joined" => date("y/m/d", $targeted_member->created_on),
                    "logins" => $this->account_model->count_logins($targeted_member->user_id),
                    "quizzes_taken" => 0,
                    "training_time" => $this->account_model->get_user_training_time($site_id, $targeted_member->user_id),
                    "total_quiz_score" => 0,
                    "avg_quiz_score" => 0,
                    "points_awarded" => 0,
                    "cost" => 0
                );
                $target_member_ids[$targeted_member->user_id] = $targeted_member->user_id;
              }
              $completed_quizzes = $this->site_model->get_completed_quizzes($site_id, $target_member_ids);
              foreach ($completed_quizzes as $completed_quiz) {
                $taken_by_user = $this->account_model->get_user($completed_quiz->user_id);
                $users_breakdown[$taken_by_user->user_id]["quizzes_taken"]++;
                if ($completed_quiz->correct_percent > 0) {
                  $users_breakdown[$taken_by_user->user_id]["total_quiz_score"] += $completed_quiz->correct_percent;
                }
                if ($completed_quiz->points_earned > 0) {
                  $users_breakdown[$taken_by_user->user_id]["points_awarded"] += $completed_quiz->points_earned;
                  $users_breakdown[$taken_by_user->user_id]["cost"] += $this->site_model->get_withdrew_points_worth($site_id, $completed_quiz->create_timestamp);
                }
              }
              foreach ($users_breakdown as $id => $member) {
                if ($member["quizzes_taken"] > 0) {
                  $users_breakdown[$id]["avg_quiz_score"] = number_format(($member["total_quiz_score"] / $member["quizzes_taken"]), 2);
                }
                $users_breakdown[$id]["training_time"] = number_format(($member["training_time"] / 60), 2)." mins";
                unset($users_breakdown[$id]["total_quiz_score"]);
                $users_breakdown[$id]["cost"] = "$".number_format(((float) $member["cost"]), 2);
              }
              $data["report_org"] = $report_org;
              $data["report_loc"] = $report_loc;
              $data["users_breakdown"] = array_2d_sort($users_breakdown, "training_time", "desc");
            } elseif ($page_number === 4) {
              $report_user = $this->account_model->get_user($user_id);
              $report_loc = $this->retailer_model->store_load($report_user->store_id);
              $report_org = $this->retailer_model->retailer_load($report_user->retailer_id);
              $completed_quizzes = $this->site_model->get_completed_quizzes($site_id, array($user_id));
              $quizzes_breakdown = array();
              foreach ($completed_quizzes as $completed_quiz) {
                $quizzes_breakdown[$completed_quiz->id] = array(
                    "training_date" =>date("y/m/d", $completed_quiz->finish_timestamp),
                    "lab" => $completed_quiz->lab,
                    "quiz_type" => $completed_quiz->quiz_type,
                    "training_time" => number_format(($completed_quiz->time_spent / 60), 2)." mins",
                    "quiz_score" => $completed_quiz->correct_percent,
                    "points_awarded" => 0,
                    "cost" => 0
                );
                if ($completed_quiz->points_earned > 0) {
                  $quizzes_breakdown[$completed_quiz->id]["points_awarded"] = $completed_quiz->points_earned;
                  $quizzes_breakdown[$completed_quiz->id]["cost"] = $this->site_model->get_withdrew_points_worth($site_id, $completed_quiz->create_timestamp);
                }
                $quizzes_breakdown[$completed_quiz->id]["cost"] = "$".number_format($quizzes_breakdown[$completed_quiz->id]["cost"], 2);
              }
              $data["report_org"] = $report_org;
              $data["report_loc"] = $report_loc;
              $data["report_user"] = $report_user;
              $data["quizzes_breakdown"] = array_2d_sort($quizzes_breakdown, "training_time", "desc");
            }
            
            return array('content' => $this->render('widget_report', $data),
                'css' =>  $this->config->item('css','target'),
                'js' =>  $this->config->item('js','target'));
          }
        } else {
          return "<p>You don't have permission to view this page.</p>";
        }

        if ($data['environment'] == 'admin_panel') {
            return array('content' => '<p>This is an empty ' . $module_title . ' widget.<br />Click here to edit.</p>');
        }
    }
}
