xml_header
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?php echo $url ?>/</loc>
    <priority>1.0</priority>
  </url>
<?php
foreach ($aPage as $row) {
?>
  <url>
    <loc><?php echo $row['loc'] ?>/</loc>
    <priority><?php echo $row['priority'] ?></priority>
  </url>
<?php
}
?>
</urlset>
