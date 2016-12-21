<div class="module">
<h2><a class="red_button" href="<?php printf('/hotcms/%s',$module_url) ?>/create"><?php printf('%s', $add_new_text) ?></a></h2>
<?php if (!empty( $aCurrent )){ ?>
<div class="table">
  <table id="tableCurrent" class="tablesorter">
    <thead>
      <tr>
        <th></th>
        <th><?php echo lang( 'hotcms_name' )?></th>
        <th><?php echo lang( 'hotcms_type' )?></th>
        <th><?php echo lang( 'hotcms_date_updated' )?></th>
        <th><?php echo lang( 'hotcms_date_created' )?></th>
        <th><?php echo lang( 'hotcms_category' )?></th>
        <th><?php echo lang( 'hotcms_actions' )?></th>
      </tr>
    </thead>
    <tbody>
  <?php foreach ($aCurrent as $image){ ?>
      <tr id="trData_<?php echo $image->id ?>">
       <td>
        <?php 
        switch ($image->type){
         case(1): printf('<img src="http://%s%s/%s.%s" alt="%s" />', $this->session->userdata( 'siteURL' ), $image->thumbnail,$image->file_name.'_thumb', $image->extension, $image->file_name);
                  break;
         case(3): printf('<img src="http://%s/application/hotcms/asset/images/icons/page_white_acrobat.png" alt="document_icon" />', $this->session->userdata( 'siteURL' ));
                  break;
         default: echo '&nbsp';
                  break;
        }

        ?>
       </td>
        <td>
         <?php echo $image->name ?>
        </td>
        <td>
          <?php switch($image->type){
              case(1): $type = 'image';break;
              case(2): $type = 'video';break;
              case(3): $type = 'document';break;
              default: $type = 'N/A';
             }
           echo $type;
             ?>

        </td>
        <td>
         <?php if (!empty( $image->update_date )){ echo $image->update_date; }else{ ?>&mdash;<?php } ?>
        </td>
        <td>
         <?php if (!empty( $image->create_date )){ echo $image->create_date; }else{ ?>&mdash;<?php } ?>
        </td>
        <td>
         <?php if (!empty( $image->category_name )){ echo $image->category_name; }else{ ?>&mdash;<?php } ?>
        </td>        
        <td>
           <a href="<?php printf('/hotcms/%s/edit/%s', $module_url, $image->id)?>"><img src="asset/images/actions/edit.png" alt="Edit" /></a>
           <a onClick="return confirmDelete()" href="<?php printf('/hotcms/%s/delete/%s', $module_url, $image->id)?>"><img src="asset/images/actions/delete.png" alt="Delete" /></a>
        </td>
      </tr>
  <?php } ?>
    </tbody>
  </table>
  <?php echo $pagination; ?>
</div>
<?php } ?>
</div>
