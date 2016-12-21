<div class="hero-unit" >
    <div class="container-fluid">
        <h1><?php print $retailer->name;?></h1>
        <?php if(!empty($retailer->logo)){?>
        <div class="row-fluid">
            <strong>Retailer's logo: </strong><?php print $retailer->logo->full_html;?>
        </div>        
        <?php }?>
        <div class="row-fluid">
            <strong>Country: </strong><?php print $retailer->country;?>
        </div>
    </div>
</div>
