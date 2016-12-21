<?php
  header('Content-type: text/javascript');
  header('pragma: no-cache');
  header('expires: 0');
  echo 'var tinyMCEImageList = new Array(';
  $length = count($images);
  $i = 0;
  foreach($images as $image)
  {
    echo '["'.$image->name.'","'.$image->full_path.'"]';
    $i++;
    if($i < $length) {
      echo ',';
    }
  }
  echo ');';
?>