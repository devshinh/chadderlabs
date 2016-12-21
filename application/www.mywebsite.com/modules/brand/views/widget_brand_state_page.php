<div class="hero-unit" id="retailer_state_locations">
    
        <div id="filters_div">
            <form id="search_form" method="post">
                <input type="hidden" value="store_name" name="sort_by">
                <input type="hidden" value="asc" name="sort_direction">
            </form>
        </div>    
    
    
    <div class="media">
        <div class="pull-left">
            <?php print $retailer_detail->logo->full_html?>
        </div>
        <div class="media-body">
            <h1><?php print $retailer_detail->name?> STORE LOCATIONS IN <?php print $state_detail->province_name?> (<?php print $state_detail->country_code?>) </h1>
        </div>
    </div>    
    
    <div class="media tableHeader">
        <div  id="sortable_city" class="media-body span3 sortable
    <?php if ($sorting['sort_by'] == 'city') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>                
              ">City</div>          
        <div id="sortable_store_name" class="span4 sortable 
    <?php if ($sorting['sort_by'] == 'store_name') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>         
             "><?=lang("hotcms_location")?></div>
        <div  id="sortable_store_num" class="media-body span2 sortable
    <?php if ($sorting['sort_by'] == 'store_num') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>                
              ">Store #</div>        
        <div  id="sortable_street_1" class="media-body span3 sortable
    <?php if ($sorting['sort_by'] == 'street_1') {
        echo ($sorting['sort_direction'] == 'desc' ? ' headerSortDown' : ' headerSortUp');
    }?>                
              ">Address</div>
     
        
    </div>    
    
<!--    <div class="media tableHeader">
        <div class="pull-left"><?= lang("hotcms_name") ?></div>
        <div class="media-body pull-right">Address</div>
    </div>-->
    <?php foreach ($stores_list as $store) {?>
    <div class="row-fluid tableRow">
        <div class="span3" style="font-weight: normal;"><?php print ($store->city);?></div>           
        <div class="span4" ><a href="/store/<?php print $retailer_detail->slug?>/<?php print (strtolower($store->country_code));?>/<?php print (strtolower($store->province));?>/<?php print (strtolower($store->slug));?>/<?php print ($store->id);?>"><?php print ($store->store_name);?></a></div> 
        <div class="span2" style="font-weight: normal;"><?php print ($store->store_num);?></div>   
        <div class="span3" style="font-weight: normal;"><?php print ($store->street_1);?></div>   
    </div>
    <?php } ?>
</div>        
