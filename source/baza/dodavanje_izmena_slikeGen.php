<?php
  require_once("konekcija.php");
  $idGeneracija = $_POST["idGeneracija"];
  $slikaNaziv = $idGeneracija."-".$_FILES["imeSlike"]["name"];
  $slikaPrivremenNaziv = $_FILES['imeSlike']['tmp_name'];

  if(move_uploaded_file($slikaPrivremenNaziv, "../slike/".$slikaNaziv)) {
    mysqli_query($link, "UPDATE generacije SET generacije.slika = '$slikaNaziv' WHERE generacije.id = $idGeneracija");
		require_once("../pomocne_funkcije/obrada_slikeGenRaz.php");
		obradaSlikeGenRaz($slikaNaziv);
	}

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
