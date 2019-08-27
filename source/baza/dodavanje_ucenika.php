<?php
  require_once("konekcija.php");
  $idGeneracija = $_POST["idGeneracija"];
  $ime = $_POST["ime"];
  $prezime = $_POST["prezime"];
  $ime_oca_majke = $_POST["ime_oca_majke"];
  $datum_rodjenja = $_POST["datum_rodjenja"];
  $mesto_rodjenja = $_POST["mesto_rodjenja"];
  $beleska = nl2br($_POST["beleska"]);
  $slikaNaziv = $idGeneracija."-".$_FILES["imeSlike"]["name"];
  $slikaPrivremenNaziv = $_FILES['imeSlike']['tmp_name'];

  if(move_uploaded_file($slikaPrivremenNaziv, "../slike/".$slikaNaziv)) {
    mysqli_query($link, "INSERT INTO `ucenici` (`ime`, `prezime`, `ime_oca_majke`, `datum_rodjenja`, `mesto_rodjenja`, `slika`, `komentar`, `generacije_id`) VALUES ('$ime', '$prezime', '$ime_oca_majke', '$datum_rodjenja', '$mesto_rodjenja', '$slikaNaziv', '$beleska', $idGeneracija)");
		require_once("../pomocne_funkcije/obrada_slike.php");
		obradaSlike($slikaNaziv);
	} else {
    mysqli_query($link, "INSERT INTO `ucenici` (`ime`, `prezime`, `ime_oca_majke`, `datum_rodjenja`, `mesto_rodjenja`, `komentar`, `generacije_id`) VALUES ('$ime', '$prezime', '$ime_oca_majke', '$datum_rodjenja', '$mesto_rodjenja', '$beleska', $idGeneracija)");
  }

  $niz = array("poruka" => "ok");
  echo json_encode($niz);
?>
