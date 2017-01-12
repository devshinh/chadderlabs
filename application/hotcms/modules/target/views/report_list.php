<?php
if ( !empty($reports)) {
  print '\n<div class="hero-unit">';
  if (count($reports) == 1) {
    print "\n<h1>Report</h1>";
  } else {
    print "\n<h1>Reports</h1>";
  }
  foreach ($reports as $report) {
    print "\n<p>";
    print '\n<a href="report/site--'.$report->id.'">'.$report->name."</a>";
    print "\n</p>";
  }
  print "\n</div>";
}
?>
