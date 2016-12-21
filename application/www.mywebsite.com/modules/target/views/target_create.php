<div>
  <?=form_open($module_url."/create", array("id" => "targetCreate"), $form["hidden"])?>
  <div class="row">
    <div class="col-1-half">
      <div class="row">
        <?=form_label(lang("hotcms_target")." ".lang("hotcms_name").lang("hotcms__colon"), "name")?>
        <?=form_input($form["name_input"])?>
      </div>
      <div class="row">
        <?=form_label(lang("hotcms_for")." ".lang("hotcms_site").lang("hotcms__colon"), "site_id")?>
        <?=form_dropdown("site_id", $form["site_options"], $form["selected_site"], 'class="select2"')?>
      </div>
      <div class="row">
        <?=form_label(lang("hotcms_description").lang("hotcms__colon"), "description")?>
        <?=form_input($form["description_input"])?>
      </div>
      <div class="row">
        <?=form_label(lang("hotcms_types").lang("hotcms__colon"), "types[]")?>
        <?=form_multiselect("types[]", $form["type_options"], $form["selected_types"], 'id="types" class="orgFilters select2"')?>
      </div>
      <div class="row">
        <?=form_label(lang("hotcms_categories").lang("hotcms__colon"), "categories[]")?>
        <?=form_multiselect("categories[]", $form["category_options"], $form["selected_categories"], 'id="categories" class="orgFilters select2"')?>
      </div>
      <div class="row">
        <?=form_label(lang("hotcms_job_titles").lang("hotcms__colon"), "job_titles[]")?>
        <?=form_multiselect("job_titles[]", $form["job_title_options"], $form["selected_job_titles"], 'id="job_titles" class="select2"')?>
      </div>
      <div class="submit">
        <input type="submit" class="red_button" value="<?=lang("hotcms_save")?>" />
        <a href="/hotcms/<?=$module_url?>/index/<?=$index_page_num?>" class="red_button"><?=lang('hotcms_back')?></a>
        <?=form_hidden('hdnMode', 'insert')?>
      </div>
    </div>
    <div id="target_jstree" class="col-1-half"></div>
  </div>
  <?=form_close()?>
</div>
<script type="text/javascript">
  function storeTree(json_tree) {
    return jQuery('#target_jstree').jstree({
      'json_data' : {
        'data' : json_tree
      },
      'plugins' : ['checkbox', 'json_data', 'themes', 'ui'],
      'themes' : {
        'icons' : false
      }
    });
  }
  jQuery('document').ready(function() {
    var treeObj = storeTree(<?=json_encode($form["stores_tree"])?>);
    treeObj.bind('loaded.jstree', function() {
      console.log('loaded');
      console.log(treeObj.jstree('get_settings'));
    });
    jQuery('.select2').select2({
      placeholder: 'click here to select'
    });
    treeObj.bind('check_node.jstree uncheck_node.jstree check_all.jstree', function() {
      var newChosenOrgs = '';
      var newChosenStores = '';
      jQuery(".nodeOrg.jstree-checked").each(function() {
        newChosenOrgs += jQuery(this).data("id") + ',';
      });
      if ( !(!jQuery.trim(newChosenOrgs))) { // js valid empty string as false
        newChosenOrgs = jQuery.trim(newChosenOrgs).slice(0, -1);
      }
      console.log("(un)checking orgs " + newChosenOrgs);
      jQuery('input[name="organizations"]').val(newChosenOrgs);
      jQuery(".nodeOrg").not(".jstree-checked").find(".nodeStore.jstree-checked").each(function() {
        newChosenStores += jQuery(this).data("id") + ',';
      });
      if ( !(!jQuery.trim(newChosenStores))) {
        newChosenStores = jQuery.trim(newChosenStores).slice(0, -1);
      }
      console.log("(un)checking stores " + newChosenStores);
      jQuery('input[name="stores"]').val(newChosenStores);
    });
    jQuery('.orgFilters').change(function() {
      console.log('time to update '+jQuery(this).val());
      jQuery.ajax({
        data: jQuery('#targetCreate').serialize(),
        dataType: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
          console.log('response: ' + jqXHR.responseText);
          console.log('status: ' + textStatus);
          console.log('http error: ' + errorThrown);
        },
        success: function(data) {
          treeObj = storeTree(data['stores_tree']);
          treeObj.on('loaded.jstree', function() {
            console.log('loaded');
            console.log(treeObj.jstree('get_settings'));
          });
          treeObj.on('check_node.jstree uncheck_node.jstree check_all.jstree', function() {
            var newChosenOrgs = '';
            var newChosenStores = '';
            jQuery(".nodeOrg.jstree-checked").each(function() {
              newChosenOrgs += jQuery(this).data("id") + ',';
            });
            if ( !(!jQuery.trim(newChosenOrgs))) { // js valid empty string as false
              newChosenOrgs = jQuery.trim(newChosenOrgs).slice(0, -1);
            }
            console.log("(un)checking orgs " + newChosenOrgs);
            jQuery('input[name="organizations"]').val(newChosenOrgs);
            jQuery(".nodeOrg").not(".jstree-checked").find(".nodeStore.jstree-checked").each(function() {
              newChosenStores += jQuery(this).data("id") + ',';
            });
            if ( !(!jQuery.trim(newChosenStores))) {
              newChosenStores = jQuery.trim(newChosenStores).slice(0, -1);
            }
            console.log("(un)checking stores " + newChosenStores);
            jQuery('input[name="stores"]').val(newChosenStores);
          });
          jQuery('input[name="organizations"]').val(data['organizations']);
          jQuery('input[name="stores"]').val(data['stores']);
          console.log('updated');
          console.log(treeObj.jstree('get_settings'));
        },
        type: "post",
        url: "/hotcms/<?=$module_url?>/ajax_tree_refresh/" + Math.random()*99999
      });
    });
  });
</script>