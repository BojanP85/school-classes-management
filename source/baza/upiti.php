<?php
  require_once("konekcija.php");
  $niz = array();

  switch($_GET["upit"]) {
    case "generacije_listing":
      $rezultat = mysqli_query($link, "SELECT * FROM generacije ORDER BY generacije.generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    // za potrebe naredna četiri upita kreiran je VIEW "prosecne_vrednosti" pomoću sledeće sintakse:
    /*
    CREATE VIEW prosecne_vrednosti AS
    SELECT generacijaID, generacija, AVG(prosek) AS razredProsek, AVG(matematika) AS razredMatematika
    FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS razred, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> "NULL") mojaTabela
    GROUP BY razredID ORDER BY generacija ASC, razred ASC
    */

    // izračunavanje proseka generacije i prosečne ocene iz matematike (AVG(prosecne_vrednosti.razredProsek) AS prosekGeneracije, AVG(prosecne_vrednosti.razredMatematika) AS matematikaGeneracije) se može obaviti i na drugi način (SUM(prosecne_vrednosti.razredProsek) / COUNT(prosecne_vrednosti.razred) AS prosekGeneracije, SUM(prosecne_vrednosti.razredMatematika) / COUNT(prosecne_vrednosti.razred) AS matematikaGeneracije), za šta bi VIEW "prosecne_vrednosti" trebalo blago modifikovati tako da se u prvi SELECT iskaz ubaci i kolona "razred":
    /*
    CREATE VIEW prosecne_vrednosti AS
    SELECT razred, generacijaID, generacija, AVG(prosek) AS razredProsek, AVG(matematika) AS razredMatematika
    FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS razred, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> "NULL") mojaTabela
    GROUP BY razredID ORDER BY generacija ASC, razred ASC
    */

    case "generacije_listing_rastuce_prosek":
      $rezultat = mysqli_query($link, "SELECT prosecne_vrednosti.generacijaID, prosecne_vrednosti.generacija, AVG(prosecne_vrednosti.razredProsek) AS prosekGeneracije, AVG(prosecne_vrednosti.razredMatematika) AS matematikaGeneracije FROM prosecne_vrednosti GROUP BY prosecne_vrednosti.generacija ORDER BY prosekGeneracije ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacije_listing_opadajuce_prosek":
      $rezultat = mysqli_query($link, "SELECT prosecne_vrednosti.generacijaID, prosecne_vrednosti.generacija, AVG(prosecne_vrednosti.razredProsek) AS prosekGeneracije, AVG(prosecne_vrednosti.razredMatematika) AS matematikaGeneracije FROM prosecne_vrednosti GROUP BY prosecne_vrednosti.generacija ORDER BY prosekGeneracije DESC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacije_listing_rastuce_matematika":
      $rezultat = mysqli_query($link, "SELECT prosecne_vrednosti.generacijaID, prosecne_vrednosti.generacija, AVG(prosecne_vrednosti.razredProsek) AS prosekGeneracije, AVG(prosecne_vrednosti.razredMatematika) AS matematikaGeneracije FROM prosecne_vrednosti GROUP BY prosecne_vrednosti.generacija ORDER BY matematikaGeneracije ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacije_listing_opadajuce_matematika":
      $rezultat = mysqli_query($link, "SELECT prosecne_vrednosti.generacijaID, prosecne_vrednosti.generacija, AVG(prosecne_vrednosti.razredProsek) AS prosekGeneracije, AVG(prosecne_vrednosti.razredMatematika) AS matematikaGeneracije FROM prosecne_vrednosti GROUP BY prosecne_vrednosti.generacija ORDER BY matematikaGeneracije DESC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacije_listingID":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT * FROM generacije WHERE generacije.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacije_listingSLIKA":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT generacije.slika FROM generacije WHERE generacije.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "generacija_dodavanje":
      $generacija = mysqli_real_escape_string($link, $_GET["generacija"]);
      $rezultat = mysqli_query($link, "SELECT generacije.id FROM generacije WHERE generacije.generacija = '$generacija'");
      if(mysqli_num_rows($rezultat) > 0) {
        $niz = array("poruka" => "duplikat");
      } else {
        mysqli_query($link, "INSERT INTO `generacije` (`generacija`) VALUES ('$generacija')");
        $niz = array("poruka" => "ok");
      }
      break;

    case "generacija_izmena":
      $id = $_GET["id"];
      $generacija = $_GET["generacija"];
      $rezultat = mysqli_query($link, "SELECT generacije.id FROM generacije WHERE generacije.generacija = '$generacija' AND generacije.id <> $id");
      if(mysqli_num_rows($rezultat) != 0) {
        $niz = array("poruka" => "duplikat");
      } else {
        mysqli_query($link, "UPDATE generacije SET generacije.generacija = '$generacija' WHERE generacije.id = $id");
        $niz = array("poruka" => "ok");
      }
      break;

    case "generacija_brisanje":
      $id = $_GET["id"];
      mysqli_query($link, "DELETE FROM generacije WHERE generacije.id = $id");
      $niz = array("poruka" => "ok");
      break;

    case "razredi_listing":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT * FROM razredi WHERE razredi.generacije_id = $id ORDER BY razredi.odeljenje ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listing_rastuce_prosek":
      $rezultat = mysqli_query($link,
      "SELECT razredID, odeljenje, generacija, AVG(prosek) AS prosekRazreda, AVG(matematika) AS matematikaRazreda
      FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS odeljenje, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY razredID ORDER BY prosekRazreda ASC, odeljenje ASC, generacija ASC");

      // za potrebe vežbe razmotriti i sledeći upit. u ovom upitu, prilikom povezivanja tabela "razredi_has_ucenici" i "razredi", koristimo "RIGHT JOIN" sintaksu jer u listing želimo da uključimo i one razrede (iz tabele "razredi") kojih nema u tabeli "razredi_has_ucenici", tj. one razrede čiji "id" se ne pojavljuje u tabeli "razredi_has_ucenici" (pod kolonom "razredi_id"). to su zapravo oni razredi u koje nije upisan nijedan učenik. isto tako, izostavljanjem uslova "razredi_has_ucenici.prosek <> 'NULL'" želimo da u listing uključimo i one razrede u koje su upisani isključivo učenici kojima nisu dodeljene prosečne ocene, odnosno ocene iz matematike.
      // ovaj upit se, naravno, može primeniti i na naredna tri case-a.
      /*
      "SELECT razredi.id, razredi.odeljenje, generacije.generacija, AVG(razredi_has_ucenici.prosek) AS prosekRazreda, AVG(razredi_has_ucenici.matematika) AS matematikaRazreda
      FROM razredi_has_ucenici
      RIGHT JOIN razredi ON razredi.id = razredi_has_ucenici.razredi_id
      INNER JOIN generacije ON razredi.generacije_id = generacije.id
      GROUP BY razredi.id ORDER BY prosekRazreda ASC, razredi.odeljenje ASC"
      */

      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listing_opadajuce_prosek":
      $rezultat = mysqli_query($link,
      "SELECT razredID, odeljenje, generacija, AVG(prosek) AS prosekRazreda, AVG(matematika) AS matematikaRazreda
      FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS odeljenje, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY razredID ORDER BY prosekRazreda DESC, odeljenje ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listing_rastuce_matematika":
      $rezultat = mysqli_query($link,
      "SELECT razredID, odeljenje, generacija, AVG(prosek) AS prosekRazreda, AVG(matematika) AS matematikaRazreda
      FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS odeljenje, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY razredID ORDER BY matematikaRazreda ASC, odeljenje ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listing_opadajuce_matematika":
      $rezultat = mysqli_query($link,
      "SELECT razredID, odeljenje, generacija, AVG(prosek) AS prosekRazreda, AVG(matematika) AS matematikaRazreda
      FROM (SELECT razredi.id AS razredID, razredi.odeljenje AS odeljenje, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM razredi, generacije, razredi_has_ucenici WHERE razredi.id = razredi_has_ucenici.razredi_id AND razredi.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY razredID ORDER BY matematikaRazreda DESC, odeljenje ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listingID":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT * FROM razredi WHERE razredi.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razredi_listingSLIKA":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT razredi.slika FROM razredi WHERE razredi.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "razred_dodavanje":
      $id = $_GET["id"];
      $razred = mysqli_real_escape_string($link, $_GET["razred"]);
      $rezultat = mysqli_query($link, "SELECT razredi.id FROM razredi WHERE razredi.odeljenje = '$razred' AND razredi.generacije_id = $id");
      if(mysqli_num_rows($rezultat) > 0) {
        $niz = array("poruka" => "duplikat");
      } else {
        mysqli_query($link, "INSERT INTO `razredi` (`odeljenje`, `generacije_id`) VALUES ('$razred', $id)");
        $niz = array("poruka" => "ok");
      }
      break;

    case "razred_izmena":
      $idRaz = $_GET["idRaz"];
      $idGen = $_GET["idGen"];
      $odeljenje = $_GET["odeljenje"];
      $rezultat = mysqli_query($link, "SELECT razredi.id FROM razredi WHERE razredi.odeljenje = '$odeljenje' AND razredi.id <> $idRaz AND razredi.generacije_id = $idGen");
      if(mysqli_num_rows($rezultat) != 0) {
        $niz = array("poruka" => "duplikat");
      } else {
        mysqli_query($link, "UPDATE razredi SET razredi.odeljenje = '$odeljenje' WHERE razredi.id = $idRaz");
        $niz = array("poruka" => "ok");
      }
      break;

    case "razred_brisanje":
      $id = $_GET["id"];
      mysqli_query($link, "DELETE FROM razredi WHERE razredi.id = $id");
      $niz = array("poruka" => "ok");
      break;

    case "ucenici_listing_gen":
      $idGeneracija = $_GET["idGeneracija"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, ucenici.slika, ucenici.komentar
      FROM ucenici
      WHERE ucenici.generacije_id = $idGeneracija
      ORDER BY ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_raz":
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, razredi_has_ucenici.slika, ucenici.komentar, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika
      FROM ucenici, razredi_has_ucenici
      WHERE razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = razredi_has_ucenici.ucenici_id
      ORDER BY ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_raz_rastuce_prosek":
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, razredi_has_ucenici.slika, ucenici.komentar, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika
      FROM ucenici, razredi_has_ucenici
      WHERE razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = razredi_has_ucenici.ucenici_id
      ORDER BY razredi_has_ucenici.prosek ASC, ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_raz_opadajuce_prosek":
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, razredi_has_ucenici.slika, ucenici.komentar, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika
      FROM ucenici, razredi_has_ucenici
      WHERE razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = razredi_has_ucenici.ucenici_id
      ORDER BY razredi_has_ucenici.prosek DESC, ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_raz_rastuce_matematika":
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, razredi_has_ucenici.slika, ucenici.komentar, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika
      FROM ucenici, razredi_has_ucenici
      WHERE razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = razredi_has_ucenici.ucenici_id
      ORDER BY razredi_has_ucenici.matematika ASC, ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_raz_opadajuce_matematika":
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link,
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, razredi_has_ucenici.slika, ucenici.komentar, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika
      FROM ucenici, razredi_has_ucenici
      WHERE razredi_has_ucenici.razredi_id = $idRazred AND ucenici.id = razredi_has_ucenici.ucenici_id
      ORDER BY razredi_has_ucenici.matematika DESC, ucenici.prezime ASC, ucenici.ime ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_rastuce_prosek":
      $rezultat = mysqli_query($link,
      "SELECT ucenikID, ucenikIme, ucenikPrezime, generacijaID, generacija, AVG(prosek) AS prosekUcenika, AVG(matematika) AS matematikaUcenika
      FROM (SELECT ucenici.id AS ucenikID, ucenici.ime AS ucenikIme, ucenici.prezime AS ucenikPrezime, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM ucenici, generacije, razredi_has_ucenici WHERE ucenici.id = razredi_has_ucenici.ucenici_id AND ucenici.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY ucenikID ORDER BY prosekUcenika ASC, ucenikPrezime ASC, ucenikIme ASC, generacija ASC");

      // za potrebe vežbe razmotriti i sledeći upit. u ovom upitu, prilikom povezivanja tabela "razredi_has_ucenici" i "ucenici", koristimo "RIGHT JOIN" sintaksu jer u listing želimo da uključimo i one učenike (iz tabele "ucenici") kojih nema u tabeli "razredi_has_ucenici", tj. one učenike čiji "id" se ne pojavljuje u tabeli "razredi_has_ucenici" (pod kolonom "ucenici_id"). to su zapravo oni učenici koji nisu upisani ni u jedan razred. isto tako, izostavljanjem uslova "razredi_has_ucenici.prosek <> 'NULL'" želimo da u listing uključimo i one učenike koji su upisani u neki od razreda ali kojima nisu dodeljene prosečne ocene, odnosno ocene iz matematike.
      // ovaj upit se, naravno, može primeniti i na naredna tri case-a.
      /*
      "SELECT ucenici.id, ucenici.ime, ucenici.prezime, generacije.id, generacije.generacija, AVG(razredi_has_ucenici.prosek) AS prosekUcenika, AVG(razredi_has_ucenici.matematika) AS matematikaUcenika
      FROM razredi_has_ucenici
      RIGHT JOIN ucenici ON ucenici.id = razredi_has_ucenici.ucenici_id
      INNER JOIN generacije ON generacije.id = ucenici.generacije_id
      GROUP BY ucenici.id ORDER BY prosekUcenika ASC, ucenici.prezime ASC, ucenici.ime ASC, generacije.generacija ASC"
      */

      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_opadajuce_prosek":
      $rezultat = mysqli_query($link,
      "SELECT ucenikID, ucenikIme, ucenikPrezime, generacijaID, generacija, AVG(prosek) AS prosekUcenika, AVG(matematika) AS matematikaUcenika
      FROM (SELECT ucenici.id AS ucenikID, ucenici.ime AS ucenikIme, ucenici.prezime AS ucenikPrezime, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM ucenici, generacije, razredi_has_ucenici WHERE ucenici.id = razredi_has_ucenici.ucenici_id AND ucenici.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY ucenikID ORDER BY prosekUcenika DESC, ucenikPrezime ASC, ucenikIme ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_rastuce_matematika":
      $rezultat = mysqli_query($link,
      "SELECT ucenikID, ucenikIme, ucenikPrezime, generacijaID, generacija, AVG(prosek) AS prosekUcenika, AVG(matematika) AS matematikaUcenika
      FROM (SELECT ucenici.id AS ucenikID, ucenici.ime AS ucenikIme, ucenici.prezime AS ucenikPrezime, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM ucenici, generacije, razredi_has_ucenici WHERE ucenici.id = razredi_has_ucenici.ucenici_id AND ucenici.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY ucenikID ORDER BY matematikaUcenika ASC, ucenikPrezime ASC, ucenikIme ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listing_opadajuce_matematika":
      $rezultat = mysqli_query($link,
      "SELECT ucenikID, ucenikIme, ucenikPrezime, generacijaID, generacija, AVG(prosek) AS prosekUcenika, AVG(matematika) AS matematikaUcenika
      FROM (SELECT ucenici.id AS ucenikID, ucenici.ime AS ucenikIme, ucenici.prezime AS ucenikPrezime, generacije.id AS generacijaID, generacije.generacija AS generacija, razredi_has_ucenici.prosek AS prosek, razredi_has_ucenici.matematika AS matematika FROM ucenici, generacije, razredi_has_ucenici WHERE ucenici.id = razredi_has_ucenici.ucenici_id AND ucenici.generacije_id = generacije.id AND razredi_has_ucenici.prosek <> 'NULL') mojaTabela
      GROUP BY ucenikID ORDER BY matematikaUcenika DESC, ucenikPrezime ASC, ucenikIme ASC, generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenik_listingID":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT ucenici.id, ucenici.ime, ucenici.prezime, ucenici.ime_oca_majke, DATE_FORMAT(ucenici.datum_rodjenja, '%d.%m.%Y.') AS datum_rodjenja, ucenici.mesto_rodjenja, ucenici.slika, ucenici.komentar FROM ucenici WHERE ucenici.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listingID":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT * FROM ucenici WHERE ucenici.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_listingSLIKA":
      $id = $_GET["id"];
      $rezultat = mysqli_query($link, "SELECT ucenici.slika FROM ucenici WHERE ucenici.id = $id");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_razredi_listingID":
      $idUcenik = $_GET["idUcenik"];
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link, "SELECT * FROM razredi_has_ucenici WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenici_razredi_listingSLIKA":
      $idUcenik = $_GET["idUcenik"];
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link, "SELECT razredi_has_ucenici.slika FROM razredi_has_ucenici WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenik_dodavanje_u_raz":
      $idUcenik = $_GET["idUcenik"];
      $idRazred = $_GET["idRazred"];
      $rezultat = mysqli_query($link, "SELECT * FROM razredi_has_ucenici WHERE razredi_has_ucenici.razredi_id = $idRazred AND razredi_has_ucenici.ucenici_id = $idUcenik");
      if(mysqli_num_rows($rezultat) > 0) {
        $niz = array("poruka" => "duplikat");
      } else {
        mysqli_query($link, "INSERT INTO `razredi_has_ucenici` (`razredi_id`, `ucenici_id`) VALUES ($idRazred, $idUcenik)");
        $niz = array("poruka" => "ok");
      }
      break;

    case "ucenik_brisanje_iz_gen":
      $id = $_GET["id"];
      $idGeneracija = $_GET["idGeneracija"];
      mysqli_query($link, "DELETE FROM ucenici WHERE ucenici.id = $id AND ucenici.generacije_id = $idGeneracija");
      $niz = array("poruka" => "ok");
      break;

    case "ucenik_brisanje_iz_raz":
      $idUcenik = $_GET["idUcenik"];
      $idRazred = $_GET["idRazred"];
      mysqli_query($link, "DELETE FROM razredi_has_ucenici WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred");
      $niz = array("poruka" => "ok");
      break;

    // za potrebe narednog upita kreiran je VIEW "pretraga_ucenika" pomoću sledeće sintakse:
    /*
    CREATE VIEW pretraga_ucenika AS
    SELECT ucenici.id AS ucenikID, CONCAT(ucenici.prezime, " ", ucenici.ime) AS prezimeImeUcenika, generacije.id as generacijaID, generacije.generacija, AVG(razredi_has_ucenici.prosek) AS prosekUcenika, AVG(razredi_has_ucenici.matematika) AS matematikaUcenika
    FROM razredi_has_ucenici
    RIGHT JOIN ucenici ON ucenici.id = razredi_has_ucenici.ucenici_id
    INNER JOIN generacije ON generacije.id = ucenici.generacije_id
    GROUP BY ucenici.id ORDER BY prezimeImeUcenika ASC, generacije.generacija ASC
    */

    case "ucenik_pretraga":
      $pretraga_vrednost = $_GET["pretraga_vrednost"];
      $rezultat = mysqli_query($link, "SELECT * FROM pretraga_ucenika WHERE pretraga_ucenika.prezimeImeUcenika LIKE '%$pretraga_vrednost%' ORDER BY pretraga_ucenika.prezimeImeUcenika ASC, pretraga_ucenika.generacija ASC");
      if(mysqli_num_rows($rezultat) == 0) {
        $niz = array("poruka" => "bez_rezultata");
      } else {
        mysqli_query($link, "CREATE OR REPLACE VIEW rezultati_pretrage AS SELECT * FROM pretraga_ucenika WHERE pretraga_ucenika.prezimeImeUcenika LIKE '%$pretraga_vrednost%'"); // za potrebe naredna četiri upita kreiramo VIEW "rezultati_pretrage". ovaj VIEW se iznova kreira svaki put kada korisnik klikne na dugme za pretragu, ali samo pod uslovom da polje za pretragu nije prazno, kao i da postoje konkretni rezultati pretrage. dotični VIEW služi kao tabela za skladištenje trenutnih rezultata pretrage, koje potom možemo sortirati pomoću nekog od naredna četiri upita.
        while($red = mysqli_fetch_assoc($rezultat)) {
          $niz[] = $red;
        }
      }
      break;

    case "ucenik_pretraga_rastuce_prosek":
      $rezultat = mysqli_query($link, "SELECT * FROM rezultati_pretrage ORDER BY rezultati_pretrage.prosekUcenika ASC, rezultati_pretrage.prezimeImeUcenika ASC, rezultati_pretrage.generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenik_pretraga_opadajuce_prosek":
      $rezultat = mysqli_query($link, "SELECT * FROM rezultati_pretrage ORDER BY rezultati_pretrage.prosekUcenika DESC, rezultati_pretrage.prezimeImeUcenika ASC, rezultati_pretrage.generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenik_pretraga_rastuce_matematika":
      $rezultat = mysqli_query($link, "SELECT * FROM rezultati_pretrage ORDER BY rezultati_pretrage.matematikaUcenika ASC, rezultati_pretrage.prezimeImeUcenika ASC, rezultati_pretrage.generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ucenik_pretraga_opadajuce_matematika":
      $rezultat = mysqli_query($link, "SELECT * FROM rezultati_pretrage ORDER BY rezultati_pretrage.matematikaUcenika DESC, rezultati_pretrage.prezimeImeUcenika ASC, rezultati_pretrage.generacija ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ocene_listing_gen":
      $idUcenik = $_GET["idUcenik"];
      $rezultat = mysqli_query($link, "SELECT razredi_has_ucenici.prosek, razredi_has_ucenici.matematika, razredi.odeljenje FROM razredi_has_ucenici, ucenici, razredi WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.ucenici_id = ucenici.id AND razredi_has_ucenici.razredi_id = razredi.id ORDER BY razredi.odeljenje ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "slika_ocene_listing_gen":
      $idUcenik = $_GET["idUcenik"];
      $rezultat = mysqli_query($link, "SELECT razredi_has_ucenici.slika, razredi_has_ucenici.prosek, razredi_has_ucenici.matematika, razredi.odeljenje FROM razredi_has_ucenici, ucenici, razredi WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.ucenici_id = ucenici.id AND razredi_has_ucenici.razredi_id = razredi.id ORDER BY razredi.odeljenje ASC");
      while($red = mysqli_fetch_assoc($rezultat)) {
        $niz[] = $red;
      }
      break;

    case "ocene_izmena":
      $idUcenik = $_GET["idUcenik"];
      $idRazred = $_GET["idRazred"];
      $matematika = $_GET["matematika"];
      $prosek = $_GET["prosek"];
      mysqli_query($link, "UPDATE razredi_has_ucenici SET razredi_has_ucenici.matematika = $matematika, razredi_has_ucenici.prosek = $prosek WHERE razredi_has_ucenici.ucenici_id = $idUcenik AND razredi_has_ucenici.razredi_id = $idRazred");
      $niz = array("poruka" => "ok");
      break;
  }
  echo json_encode($niz);
?>
