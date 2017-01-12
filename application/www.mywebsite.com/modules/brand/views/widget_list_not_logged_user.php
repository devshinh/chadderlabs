<br/>
<div class="row-fluid" id="retailers-list">
    
        <div id="filters_div">
            <form id="search_form" method="post">
                <input type="hidden" value="active_store_count" name="sort_by">
                <input type="hidden" value="desc" name="sort_direction">
            </form>
        </div>    
    
    <div class="media tableHeader">
        <div id="sortable_name" class="pull-left sortable 
    <?php if ($sorting['sort_by'] == 'name') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>         
             "><?=lang("hotcms_organization")?></div>
        <div  id="sortable_active_store_count" class="media-body pull-right sortable
    <?php if ($sorting['sort_by'] == 'active_store_count') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>                
              "># of locations</div>
        
    </div>
    <?php foreach ($retailers as $row) {
                if(!empty($row->logo)){
                    if(strlen($row->name) < 20){
                        echo '<div class="media tableRow">';
                    }else{
                        echo '<div class="media tableRow2">';        
                    }
                    echo '<div class="pull-left"><div><div style="margin-bottom:15px;margin-left:15px;width:75px;float:left;">'.$row->logo->full_html.'</div>';
                    echo '<div style="margin-left:110px;text-align:left;"><a href="/retailer/'.  strtolower(url_title($row->name)).'">'.$row->name.'</a></div></div></div>';
                }else{
                    echo '<div class="media tableRow">';
                     echo '<div class="pull-left" >'.$row->name.'</div>'; 
                }?>
        <div class="media-body pull-right" style="font-weight: normal;"><?php echo $row->active_store_count; ?></div>
        
    </div>
    <?php } ?>
</div>
