<?php
  require_once("konekcija.php");
  $idGeneracija = $_POST["idGeneracija"];
  $idRazred = $_POST["idRazred"];
  $slikaNaziv = $idGeneracija."-".$idRazred."-".$_FILES["imeSlike"]["name"];
  $slikaPrivremenNaziv = $_FILES['imeSlike']['tmp_name'];

  if(move_uploaded_file($slikaPrivremenNaziv, "../slike/".$slikaNaziv)) {
    mysqli_query($link, "UPDATE razredi SET razredi.slika = '$slikaNaziv' WHERE razredi.id = $idRazred");
		require_once("../pomocne_funkcije/obrada_slikeGenRaz.php");
		obradaSlikeGenRaz($slikaNaziv);
	}

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
