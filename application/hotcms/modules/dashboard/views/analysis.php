<script type="text/javascript">
  var clientId = '872379554084-mp29qcq18ip81rn24iqfhte5ocm5dfmp.apps.googleusercontent.com';
  var apiKey = 'AIzaSyC1zzCH-xeXOQcJrI2OXhvKLu-IuEMAHPY';
  var scopes = 'https://www.googleapis.com/auth/analytics.readonly';

  // This function is called after the Client Library has finished loading
  function handleClientLoad() {
    // 1. Set the API Key
    console.log('client loaded');
    gapi.client.setApiKey(apiKey);

    // 2. Call the function that checks if the user is Authenticated. This is defined in the next section
    window.setTimeout(checkAuth,1);
  }

  function checkAuth() {
    // Call the Google Accounts Service to determine the current user's auth status.
    // Pass the response to the handleAuthResult callback function
    gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
  }

  function handleAuthResult(authResult) {
    console.log(authResult);
    if (authResult) {
      // The user has authorized access
      // Load the Analytics Client. This function is defined in the next section.
      loadAnalyticsClient();
    } else {
      // User has not Authenticated and Authorized
      handleUnAuthorized(authResult);
    }
  }

  // Authorized user
  function handleAuthorized() {
    console.log('API loaded');
    makeApiCall();
  }

  // Unauthorized user
  function handleUnAuthorized(authResult) {
    console.log('not authorized');
  }

  function loadAnalyticsClient() {
    console.log('authorized');
    // Load the Analytics client and set handleAuthorized as the callback function
    gapi.client.load('analytics', 'v3', handleAuthorized);
  }

  function makeApiCall() {
    var apiQuery = gapi.client.analytics.data.ga.get({
      'ids': 'ga:66655168',
//      'ids': '<?php // echo $google_analytic_view_id; ?>',
      'metrics': 'ga:timeOnPage',
      'filters': 'ga:pagePath=~^/labs.*,ga:pagePath=~^/quiz.*<?php
      if ($site_id > 1) {
        echo ";ga:hostname==".$domain;
      }
      foreach(array('country', 'region', 'city') as $filter) {
        if (isset($$filter) && ( ! empty($$filter) && (strcasecmp($$filter, "all") != 0 ))) {
          echo ";ga:".$filter."==".$$filter;
        }
      }
      ?>',
      'start-date': '<?=date("Y-m-d", strtotime($start_date))?>',
      'end-date': '<?=date("Y-m-d", strtotime($end_date))?>'
    });
    apiQuery.execute(handleCoreReportingResults);
  }
  
  function handleCoreReportingResults(results) {
    console.log(results);
    if (typeof results.rows != 'undefined') {
      jQuery('#labHours').text((results.rows[0][0] / 3600).toFixed(1));
    }
  }
