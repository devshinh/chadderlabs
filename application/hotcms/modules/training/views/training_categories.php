<div class="trainng-categories-wrapper">
<div class="leftColumn">
  <h2>Training Categories</h2>

  <?php foreach ($categories as $cat) { ?>
    <div class="category">
      <div class="category_header"><div class="category_name"><?php echo $cat->name; ?></div>
        <?php if ($cat->system_generated != 1) { ?>
          <div class="category_actions">
            <a href="<?php echo $cat->id ?>" class="edit_category_name">
              <div class="btn-edit"></div>
            </a>     
            <a class="category_delete" href="/hotcms/training/category_delete/<?php echo $cat->id ?>" onclick="return confirmDelete()">
              <div class="btn-delete"></div>
            </a>
          </div>
        <?php } ?>
      </div>
      <div></div>
      <div class="table">
        <table>
          <tr>
            <th>Subcategory</th>
            <th>Template</th>
            <th class="action">Edit</th>
            <th class="action">Delete</th>
          </tr>
          <?php
          foreach ($subcategories as $s) {
            for ($i = 0; $i < count($s); $i++) {
              if ($cat->id == $s[$i]->parent_id) {
                ?>
                <tr class="subcategory">
                  <?php
                  $edit_link = sprintf('<a class="edit_subcategory" href="%s" class="edit_subcategory"><div class="btn-edit"></div></a>', $s[$i]->id);
                  $delete_link = sprintf('<a class="delete_subcategory" href="/hotcms/training/category_delete/%s" onclick="return confirmDelete()"><div class="btn-delete"></div></a>', $s[$i]->id);
                  printf('<td>%s</td><td>%s</td><td class="action">%s</td><td class="last action">%s</td>', $s[$i]->name, $s[$i]->template_id, $edit_link, $delete_link);
                  ?>
                </tr>
                <?php
              }
            }
          }
          ?>
        </table>
      </div>
      <?php printf('<a class="red_button_smaller add_subcategory" href="/hotcms/training/subcategory_add/%s">Add new subcategory</a>', $cat->id); ?>
    </div>
  <?php } ?>

  <a class="category_add red_button"href="<?php printf('/hotcms/%s/category_add', $module_url) ?>">Add category</a>
</div>
<div class="rightColumn">
</div>
<div class="clearfix"></div>
</div>