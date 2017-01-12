<div>
  <h2>Training Templates</h2>

  <div class="row">
    <form action="/hotcms/<?php echo $module_url ?>/template_reload" method="post" name="category">
      <?php echo form_error('training_category', '<div class="error">', '</div>'); ?>
      <?php
      $add = 'onChange="document.category.submit()"';
      echo form_dropdown("training_category", $categories_dropdown, $selected_category, $add);
      ?>
    </form>
  </div>
  <div class="trainng-templates-wrapper">
  <div class="leftColumn">
    <h2>Training Item Tags</h2>
    <?php
    if (!empty($type_tags_main)) {
      foreach ($type_tags_main as $type) {
        ?>
        <div class="tag_type">
          <div class="module_header"><div class="type_tag_name"><?php echo $type->type_name; ?></div>
            <div class="type_tag_actions">
              <a href="<?php echo $type->id ?>" class="edit_type_tag_name">
                <div class="btn-edit"></div>
              </a>
              <a href="/hotcms/training/tag_type_delete/<?php echo $type->id ?>/<?php echo $selected_category ?>" onclick="return confirmDelete()">
                <div class="btn-delete"></div>
              </a>
            </div>
          </div>
          <div></div>
          <div class="table">
            <table>
              <tr>
                <th>Name</th>
                <th class="action">Edit</th>
                <th class="action">Delete</th>
              </tr>
              <?php
              foreach ($type_tags_sub as $s) {
                for ($i = 0; $i < count($s); $i++) {
                  if ($type->id == $s[$i]->type_id) {
                    ?>
                    <tr>
                      <?php
                      $edit_link = sprintf('<a href="%s" class="edit_tag"><div class="btn-edit"></div></a>', $s[$i]->id);
                      $delete_link = sprintf('<a href="/hotcms/training/tag_delete/%s/%s" onclick="return confirmDelete()"><div class="btn-delete"></div></a>', $s[$i]->id, $selected_category);
                      printf('<td>%s</td><td class="action">%s</td><td class=" action last">%s</td>', $s[$i]->name, $edit_link, $delete_link);
                      ?>
                    </tr>
                    <?php
                  }
                }
              }
              ?>
            </table>
          </div>
          <div class="row">
            <a href="<?php printf('/hotcms/%s/tag_type_add_child/%s/%s', $module_url, $type->id, $selected_category) ?>" class="red_button_smaller">Add new tag</a>
          </div>
        </div>
        <?php
      }
    }
    ?>
    <div class="row">
      <a href="<?php printf('/hotcms/%s/tag_type_add/%s', $module_url, $selected_category) ?>" class="red_button">Add new tag type</a>
    </div>
  </div>
  <div class="rightColumn">
    <h2>Variant Information</h2>
    <div class="droppable-zone">
    <?php
    if (!empty($variant_fields)) {
      foreach ($variant_fields as $field) {
        if ($field->visible == FALSE) {
          continue;
        }
        echo '<div class="variant_type row editable cloneable ui-draggable" id="field_id_' . $field->id . '">';
        echo field_config_ui($field);
        echo '</div>';
      }
    }
    ?>
    </div>
    <div class="row">
        <a href="#" class="red_button add_variant_type_link">Add New Variant Type</a>
    </div>
  </div>
  </div>
  <div class="clear"></div>
</div>

<div id="variant-type-form" title="Create new Variant Type">
	<p class="validateTips">All form fields are required.</p>
	<form id="new_variant_type_form">
	<fieldset>
    <div class="row">
      <label for="field_name">Name: </label>
      <input type="text" name="field_name" id="field_name" class="text ui-widget-content ui-corner-all required" />
    </div>
    <div class="row">
      <label for="field_type">Field Type: </label>
      <select name="field_type" id="field_type" class="required">
        <option value=""> -- Select Field Type -- </option>
        <?php
        foreach($field_types as $k => $v ) {
          echo '<option value="' . $k . '">' . $v . '</option>';
        }
        ?>
      </select>
    </div>
	</fieldset>
	</form>
</div>