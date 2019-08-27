<?php
  $link = mysqli_connect("localhost", "root", "", "skola");
  if(!$link) {
    echo "Došlo je do greške u povezivanju sa bazom." .PHP_EOL;
    exit;
  }
?>
