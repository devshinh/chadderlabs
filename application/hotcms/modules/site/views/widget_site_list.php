<?php foreach($sites as $site){
    if($site->id == 1) continue;
    ?>
<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid bg-white">
            <div class="span3"><a href="http://<?php echo $site->domain?>"><img width="227" height="157" alt="brand image" src="<?php echo $site->image->full_path?>"></a></div>
            <div class="span9">
                <h2><a href="http://<?php echo $site->domain?>"><?php echo $site->name?></a></h2>
                <!--<p>Test yourself on all the latest games offered from EA.</p>-->
                <a href="http://<?php echo $site->domain?>" class="btn btn-primary">Go To Lab</a></div>
        </div>                
    </div><!--/span12-->
</div>
<?php }?>