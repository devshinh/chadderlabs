<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#reportTable').dataTable( {
            "columnDefs": [
                { "type": "numeric-comma", targets: 3 }
            ]
        } );
    } );
</script>

<?php
if (isset($summary) && ( !empty($summary))) {
  print '<div class="page-header">';
  print "<h1>".$report_site->name." <small>summary</small><h1>";
  print "</div>";
  $i = 1;
  foreach ($summary as $key => $value) {
    if ($i > 4) {
      print "</div>";
      $i = 1;
    }
    if ($i === 1) {
      print '<div class="row-fluid">';
    }
    print '<div class="span3">';
    print '<div class="hero-unit well">';
    print "<h4>".strtoupper(str_replace("_", " ", $key))."</h4>";
    print "<p>".$value."</p>";
    print "</div>";
    print "</div>";
    $i++;
  }
    print "</div>";
}
if (isset($orgs_breakdown) && ( !empty($orgs_breakdown))) {
  print '<div class="page-header">';
  print "<h1><small>organizations</small></h1>";
  print "</div>";
  print '<table class="table dataTable" id="reportTable">';
  print "<thead>";
  print "<tr>";
  foreach (array_shift(array_slice($orgs_breakdown, 0, 1)) as $key => $value) {
    if (strcasecmp($key, "id") !== 0) {
      print "<th>".ucfirst(str_replace("_", " ", $key))."</th>";
    }
  }
  print "</tr>";
  print "</thead>";
  print '<tbody title="click for locations report of this organization">';

  foreach ($orgs_breakdown as $org_id => $org) { ?>
<tr onclick="document.location = 'report/organization--<?=$org["id"]?>--<?=$report_site->id?>'"> <?php
    foreach ($org as $key => $value) {
      if (strcasecmp($key, "id") !== 0) {
        print "<td>".$value."</td>";
      }
    }
    print "</tr>";
  }
  print "</tbody>";
  print "</table>";
}
if (isset($locs_breakdown) && ( !empty($locs_breakdown))) {
  print '<div class="page-header">';
  print '<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name."</a><small> ".$report_org->name."</small><h1>";
  print "</div>";
  print '<table class="table dataTable" id="reportTable">';
  print "<thead>";
  print "<tr>";
  foreach (array_shift(array_slice($locs_breakdown, 0, 1)) as $key => $value) {
    if (strcasecmp($key, "id") !== 0) {
      print "<th>".ucfirst(str_replace("_", " ", $key))."</th>";
    }
  }
  print "</tr>";
  print "</thead>";
  print '<tbody title="click for members report of this location">';
  foreach ($locs_breakdown as $loc) { ?>
<tr onclick="document.location = 'report/location--<?=$loc["id"]?>--<?=$report_site->id?>'"> <?php
    foreach ($loc as $key => $value) {
      if (strcasecmp($key, "id") !== 0) {
        print "<td>".$value."</td>";
      }
    }
    print "</tr>";
  }
  print "</tbody>";
  print "</table>";
}
if (isset($users_breakdown) && ( !empty($users_breakdown))) {
  print '<div class="page-header">';
  print '<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name.'</a><small> <a href="report/organization--'.$report_org->id.'--'.$report_site->id.'">'.$report_org->name."</a> / ".$report_loc->store_name."</small><h1>";
  print "</div>";
  print '<table class="table dataTable" id="reportTable">';
  print "<thead>";
  print "<tr>";
  foreach (array_shift(array_slice($users_breakdown, 0, 1)) as $key => $value) {
    if (strcasecmp($key, "id") !== 0) {
      print "<th>".ucfirst(str_replace("_", " ", $key))."</th>";
    }
  }
  print "</tr>";
  print "</thead>";
  print '<tbody title="click for quizzes report of this member if he/she has taken any.">';
  foreach ($users_breakdown as $user) {
    if ($user["quizzes_taken"] > 0) { ?>
<tr onclick="document.location = 'report/member--<?=$user["id"]?>--<?=$report_site->id?>'"> <?php
    } else {
      print "<tr>";
    }
    foreach ($user as $key => $value) {
      if (strcasecmp($key, "id") !== 0) {
        print "<td>".$value."</td>";
      }
    }
    print "</tr>";
  }
  print "</tbody>";
  print "</table>";
}
if (isset($quizzes_breakdown) && ( !empty($quizzes_breakdown))) {
  print '<div class="page-header">';
  print '<h1><a href="report/site--'.$report_site->id.'">'.$report_site->name.'</a><small> <a href="report/organization--'.$report_org->id.'--'.$report_site->id.'">'.$report_org->name.'</a> / <a href="report/location--'.$report_loc->id.'--'.$report_site->id.'">'.$report_loc->store_name."</a> / ".$report_user->screen_name."</small><h1>";
  print "</div>";
  print '<table class="table table-striped">';
  print "<thead>";
  print "<tr>";
  foreach (array_shift(array_slice($quizzes_breakdown, 0, 1)) as $key => $value) {
    print "<th>".ucfirst(str_replace("_", " ", $key))."</th>";
  }
  print "</tr>";
  print "</thead>";
  print "<tbody>";
  foreach ($quizzes_breakdown as $quiz) {
    print "</tr>";
    foreach ($quiz as $value) {
      print "<td>".$value."</td>";
    }
    print "</tr>";
  }
  print "</tbody>";
  print "</table>";
}
?>
