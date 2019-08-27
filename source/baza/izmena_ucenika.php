<?php
  require_once("konekcija.php");
  $idUcenik = $_POST["idUcenik"];
  $ime = $_POST["ime"];
  $prezime = $_POST["prezime"];
  $ime_oca_majke = $_POST["ime_oca_majke"];
  $datum_rodjenja = $_POST["datum_rodjenja"];
  $mesto_rodjenja = $_POST["mesto_rodjenja"];
  $beleska = nl2br($_POST["beleska"]);
  $slikaNaziv = $idUcenik."-".$_FILES["imeSlike"]["name"];
  $slikaPrivremenNaziv = $_FILES['imeSlike']['tmp_name'];

  if(move_uploaded_file($slikaPrivremenNaziv, "../slike/".$slikaNaziv)) {
    mysqli_query($link, "UPDATE ucenici SET ucenici.ime = '$ime', ucenici.prezime = '$prezime', ucenici.ime_oca_majke = '$ime_oca_majke', ucenici.datum_rodjenja = '$datum_rodjenja', ucenici.mesto_rodjenja = '$mesto_rodjenja', ucenici.slika = '$slikaNaziv', ucenici.komentar = '$beleska' WHERE ucenici.id = $idUcenik");
		require_once("../pomocne_funkcije/obrada_slike.php");
		obradaSlike($slikaNaziv);
	} else {
    mysqli_query($link, "UPDATE ucenici SET ucenici.ime = '$ime', ucenici.prezime = '$prezime', ucenici.ime_oca_majke = '$ime_oca_majke', ucenici.datum_rodjenja = '$datum_rodjenja', ucenici.mesto_rodjenja = '$mesto_rodjenja', ucenici.komentar = '$beleska' WHERE ucenici.id = $idUcenik");
  }

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
