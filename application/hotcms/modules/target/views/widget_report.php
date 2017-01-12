<?php
if (isset($summary) && ( !empty($summary))) {
  print '\n<div class="page-header">';
  print "\n<h1>".$report_site->name."<small> summary</small><h1>";
  print "\n</div>";
  $i = 1;
  foreach ($summary as $key => $value) {
    if ($i > 4) {
      $i = 1;
    }
    if ($i === 1) {
      print '\n<div class="row-fluid">';
    }
    print '\n<div class="span4">';
    print '\n<div class="hero-unit">';
    print "\n<h1>".$key."</h1>";
    print "\n<p>".$value."</p>";
    print "\n</div>";
    print "\n</div>";
    $i++;
    if ($i === 4) {
      print "\n</div>";
    }
  }
}
if (isset($orgs_breakdown) && ( !empty($orgs_breakdown))) {
  print '\n<div class="page-header">';
  print "\n<h1><small>organizations</small><h1>";
  print "\n</div>";
  print '\n<table class="table table-striped">';
  print "\n<thead>";
  print "\n<tr>";
  foreach (array_shift(array_slice($orgs_breakdown, 0, 1)) as $key => $value) {
    print "\n<th>".str_replace("_", " ", $key)."</th>";
  }
  print "\n</tr>";
  print "\n</thead>";
  print "\n<tbody>";
  foreach ($orgs_breakdown as $org_id => $org) { ?>
<tr onclick="document.location = 'report/organization--<?=$org_id?>'"> <?php
    foreach ($org as $value) {
      print "\n<td>".$value."</tr>";
    }
    print "\n</tr>";
  }
  print "\n</tbody>";
  print "\n</table>";
}
if (isset($locs_breakdown) && ( !empty($locs_breakdown))) {
  print '\n<div class="page-header">';
  print '\n<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name."</a><small>".$report_org->name."</small><h1>";
  print "\n</div>";
  print '\n<table class="table table-striped">';
  print "\n<thead>";
  print "\n<tr>";
  foreach (array_shift(array_slice($locs_breakdown, 0, 1)) as $key => $value) {
    print "\n<th>".str_replace("_", " ", $key)."</th>";
  }
  print "\n</tr>";
  print "\n</thead>";
  print "\n<tbody>";
  foreach ($locs_breakdown as $loc_id => $loc) { ?>
<tr onclick="document.location = 'report/location--<?=$loc_id?>'"> <?php
    foreach ($loc as $value) {
      print "\n<td>".$value."</tr>";
    }
    print "\n</tr>";
  }
  print "\n</tbody>";
  print "\n</table>";
}
if (isset($users_breakdown) && ( !empty($users_breakdown))) {
  print '\n<div class="page-header">';
  print '\n<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name.'</a><small><a href="report/organization--'.$report_site->id.'">'.$report_org->name."</a> / ".$report_loc->store_name."</small><h1>";
  print "\n</div>";
  print '\n<table class="table table-striped">';
  print "\n<thead>";
  print "\n<tr>";
  foreach (array_shift(array_slice($users_breakdown, 0, 1)) as $key => $value) {
    print "\n<th>".str_replace("_", " ", $key)."</th>";
  }
  print "\n</tr>";
  print "\n</thead>";
  print "\n<tbody>";
  foreach ($users_breakdown as $user_id => $user) { ?>
<tr onclick="document.location = 'report/member--<?=$user_id?>'"> <?php
    foreach ($user as $value) {
      print "\n<td>".$value."</tr>";
    }
    print "\n</tr>";
  }
  print "\n</tbody>";
  print "\n</table>";
}
if (isset($quizzes_breakdown) && ( !empty($quizzes_breakdown))) {
  print '\n<div class="page-header">';
  print '\n<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name.'</a><small><a href="report/organization--'.$report_site->id.'">'.$report_org->name.'</a> / <a href="report/location--'.$report_loc->id.'">'.$report_loc->store_name."</a> / ".$report_user->screen_name."</small><h1>";
  print "\n</div>";
  print '\n<table class="table table-striped">';
  print "\n<thead>";
  print "\n<tr>";
  foreach (array_shift(array_slice($quizzes_breakdown, 0, 1)) as $key => $value) {
    print "\n<th>".str_replace("_", " ", $key)."</th>";
  }
  print "\n</tr>";
  print "\n</thead>";
  print "\n<tbody>";
  foreach ($quizzes_breakdown as $quiz) {
    print "\n</tr>";
    foreach ($quiz as $value) {
      print "\n<td>".$value."</tr>";
    }
    print "\n</tr>";
  }
  print "\n</tbody>";
  print "\n</table>";
}
?>
