<?php
  require_once("konekcija.php");
  $idUcenik = $_POST["idUcenik"];
  $idRazred = $_POST["idRazred"];
  $beleska = nl2br($_POST["beleska"]);
  $slikaNaziv = $idUcenik."-".$idRazred."-".$_FILES["imeSlike"]["name"];
  $slikaPrivremenNaziv = $_FILES['imeSlike']['tmp_name'];

  if(move_uploaded_file($slikaPrivremenNaziv, "../slike/".$slikaNaziv)) {
    mysqli_query($link, "UPDATE razredi_has_ucenici, ucenici SET razredi_has_ucenici.slika = '$slikaNaziv', ucenici.komentar = '$beleska' WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = $idUcenik");
		require_once("../pomocne_funkcije/obrada_slike.php");
		obradaSlike($slikaNaziv);
	} else {
    mysqli_query($link, "UPDATE razredi_has_ucenici, ucenici SET ucenici.komentar = '$beleska' WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = $idUcenik");
  }

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