</script>
<script src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>
<?php echo form_open('dashboard/analysis', array('id' => 'analysis_form')); ?>
  <!--<h2 class="welcome">Lab Analysis: Dashboard: Client Name</h2>-->
  <div id="dashboard_stats">
    <div class="stats">
      <div class="stats_label">Brand</div>
      <?php if (is_numeric($brand)) { ?>
      <div class="stats_detail"><?=$brand?></div>
      <?php } else { ?>
      <div class="stats_detail brand"><?=$brand?></div>
      <?php } ?>
    </div> 
    <div class="stats">
      <div class="stats_label">Active Retailers</div>
      <div class="stats_detail"><a href="/hotcms/retailer"><?php echo number_format($retailers_count); ?></a></div>
    </div>
    <div class="stats">
      <div class="stats_label">Active Locations</div>
      <div class="stats_detail"><?php echo number_format($active_locations_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Pending Locations</div>
      <div class="stats_detail"><?php echo number_format($pending_locations_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Members</div>
      <div class="stats_detail"><a href="/hotcms/user"><?php echo number_format($members_count); ?></a></div>
    </div>     
    <div class="stats">
      <div class="stats_label">Active Labs</div>
      <div class="stats_detail"><?php echo number_format($lab_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Lab Hours</div>
      <!--<div class="stats_detail"><?php echo number_format($lab_hours_count); ?></div>-->
      <div id="labHours" class="stats_detail">0</div>
    </div>   
    <div class="stats">
      <div class="stats_label">Active Quizzes</div>
      <div class="stats_detail"><?php echo number_format($active_quiz_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Quiz Hours</div>
      <div class="stats_detail"><?php echo number_format($quiz_result_time_sum,1); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Quizzes Completed</div>
      <div class="stats_detail"><?php echo number_format($completed_quiz_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Average Quiz Time</div>
      <div class="stats_detail"><?php echo number_format($quiz_time_avg); ?> seconds</div>
    </div>
    <div class="stats">
      <div class="stats_label">Average Quiz Score</div>
      <div class="stats_detail"><?php echo number_format($quiz_score_avg, 1); ?>%</div>
    </div>
    <div class="stats">
      <div class="stats_label">Average Quiz Points</div>
      <div class="stats_detail"><?php echo number_format($quiz_points_avg, 2); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Points Awarded</div>
      <div class="stats_detail"><?php echo number_format($point_awarded); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Points Redeemed</div>
      <div class="stats_detail"><?php echo number_format($point_redeemed); ?></div>
    </div>        
    <div class="stats">
      <div class="stats_label">Store Orders</div>
      <div class="stats_detail"><a href="/hotcms/order"><?php echo number_format($order_count); ?></a></div>
    </div>
    <div class="stats">
      <div class="stats_label">Contest Entries</div>
      <div class="stats_detail"><?php echo number_format($draws_sum); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Colleagues Referred</div>
      <div class="stats_detail"><?php echo number_format($referral_count); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Referral Conversion</div>
      <div class="stats_detail"><?php echo number_format($referral_converson, 1); ?>%</div>
    </div>
    <div class="stats">
      <div class="stats_label">Badges Awarded</div>
      <div class="stats_detail"><?php echo number_format($badges_awarded); ?></div>
    </div>
    <div class="stats">
      <div class="stats_label">Member Sessions</div>
      <div class="stats_detail"><?php echo number_format($member_sessions); ?></div>
    </div>
    <div class="clear"></div>
  </div>

  <script type="text/javascript">
    console.log('chart data');
    console.log(<?php echo $chart_data; ?>);
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = new google.visualization.DataTable(<?php echo $chart_data; ?>);
      var options = {
        chartArea: {width: '90%', height: '90%'},
        legend: {position: "none"},
        tooltip: {textStyle: {color: '#FF0000'}, showColorCode: true, trigger: "focus"},
        hAxis: {textStyle: {fontSize: 11}},
        vAxis: {textStyle: {fontSize: 11}, logScale: true},
        hight: 510,
        width: 750
      };
      var chart = new google.visualization.ColumnChart(document.getElementById('chart_graph')).draw(data, options);
    }
  </script>
  
  <div id="dashboard_charts">
    <div id="report_chart">
      <div class="stats_label">Select Report: Quizzes Taken Per Month</div>
      <div id="chart_graph"></div>
    </div>
    <div id="report_filters">
      <div class="stats_label">Filters</div>
      <div id="filter_form_div">
        <div class="row"><label>Date</label></div>
        <div class="row date_range"><input type="text" name="from_filter_range" id="from_filter_range" value="<?=set_value("from_filter_range")?>" /> to <input type="text" name="to_filter_range" id="to_filter_range" value="<?=set_value("to_filter_range")?>" /></div>
        <div class="row"><label>Country</label></div>
        <div class="row"><select name="country" id="country">
          <?php
          foreach ($countries as $k => $v) {
            echo '<option value="' . $k . '" ' . set_select("country", $k) . '>' . $v . '</option>';
          }
          ?>
        </select></div>
        <div class="row"><label>Retailer</label></div>
        <div class="row"><select name="retailer" id="retailer">
          <?php
          foreach ($retailers as $k => $v) {
            echo '<option value="' . $k . '" ' . set_select("retailer", $k) . '>' . $v . '</option>';
          }
          ?>
        </select></div>
        <div class="row"><label>State/Province</label></div>
        <div class="row"><select name="province" id="province">
          <?php
          foreach ($provinces as $k => $v) {
            echo '<option value="' . $k . '" ' . set_select("province", $k) . '>' . $v . '</option>';
          }
          ?>
        </select></div>
        <div class="row"><label>City</label></div>
        <div class="row"><select name="city" id="city">
          <?php
          foreach ($cities as $k => $v) {
            echo '<option value="' . $k . '" ' . set_select("city", $k) . '>' . $v . '</option>';
          }
          ?>
        </select></div>
        <div class="row"><label>Store</label></div>
        <div class="row"><select name="store" id="store">
          <?php
          foreach ($stores as $k => $v) {
            echo '<option value="' . $k . '" ' . set_select("store", $k) . '>' . $v . '</option>';
          }
          ?>
        </select></div>
<!--        <div class="row"><label>Benchmarking</label></div>
        <div class="row"><input type="checkbox" name="benchmarking" id="benchmarking" /> <label id="benchmarking" for="benchmarking">Show</label></div>-->
        <div class="row">
          <input type="submit" name="update" value="Update" class="red_button" />
          <input type="submit" name="reset" value="Reset" class="red_button" />
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div>

<?php echo form_close(); ?>
