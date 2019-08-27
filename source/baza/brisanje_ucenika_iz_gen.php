<?php
  require_once("konekcija.php");
  $id = $_POST["id"];
  $idGeneracija = $_POST["idGeneracija"];
  mysqli_query($link, "DELETE FROM ucenici WHERE ucenici.id = $id AND ucenici.generacije_id = $idGeneracija");

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
