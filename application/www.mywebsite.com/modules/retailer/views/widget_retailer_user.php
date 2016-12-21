<div class="hero-unit" id="retailers">
    <div class="row-fluid">
        <h2 class="pull-left">Employer</h2>
        <div class="pull-right">
        </div> 
    </div>
    <div class="row-fluid">
        <?php
        if (!empty($retailers_info)) {  
                if(!empty($retailers_info->logo)){
                    if(strlen($retailers_info->name) < 20){
                      print('<div class="row-fluid logoRow">');
                    }else{
                      print('<div class="row-fluid" style="text-transform: uppercase">');  
                    }
                  //printf('<div class="span3">%s</div><div class="span9"><a style="vertical-align: middle;" href="/retailer/%s">%s</a></div>', $retailers_info->logo->full_html, $retailers_info->slug ,$retailers_info->name);                     
                    if(!empty($retailers_info->store_details)){
                        if(!empty($retailers_info->store_details->province)){
                          $store_details = sprintf('<br />%s (%s)', $retailers_info->store_details->store_name,$retailers_info->store_details->province);
                        }else{
                          $store_details = sprintf('<br />%s', $retailers_info->store_details->store_name);  
                        }
                    }else {
                        $store_details = '';
                    }
                    printf('<div class="span3"><img src="/asset/upload/%s.%s" alt="%s"/></div><div class="span9">%s%s</div>', $retailers_info->logo->file_name, $retailers_info->logo->extension, $retailers_info->logo->description,$retailers_info->name, $store_details);                     
                }else{
                    print('<div class="row-fluid">');
                  //printf('<div class="span12"><a style="text-transform: uppercase;" href="/retailer/%s">%s</a></div>', $retailers_info->slug ,$retailers_info->name); 
                    printf('<div class="span12" style="text-transform:uppercase;">%s</div>', $retailers_info->name);                     
                }
                print('</div>');
        }
        ?>
    </div> 
</div>     