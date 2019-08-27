<!DOCTYPE html>
<html>
  <head>
    <title>Rezultati pretrage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="javascript/bootstrap.min.js"></script>
    <link rel="icon" href="slike-favicon/icons8-search-40.png" type="image/png">
    <link rel="stylesheet" href="stilovi/bootstrap.css">
    <link rel="stylesheet" href="stilovi/stilovi.css">
    <style type="text/css">
      body {
        background-color: #e4e7ed;
      }
      #unos-ucenika-okvir {
        visibility: hidden;
        width: 950px;
        border: 2px solid;
        border-radius: 15px;
        box-shadow: 7px 7px 15px rgba(0, 0, 0, 0.6);
        margin-bottom: 30px;
        padding: 0px 20px 20px 20px;
      }
      .top-dugmici {
        font-size: 15px;
      }
      #gen-raz-naslov {
        float: left;
        position: sticky;
        left: 23px;
      }
      #horizontalna-linija {
        clear: left;
      }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker3.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
  </head>
  <body>
    <?php
      require_once("baza/konekcija.php");

      $pretraga_vrednost = $_GET["pretraga_vrednost"]; // vrednost parametra "pretraga_vrednost" dobijamo zahvaljujući "name" atributu input-a (id = "poljePretragaUcenika"). to znači da parametar i "name" atribut moraju imati isti naziv.
      $rezultat = mysqli_query($link, "SELECT * FROM pretraga_ucenika WHERE pretraga_ucenika.prezimeImeUcenika LIKE '%$pretraga_vrednost%' ORDER BY pretraga_ucenika.prezimeImeUcenika ASC, pretraga_ucenika.generacija ASC");
    ?>

    <!-- Glavni "div" okvir -->
    <div class="container-fluid">
      <!-- Navigacija -->
      <nav class="navbar navbar-expand navbar-fixed-top" style="background: linear-gradient(to right, #173CA5 0%, #0DABDF 100%)">
        <!-- Početna strana -->
        <span class="navbar-brand"><a href="pocetna_strana.html" style="color: white; margin-left: 50px; font-family: Centaur; font-size: 25px; text-decoration: none">Početna strana</a></span>
        <!-- Najbolje generacije / razredi / učenici -->
        <div class="btn-group" role="group" style="margin-top: 8px; margin-left: 152px">
          <button type="button" class="top-dugmici btn btn-light" style="color: black" disabled><b>Najbolje</b></button>
          <button type="button" class="top-dugmici btn btn-secondary"><a href="najbolje_generacije.html" style="color: black" target="_self">generacije</a></button><button type="button" class="top-dugmici btn btn-secondary" style="cursor: default">|</button>
          <button type="button" class="top-dugmici btn btn-secondary"><a href="najbolji_razredi.html" style="color: black" target="_self">razredi</a></button><button type="button" class="top-dugmici btn btn-secondary" style="cursor: default">|</button>
          <button type="button" class="top-dugmici btn btn-secondary"><a href="najbolji_ucenici.html" style="color: black" target="_self">učenici</a></button>
        </div>
        <!-- Pretraga -->
        <div class="form-inline pull-right" style="margin-top: 8px; margin-right: 65px">
          <form action="pretraga.php" method="get">
            <input class="form-control mr-sm-2" type="text" id="poljePretragaUcenika" name="pretraga_vrednost" placeholder="Nađi učenika..." aria-label="Search">
            <input class="btn btn-outline-success my-2 my-sm-0" type="submit" id="dgmPretragaUcenika" value="Traži" disabled="disabled" onclick="poljePretragaPrazno()">
          </form>
        </div>
      </nav>

      <!-- Prostor za naslov -->
      <h2 id="gen-raz-naslov">Rezultati pretrage za '<?php echo $pretraga_vrednost; ?>'</h2>
      <hr id="horizontalna-linija" style="margin-bottom: 40px"><br>

      <!-- Levi "div" okvir -->
      <div id="levi-okvir" style="float: left">
        <?php if(mysqli_num_rows($rezultat) == 0) { ?>
          <p style="font-size: 16px">Traženi učenik nije pronađen.</p>
        <?php } else { mysqli_query($link, "CREATE OR REPLACE VIEW rezultati_pretrage AS SELECT * FROM pretraga_ucenika WHERE pretraga_ucenika.prezimeImeUcenika LIKE '%$pretraga_vrednost%'"); // za objašnjenje ovog upita videti fajl "upiti.php", komentar za CASE "ucenik_pretraga" ?>
        <!-- Tabela za prikaz rezultata pretrage -->
        <table class="table table-condensed table-hover">
          <thead style="background-color: #e6f0f2">
            <tr>
              <th class="text-center" style="vertical-align: middle">#</th>
              <th class="text-center" style="width: auto; vertical-align: middle">Učenik</th>
              <th class="text-center" style="width: 150px; vertical-align: middle">Generacija</th>
              <th class="text-center" style="width: 85px; vertical-align: middle">
                <span>Prosek</span>
                <span id="strelica-prosek" style="margin-left: 11px; cursor: pointer; font-size: large" data-toggle="tooltip" title="Sortiraj rastućim redosledom" onclick="prikazUcen_rastuceProsekPretraga()">&#8593;</span>
              </th>
              <th class="text-center" style="width: 115px; vertical-align: middle">
                <span>Matematika</span>
                <span id="strelica-matematika" style="margin-left: 11px; cursor: pointer; font-size: large" data-toggle="tooltip" title="Sortiraj rastućim redosledom" onclick="prikazUcen_rastuceMatematikaPretraga()">&#8593;</span>
              </th>
            </tr>
          </thead>
          <tbody id="telo-tabelaPretragaUcen" style="background-color: #FFFFFF">
            <?php
              $i = 1;
              while($red = mysqli_fetch_assoc($rezultat)) {
            ?>
            <tr>
              <th class="text-center" style="vertical-align: middle; height: 60px" scope="row"><?php echo $i++; ?></th>
              <td class="text-center" style="vertical-align: middle; color: black">
                <span id="polje<?php echo $red["ucenikID"]; ?>" onmouseover="ucenik_hover(<?php echo $red["ucenikID"]; ?>)" onmouseout="ucenik_unhover(<?php echo $red["ucenikID"]; ?>)" onclick="prikazUcenikaPojedPretraga(<?php echo $red["ucenikID"]; ?>, <?php echo $red["generacijaID"]; ?>, <?php echo $red["generacija"]; ?>)"><?php echo $red["prezimeImeUcenika"]; ?></span>
              </td>
              <td class="text-center" style="vertical-align: middle; color: black">
                <span id="polje<?php echo $red["generacijaID"]; ?><?php echo $red["ucenikID"]; ?>" onmouseover="generacija_hover(<?php echo $red["generacijaID"]; ?>, <?php echo $red["ucenikID"]; ?>)" onmouseout="generacija_unhover(<?php echo $red["generacijaID"]; ?>, <?php echo $red["ucenikID"]; ?>)" onclick="prikazUcenikaGenPretraga(<?php echo $red["generacijaID"]; ?>, <?php echo $red["generacija"]; ?>)"><?php echo $red["generacija"]; ?></span>
              </td>
              <td id="polje-prosekUcenika<?php echo $red["ucenikID"]; ?>" class="text-center" style="vertical-align: middle">
                <?php if($red["prosekUcenika"] == null) { echo $red["prosekUcenika"]; } else { ?>
                <script>
                  /* skripta koja zaokružuje prosek na dve decimale (za drugi način videti komentar ispod funkcije "zaokruzivanjeMatematika()")
                     u slučaju kada je prva cifra koju odbacujemo 5, a poslednja cifra koju zadržavamo parna, uvodimo varijablu "suma". ukoliko je zbir cifara nakon broja 5 jednak nuli (suma == 0), poslednja cifra koju zadržavamo se ne uvećava. ukoliko je zbir cifara nakon broja 5 različit od nule (suma != 0), poslednja cifra koju zadržavamo se uvećava za jedan.
                     sve ovo važi i za skriptu koja zaokružuje prosečnu ocenu iz matematike na dve decimale. */
                  var prosek = <?php echo $red["prosekUcenika"]; ?>;
                  var prosekString = prosek.toString(); // prosek pretvaramo u "string".
                  var prosekStringNiz = Array.from(prosekString); // dobijeni "string" pretvaramo u niz.
                  if(prosekStringNiz[4] == 5 && prosekStringNiz[3]%2 == 1) { // proveravamo da li je peti član niza (indeks = 4) broj 5, kao i da li je četvrti član niza (indeks = 3) neparan broj.
                    prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ukoliko je uslov ispunjen, poslednja cifra koju zadržavamo se uvećava za jedan.
                    prosekStringNiz.splice(4); // ostatak niza, počev od broja 5 (indeks = 4), odbacujemo.
                    var zaokruzeniProsek = prosekStringNiz.join(""); // preostale članove niza spajamo u "string".
                  } else if(prosekStringNiz[4] == 5 && prosekStringNiz[3]%2 == 0) { // proveravamo da li je peti član niza (indeks = 4) broj 5, kao i da li je četvrti član niza (indeks = 3) paran broj.
                    var suma = 0; // ukoliko je uslov ispunjen, uvodimo varijablu "suma" i dodeljujemo joj vrednost 0.
                    for(var i = 5; i < prosekStringNiz.length; i++) { // prolazimo kroz niz počev od šestog člana niza (indeks = 5).
                      suma = suma + parseInt(prosekStringNiz[i]); // sumu uvećavamo za vrednost trenutnog člana niza.
                    }
                    if(suma == 0) { // ukoliko je konačna vrednost sume jednaka nuli...
                      prosekStringNiz.splice(4); // ...ostatak niza, počev od broja 5 (indeks = 4), odbacujemo, bez promene poslednje cifre koju zadržavamo.
                    } else { // ukoliko je konačna vrednost sume različita od nule...
                      prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ...poslednja cifra koju zadržavamo se uvećava za jedan.
                      prosekStringNiz.splice(4); // ostatak niza, počev od broja 5 (indeks = 4), odbacujemo.
                    }
                    var zaokruzeniProsek = prosekStringNiz.join(""); // preostale članove niza spajamo u "string".
                  } else { // ukoliko ni jedan od prethodna dva uslova nije ispunjen...
                    var zaokruzeniProsek = Number.parseFloat(prosek).toFixed(2); // ...na prosek primenjujemo built-in funkcije "parseFloat()" i "toFixed()".
                  }
                  $('#polje-prosekUcenika<?php echo $red["ucenikID"]; ?>').html(zaokruzeniProsek);
                </script>
                <?php } ?>
              </td>
              <td id="polje-matematikaUcenika<?php echo $red["ucenikID"]; ?>" class="text-center" style="vertical-align: middle">
                <?php if($red["matematikaUcenika"] == null) { echo $red["matematikaUcenika"]; } else { ?>
                <script>
                  /* skripta koja zaokružuje prosečnu ocenu iz matematike na dve decimale
                     svi komentari vezani za prethodnu skriptu važe i za ovu skriptu. */
                  var matematika = <?php echo $red["matematikaUcenika"]; ?>;
                  var matematikaString = matematika.toString();
                  var matematikaStringNiz = Array.from(matematikaString);
                  if(matematikaStringNiz[4] == 5 && matematikaStringNiz[3]%2 == 1) {
                    matematikaStringNiz[3] = (parseInt(matematikaStringNiz[3]) + 1).toString();
                    matematikaStringNiz.splice(4);
                    var zaokruzeniMatematika = matematikaStringNiz.join("");
                  } else if(matematikaStringNiz[4] == 5 && matematikaStringNiz[3]%2 == 0) {
                    var suma = 0;
                    for(var i = 5; i < matematikaStringNiz.length; i++) {
                      suma = suma + parseInt(matematikaStringNiz[i]);
                    }
                    if(suma == 0) {
                      matematikaStringNiz.splice(4);
                    } else {
                      matematikaStringNiz[3] = (parseInt(matematikaStringNiz[3]) + 1).toString();
                      matematikaStringNiz.splice(4);
                    }
                    var zaokruzeniMatematika = matematikaStringNiz.join("");
                  } else {
                    var zaokruzeniMatematika = Number.parseFloat(matematika).toFixed(2);
                  }
                  $('#polje-matematikaUcenika<?php echo $red["ucenikID"]; ?>').html(zaokruzeniMatematika);
                </script>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php } ?>
      </div>

      <!-- Srednji "div" okvir -->
      <div id="srednji-okvir" style="float: left; margin-left: 90px"></div>
    </div>
    <script type="text/javascript">
      (function() {
        $('#poljePretragaUcenika').val("");
      })();

      /* funkcija koja zaokružuje prosek na dve decimale
         u slučaju kada je prva cifra koju odbacujemo 5, a poslednja cifra koju zadržavamo parna, uvodimo varijablu "suma". ukoliko je zbir cifara nakon broja 5 jednak nuli (suma == 0), poslednja cifra koju zadržavamo se ne uvećava. ukoliko je zbir cifara nakon broja 5 različit od nule (suma != 0), poslednja cifra koju zadržavamo se uvećava za jedan.
         sve ovo važi i za funkciju koja zaokružuje prosečnu ocenu iz matematike na dve decimale. */
      function zaokruzivanjeProsek(parametarProsek) {
        var prosek = parametarProsek;
        var prosekString = prosek.toString(); // prosek pretvaramo u "string".
        var prosekStringNiz = Array.from(prosekString); // dobijeni "string" pretvaramo u niz.
        if(prosekStringNiz[4] == 5 && prosekStringNiz[3]%2 == 1) { // proveravamo da li je peti član niza (indeks = 4) broj 5, kao i da li je četvrti član niza (indeks = 3) neparan broj.
          prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ukoliko je uslov ispunjen, poslednja cifra koju zadržavamo se uvećava za jedan.
          prosekStringNiz.splice(4); // ostatak niza, počev od broja 5 (indeks = 4), odbacujemo.
          var zaokruzeniProsek = prosekStringNiz.join(""); // preostale članove niza spajamo u "string".
        } else if(prosekStringNiz[4] == 5 && prosekStringNiz[3]%2 == 0) { // proveravamo da li je peti član niza (indeks = 4) broj 5, kao i da li je četvrti član niza (indeks = 3) paran broj.
          var suma = 0; // ukoliko je uslov ispunjen, uvodimo varijablu "suma" i dodeljujemo joj vrednost 0.
          for(var i = 5; i < prosekStringNiz.length; i++) { // prolazimo kroz niz počev od šestog člana niza (indeks = 5).
            suma = suma + parseInt(prosekStringNiz[i]); // sumu uvećavamo za vrednost trenutnog člana niza.
          }
          if(suma == 0) { // ukoliko je konačna vrednost sume jednaka nuli...
            prosekStringNiz.splice(4); // ...ostatak niza, počev od broja 5 (indeks = 4), odbacujemo, bez promene poslednje cifre koju zadržavamo.
          } else { // ukoliko je konačna vrednost sume različita od nule...
            prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ...poslednja cifra koju zadržavamo se uvećava za jedan.
            prosekStringNiz.splice(4); // ostatak niza, počev od broja 5 (indeks = 4), odbacujemo.
          }
          var zaokruzeniProsek = prosekStringNiz.join(""); // preostale članove niza spajamo u "string".
        } else { // ukoliko ni jedan od prethodna dva uslova nije ispunjen...
          var zaokruzeniProsek = Number.parseFloat(prosek).toFixed(2); // ...na prosek primenjujemo built-in funkcije "parseFloat()" i "toFixed()".
        }
        return zaokruzeniProsek;
      }

      /* funkcija koja zaokružuje prosečnu ocenu iz matematike na dve decimale
         svi komentari vezani za funkciju "zaokruzivanjeProsek()" važe i za funkciju "zaokruzivanjeMatematika()". */
      function zaokruzivanjeMatematika(parametarMatematika) {
        var matematika = parametarMatematika;
        var matematikaString = matematika.toString();
        var matematikaStringNiz = Array.from(matematikaString);
        if(matematikaStringNiz[4] == 5 && matematikaStringNiz[3]%2 == 1) {
          matematikaStringNiz[3] = (parseInt(matematikaStringNiz[3]) + 1).toString();
          matematikaStringNiz.splice(4);
          var zaokruzeniMatematika = matematikaStringNiz.join("");
        } else if(matematikaStringNiz[4] == 5 && matematikaStringNiz[3]%2 == 0) {
          var suma = 0;
          for(var i = 5; i < matematikaStringNiz.length; i++) {
            suma = suma + parseInt(matematikaStringNiz[i]);
          }
          if(suma == 0) {
            matematikaStringNiz.splice(4);
          } else {
            matematikaStringNiz[3] = (parseInt(matematikaStringNiz[3]) + 1).toString();
            matematikaStringNiz.splice(4);
          }
          var zaokruzeniMatematika = matematikaStringNiz.join("");
        } else {
          var zaokruzeniMatematika = Number.parseFloat(matematika).toFixed(2);
        }
        return zaokruzeniMatematika;
      }

      /* drugi način da se prosek, odnosno prosečna ocena iz matematike zaokruži na dve decimale (u slučaju kada je prva cifra koju odbacujemo 5, a poslednja cifra koju zadržavamo parna) jeste da se proveri da li je svaka od cifara u decimalnom zapisu koje dolaze nakon broja 5 jednaka nuli. ukoliko jeste, poslednja cifra koju zadržavamo se ne uvećava. ukoliko, pak, postoji barem jedna cifra koja nije jednaka nuli, poslednja cifra koju zadržavamo se uvećava za jedan.  */
      /*
      // funkcija pomoću koje proveravamo da li je svaki element niza jednak nuli
      function provera(elementNiza) {
        return elementNiza == 0;
      }

      // funkcija koja zaokružuje prosek na dve decimale (sve dole navedeno važi i za funkciju koja zaokružuje prosečnu ocenu iz matematike na dve decimale)
      function zaokruzivanjeProsek(parametarProsek) {
        var prosek = parametarProsek;
        var prosekString = prosek.toString(); // prosek pretvaramo u "string".
        var prosekStringNiz = Array.from(prosekString); // dobijeni "string" pretvaramo u niz.
        var prosekStringNizSlice = prosekStringNiz.slice(5); // odvajamo elemente niza počev od šestog člana (indeks = 5) i od tih elemenata formiramo nov niz.
        if(prosekStringNiz[4] == 5) { // proveravamo da li je peti član niza (indeks = 4) broj 5.
          if(prosekStringNiz[3]%2 == 0) { // ukoliko jeste, proveravamo da li je četvrti član niza (indeks = 3) paran broj.
            if(prosekStringNizSlice.every(provera) == true) { // ukoliko jeste, proveravamo da li je svaki član novoformiranog niza jednak nuli.
              prosekStringNiz.splice(4); // ukoliko jeste, ostatak prvobitnog niza, počev od broja 5 (indeks = 4), odbacujemo, bez promene poslednje cifre koju zadržavamo.
            } else { // ukoliko nije...
              prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ...poslednja cifra koju zadržavamo se uvećava za jedan.
              prosekStringNiz.splice(4); // ostatak prvobitnog niza, počev od broja 5 (indeks = 4), odbacujemo.
            }
          } else { // ukoliko je četvrti član niza (indeks = 3) neparan broj...
            prosekStringNiz[3] = (parseInt(prosekStringNiz[3]) + 1).toString(); // ...poslednja cifra koju zadržavamo se uvećava za jedan.
            prosekStringNiz.splice(4); // ostatak prvobitnog niza, počev od broja 5 (indeks = 4), odbacujemo.
          }
          var zaokruzeniProsek = prosekStringNiz.join(""); // preostale članove niza spajamo u "string".
        } else { // ukoliko peti član niza (indeks = 4) nije broj 5...
          var zaokruzeniProsek = Number.parseFloat(prosek).toFixed(2); // ...na prosek primenjujemo built-in funkcije "parseFloat()" i "toFixed()".
        }
        return zaokruzeniProsek;
      }
      */

      // funkcija koja menja "disabled" svojstvo dugmeta (id = "dgmPretragaUcenika") zavisno od toga da li je polje za pretragu prazno ("disabled" = true; na dugme nije moguće kliknuti) ili ne ("disabled" = false; na dugme je moguće kliknuti). po default-u, "disabled" svojstvo je definisano kao true (disabled = "disabled").
      $('#poljePretragaUcenika').keyup(function() {
        if($(this).val() == "") {
          $('#dgmPretragaUcenika').prop("disabled", true);
        } else {
          $('#dgmPretragaUcenika').prop("disabled", false);
        }
      });

      // funkcija koja prazni polje za pretragu prilikom povratka sa stranice "pretraga.php" na stranicu na kojoj je izvršena pretraga. budući da ne prikupljamo podatke iz forme (vrednosti input-a) kako bismo ih prosledili ajax-u, nepotrebne parametre stavljamo pod komentar.
      // radi dodatne sigurnosti, komanda za pražnjenje polja za pretragu nalazi se i unutar funkcije koja se izvršava prilikom učitavanja stranice.
      function poljePretragaPrazno() {
        $.ajax({
          // url: 'pretraga.php',
          // type: 'get',
          // data: {
          //   pretraga_vrednost: $('#poljePretragaUcenika').val()
          // },
          success: function(data) {
            $('#poljePretragaUcenika').val("");
          },
          error: function(data) {
            console.log("error");
            console.log(data);
          }
        });
      }

      // funkcija koja prelaskom kursora miša preko naziva učenika menja CSS obeležja tog naziva
      function ucenik_hover(idUcenika) {
        $('#polje'+idUcenika).css({"color": "#cc1212", "cursor": "pointer", "text-decoration": "underline overline"});
      }

      // funkcija koja sklanjanjem kursora miša sa naziva učenika vraća prvobitna CSS obeležja tog naziva
      function ucenik_unhover(idUcenika) {
        $('#polje'+idUcenika).css({"color": "black", "text-decoration": "none"});
      }

      // funkcija koja prelaskom kursora miša preko naziva generacije menja CSS obeležja tog naziva
      function generacija_hover(idGeneracije, idUcenika) {
        $('#polje'+idGeneracije+idUcenika).css({"color": "#cc1212", "cursor": "pointer", "text-decoration": "underline overline"});
      }

      // funkcija koja sklanjanjem kursora miša sa naziva generacije vraća prvobitna CSS obeležja tog naziva
      function generacija_unhover(idGeneracije, idUcenika) {
        $('#polje'+idGeneracije+idUcenika).css({"color": "black", "text-decoration": "none"});
      }

      // funkcija koja prikazuje podatke o pojedinačnom učeniku (u okviru rezultata pretrage)
      function prikazUcenikaPojedPretraga(idUcenika, idGeneracije, nazivGeneracije) {
        window.scrollTo(0, 0);
        $('.container-fluid').css("width", "1420px");
        $('#srednji-okvir').empty();
        // dodavanje HTML elemenata
        $('#srednji-okvir').append('<table id="tabela-ucenici" class="table table-bordered table-condensed table-hover" style="visibility: hidden"><thead style="background-color: #e6f0f2"><tr id="uzglavlje-oceneRaz"></tr></thead><tbody id="telo-tabelaUcenici" style="background-color: #FFFFFF"></tbody></table><div id="slika-GenRaz-okvir" style="margin-top: 35px" class="text-center"></div>');
        $('#uzglavlje-oceneRaz').append('<th class="text-center" style="width: 165px; vertical-align: middle">Ime oca / majke</th><th class="text-center" style="width: 120px; vertical-align: middle">Datum rođenja</th><th class="text-center" style="width: 135px; vertical-align: middle">Mesto rođenja</th><th class="text-center" style="width: 200px; vertical-align: middle">Beleška</th>');
        $('#slika-GenRaz-okvir').append('<table id="tabela-ucenik" style="width: auto" align="center" class="table table-bordered table-condensed table-hover"><thead style="background-color: #e6f0f2"><tr id="uzglavlje-oceneRaz-pojedUcen"></tr></thead><tbody id="telo-tabelaUcenik" style="background-color: #FFFFFF"><tr id="redPojedUcenSlika'+idUcenika+'"></tr><tr id="redPojedUcenOcene'+idUcenika+'"></tr></tbody></table>');
        $('#telo-tabelaUcenici').empty();
        $("#tabela-ucenici").css("visibility", "visible");
        $.getJSON("baza/upiti.php", {
          upit: "ucenik_listingID",
          id: idUcenika
        }, function(podaci) {
          $.each(podaci, function(kljuc, vrednost) {
            $('#gen-raz-naslov').html(vrednost.ime+ " " +vrednost.prezime);
            $('#telo-tabelaUcenici').append('<tr><td class="text-center" style="vertical-align: middle">'+vrednost.ime_oca_majke+'</td><td class="text-center" style="vertical-align: middle">'+vrednost.datum_rodjenja+'</td><td class="text-center" style="vertical-align: middle">'+vrednost.mesto_rodjenja+'</td><td><div style="height: 100px; width: 200px; white-space: nowrap; overflow: scroll;">'+vrednost.komentar+'</div></td></tr>');
          });
        });
        $.getJSON("baza/upiti.php", {
          upit: "razredi_listing",
          id: idGeneracije
        }, function(podaciRazred) {
          $.each(podaciRazred, function(kljucRazred, vrednostRazred) {
            $('#uzglavlje-oceneRaz-pojedUcen').append('<th class="naslov-razredi text-center" style="width: 140px">'+nazivGeneracije+' / '+vrednostRazred.odeljenje+'</th>');
            $('#redPojedUcenSlika' + idUcenika).append('<td id="poljePojedUcenSlika'+idUcenika+vrednostRazred.id+'" class="text-center" style="vertical-align: middle; padding-top: 10px; padding-bottom: 10px"></td>'); // svakom polju predviđenom za upis fotografije dodeljujemo dve koordinate: X koordinatu dobijamo od id-a učenika, dok Y koordinatu dobijamo od id-a razreda. na taj način, svako pojedinačno polje ima jedinstvene koordinate što nam omogućava da se svakom od njih posebno "obratimo" prilikom upisa fotografije.
            $('#redPojedUcenOcene' + idUcenika).append('<td id="poljePojedUcenOcene'+idUcenika+vrednostRazred.id+'" class="text-center" style="vertical-align: middle; padding-top: 15px; padding-bottom: 15px"></td>'); // svakom polju predviđenom za upis ocena dodeljujemo dve koordinate: X koordinatu dobijamo od id-a učenika, dok Y koordinatu dobijamo od id-a razreda. na taj način, svako pojedinačno polje ima jedinstvene koordinate što nam omogućava da se svakom od njih posebno "obratimo" prilikom upisa ocena.
            $.getJSON("baza/upiti.php", {
              upit: "slika_ocene_listing_gen",
              idUcenik: idUcenika
            }, function(podaciSlikaOcene) {
              $.each(podaciSlikaOcene, function(kljucSlikaOcene, vrednostSlikaOcene) {
                if(vrednostSlikaOcene.prosek == null) {
                  vrednostSlikaOcene.prosek = "";
                }
                if(vrednostSlikaOcene.matematika == null) {
                  vrednostSlikaOcene.matematika = "";
                }
                // ukoliko na nekom polju dolazi do "preklapanja" odeljenja iz upita "slika_ocene_listing_gen" i odeljenja iz upita "razredi_listing", u to polje treba upisati odgovarajuću fotografiju, odnosno odgovarajuće ocene.
                if(vrednostSlikaOcene.odeljenje == vrednostRazred.odeljenje) {
                  $('#poljePojedUcenSlika' + idUcenika + vrednostRazred.id).html('<img src="slike/'+vrednostSlikaOcene.slika+'">');
                  $('#poljePojedUcenOcene' + idUcenika + vrednostRazred.id).html("Prosek: &nbsp<b>"+vrednostSlikaOcene.prosek+"</b><hr>"+"Matematika: &nbsp;<b>"+vrednostSlikaOcene.matematika+"</b>");
                }
              });
            });
          });
        });
      }

      // funkcija koja u okviru pojedinačne generacije izlistava pripadajuće učenike (u okviru rezultata pretrage)
      function prikazUcenikaGenPretraga(idGeneracije, nazivGeneracije) {
        window.scrollTo(0, 0);
        $('.container-fluid').css("width", "2031.5px");
        $('#srednji-okvir').empty();
        // dodavanje HTML elemenata
        $('#srednji-okvir').append('<table id="tabela-ucenici" class="table table-bordered table-condensed table-hover" style="visibility: hidden"><thead style="background-color: #e6f0f2"><tr id="uzglavlje-oceneRaz"><th class="text-center" style="vertical-align: middle">#</th><th style="width: 90px;"></th><th class="text-center" style="width: 150px; vertical-align: middle">Učenik</th><th class="text-center" style="width: 165px; vertical-align: middle">Ime oca / majke</th><th class="text-center" style="width: 120px; vertical-align: middle">Datum rođenja</th><th class="text-center" style="width: 135px; vertical-align: middle">Mesto rođenja</th><th class="text-center" style="width: 200px; vertical-align: middle">Beleška</th></tr></thead><tbody id="telo-tabelaUcenici" style="background-color: #FFFFFF"></tbody></table><div id="slika-GenRaz-okvir" style="margin-top: 35px" class="text-center"></div>');
        $.getJSON("baza/upiti.php", {
          upit: "generacije_listingSLIKA",
          id: idGeneracije
        }, function(podaci) {
          $('#slika-GenRaz-okvir').empty();
          if(podaci[0].slika == null) {
            // $('#slika-GenRaz-okvir').empty(); - ova linija koda nije neophodna, jer prazan "if" iskaz znači da, u slučaju ispunjenosti "if" uslova, važi ono što je utvrđeno pre "if"-a, a to je upravo linija koda "$('#slika-GenRaz-okvir').empty();".
          } else {
            $('#slika-GenRaz-okvir').append('<img src="slike/'+podaci[0].slika+'" class="img-thumbnail">');
          }
        });
        $('#gen-raz-naslov').html("Generacija " + "<b>"+nazivGeneracije+"</b>");
        $('#telo-tabelaUcenici').empty();
        $("#tabela-ucenici").css("visibility", "visible");
        // uklanjanje HTML elemenata
        $('.naslov-razredi').remove();
        $.getJSON("baza/upiti.php", {
          upit: "ucenici_listing_gen",
          idGeneracija: idGeneracije
        }, function(podaci) {
          var i = 1;
          $.each(podaci, function(kljuc, vrednost) {
            $('#telo-tabelaUcenici').append('<tr id="red'+vrednost.id+'"><td class="text-center" style="vertical-align: middle">'+ i++ +'</td><td class="text-center" style="vertical-align: middle"><img src="slike/'+vrednost.slika+'"></td><td style="vertical-align: middle"><span style="margin-left: 15px">'+vrednost.prezime+'</span><br><span style="margin-left: 40px">'+vrednost.ime+'</span></td><td class="text-center" style="vertical-align: middle">'+vrednost.ime_oca_majke+'</td><td class="text-center" style="vertical-align: middle">'+vrednost.datum_rodjenja+'</td><td class="text-center" style="vertical-align: middle">'+vrednost.mesto_rodjenja+'</td><td><div style="height: 100px; width: 200px; white-space: nowrap; overflow: scroll;">'+vrednost.komentar+'</div></td></tr>');
            $.getJSON("baza/upiti.php", {
              upit: "razredi_listing",
              id: idGeneracije
            }, function(podaciRazred) {
              $('.naslov-razredi').remove();
              $.each(podaciRazred, function(kljucRazred, vrednostRazred) {
                $('#uzglavlje-oceneRaz').append('<th class="naslov-razredi text-center" style="width: 120px">'+vrednostRazred.odeljenje+'</th>');
                $('#red' + vrednost.id).append('<td id="polje'+vrednost.id+vrednostRazred.id+'" class="text-center" style="vertical-align: middle"></td>'); // svakom polju predviđenom za upis ocena dodeljujemo dve koordinate: X koordinatu dobijamo od id-a učenika, dok Y koordinatu dobijamo od id-a razreda. na taj način, svako pojedinačno polje ima jedinstvene koordinate što nam omogućava da se svakom od njih posebno "obratimo" prilikom upisa ocena.
                $.getJSON("baza/upiti.php", {
                  upit: "ocene_listing_gen",
                  idUcenik: vrednost.id
                }, function(podaciOcene) {
                  $.each(podaciOcene, function(kljucOcene, vrednostOcene) {
                    if(vrednostOcene.prosek == null) {
                      vrednostOcene.prosek = "";
                    }
                    if(vrednostOcene.matematika == null) {
                      vrednostOcene.matematika = "";
                    }
                    // ukoliko na nekom polju dolazi do "preklapanja" odeljenja iz upita "ocene_listing_gen" i odeljenja iz upita "razredi_listing", u to polje treba upisati odgovarajuće ocene.
                    if(vrednostOcene.odeljenje == vrednostRazred.odeljenje) {
                      $('#polje' + vrednost.id + vrednostRazred.id).html("Prosek: &nbsp<b>"+vrednostOcene.prosek+"</b><hr>"+"Matematika: &nbsp;<b>"+vrednostOcene.matematika+"</b>");
                    }
                  });
                });
              });
            });
          });
        });
      }

      // funkcija koja izlistava rezultate pretrage (učenike) rastućim redosledom (na osnovu proseka)
      function prikazUcen_rastuceProsekPretraga() {
        $('#telo-tabelaPretragaUcen').empty();
        $('#strelica-prosek').attr("title", "Sortiraj opadajućim redosledom");
        $('#strelica-prosek').attr("onclick", "prikazUcen_opadajuceProsekPretraga()").html("&#8595;");
        $('#strelica-matematika').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-matematika').attr("onclick", "prikazUcen_rastuceMatematikaPretraga()").html("&#8593;");
        $.getJSON("baza/upiti.php", {
          upit: "ucenik_pretraga_rastuce_prosek"
        }, function(podaci) {
          var i = 1;
          $.each(podaci, function(kljuc, vrednost) {
            if(vrednost.prosekUcenika == null) {
              var zaokruzeniProsekPretraga = "";
            } else {
              var zaokruzeniProsekPretraga = zaokruzivanjeProsek(vrednost.prosekUcenika);
            }
            if(vrednost.matematikaUcenika == null) {
              var zaokruzeniMatematikaPretraga = "";
            } else {
              var zaokruzeniMatematikaPretraga = zaokruzivanjeMatematika(vrednost.matematikaUcenika);
            }
            $('#telo-tabelaPretragaUcen').append('<tr><th class="text-center" style="vertical-align: middle; height: 60px" scope="row">'+ i++ +'</th><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.ucenikID+'" onmouseover="ucenik_hover('+vrednost.ucenikID+')" onmouseout="ucenik_unhover('+vrednost.ucenikID+')" onclick="prikazUcenikaPojedPretraga('+vrednost.ucenikID+', '+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.prezimeImeUcenika+'</span></td><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.generacijaID+''+vrednost.ucenikID+'" onmouseover="generacija_hover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onmouseout="generacija_unhover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onclick="prikazUcenikaGenPretraga('+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.generacija+'</span></td><td class="text-center" style="vertical-align: middle">'+zaokruzeniProsekPretraga+'</td><td class="text-center" style="vertical-align: middle">'+zaokruzeniMatematikaPretraga+'</td></tr>');
            $('#poljePretragaUcenika').val("");
          });
        });
      }

      // funkcija koja izlistava rezultate pretrage (učenike) opadajućim redosledom (na osnovu proseka)
      function prikazUcen_opadajuceProsekPretraga() {
        $('#telo-tabelaPretragaUcen').empty();
        $('#strelica-prosek').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-prosek').attr("onclick", "prikazUcen_rastuceProsekPretraga()").html("&#8593;");
        $('#strelica-matematika').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-matematika').attr("onclick", "prikazUcen_rastuceMatematikaPretraga()").html("&#8593;");
        $.getJSON("baza/upiti.php", {
          upit: "ucenik_pretraga_opadajuce_prosek"
        }, function(podaci) {
          var i = 1;
          $.each(podaci, function(kljuc, vrednost) {
            if(vrednost.prosekUcenika == null) {
              var zaokruzeniProsekPretraga = "";
            } else {
              var zaokruzeniProsekPretraga = zaokruzivanjeProsek(vrednost.prosekUcenika);
            }
            if(vrednost.matematikaUcenika == null) {
              var zaokruzeniMatematikaPretraga = "";
            } else {
              var zaokruzeniMatematikaPretraga = zaokruzivanjeMatematika(vrednost.matematikaUcenika);
            }
            $('#telo-tabelaPretragaUcen').append('<tr><th class="text-center" style="vertical-align: middle; height: 60px" scope="row">'+ i++ +'</th><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.ucenikID+'" onmouseover="ucenik_hover('+vrednost.ucenikID+')" onmouseout="ucenik_unhover('+vrednost.ucenikID+')" onclick="prikazUcenikaPojedPretraga('+vrednost.ucenikID+', '+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.prezimeImeUcenika+'</span></td><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.generacijaID+''+vrednost.ucenikID+'" onmouseover="generacija_hover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onmouseout="generacija_unhover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onclick="prikazUcenikaGenPretraga('+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.generacija+'</span></td><td class="text-center" style="vertical-align: middle">'+zaokruzeniProsekPretraga+'</td><td class="text-center" style="vertical-align: middle">'+zaokruzeniMatematikaPretraga+'</td></tr>');
            $('#poljePretragaUcenika').val("");
          });
        });
      }

      // funkcija koja izlistava rezultate pretrage (učenike) rastućim redosledom (na osnovu prosečne ocene iz matematike)
      function prikazUcen_rastuceMatematikaPretraga() {
        $('#telo-tabelaPretragaUcen').empty();
        $('#strelica-matematika').attr("title", "Sortiraj opadajućim redosledom");
        $('#strelica-matematika').attr("onclick", "prikazUcen_opadajuceMatematikaPretraga()").html("&#8595;");
        $('#strelica-prosek').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-prosek').attr("onclick", "prikazUcen_rastuceProsekPretraga()").html("&#8593;");
        $.getJSON("baza/upiti.php", {
          upit: "ucenik_pretraga_rastuce_matematika"
        }, function(podaci) {
          var i = 1;
          $.each(podaci, function(kljuc, vrednost) {
            if(vrednost.prosekUcenika == null) {
              var zaokruzeniProsekPretraga = "";
            } else {
              var zaokruzeniProsekPretraga = zaokruzivanjeProsek(vrednost.prosekUcenika);
            }
            if(vrednost.matematikaUcenika == null) {
              var zaokruzeniMatematikaPretraga = "";
            } else {
              var zaokruzeniMatematikaPretraga = zaokruzivanjeMatematika(vrednost.matematikaUcenika);
            }
            $('#telo-tabelaPretragaUcen').append('<tr><th class="text-center" style="vertical-align: middle; height: 60px" scope="row">'+ i++ +'</th><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.ucenikID+'" onmouseover="ucenik_hover('+vrednost.ucenikID+')" onmouseout="ucenik_unhover('+vrednost.ucenikID+')" onclick="prikazUcenikaPojedPretraga('+vrednost.ucenikID+', '+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.prezimeImeUcenika+'</span></td><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.generacijaID+''+vrednost.ucenikID+'" onmouseover="generacija_hover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onmouseout="generacija_unhover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onclick="prikazUcenikaGenPretraga('+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.generacija+'</span></td><td class="text-center" style="vertical-align: middle">'+zaokruzeniProsekPretraga+'</td><td class="text-center" style="vertical-align: middle">'+zaokruzeniMatematikaPretraga+'</td></tr>');
            $('#poljePretragaUcenika').val("");
          });
        });
      }

      // funkcija koja izlistava rezultate pretrage (učenike) opadajućim redosledom (na osnovu prosečne ocene iz matematike)
      function prikazUcen_opadajuceMatematikaPretraga() {
        $('#telo-tabelaPretragaUcen').empty();
        $('#strelica-matematika').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-matematika').attr("onclick", "prikazUcen_rastuceMatematikaPretraga()").html("&#8593;");
        $('#strelica-prosek').attr("title", "Sortiraj rastućim redosledom");
        $('#strelica-prosek').attr("onclick", "prikazUcen_rastuceProsekPretraga()").html("&#8593;");
        $.getJSON("baza/upiti.php", {
          upit: "ucenik_pretraga_opadajuce_matematika"
        }, function(podaci) {
          var i = 1;
          $.each(podaci, function(kljuc, vrednost) {
            if(vrednost.prosekUcenika == null) {
              var zaokruzeniProsekPretraga = "";
            } else {
              var zaokruzeniProsekPretraga = zaokruzivanjeProsek(vrednost.prosekUcenika);
            }
            if(vrednost.matematikaUcenika == null) {
              var zaokruzeniMatematikaPretraga = "";
            } else {
              var zaokruzeniMatematikaPretraga = zaokruzivanjeMatematika(vrednost.matematikaUcenika);
            }
            $('#telo-tabelaPretragaUcen').append('<tr><th class="text-center" style="vertical-align: middle; height: 60px" scope="row">'+ i++ +'</th><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.ucenikID+'" onmouseover="ucenik_hover('+vrednost.ucenikID+')" onmouseout="ucenik_unhover('+vrednost.ucenikID+')" onclick="prikazUcenikaPojedPretraga('+vrednost.ucenikID+', '+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.prezimeImeUcenika+'</span></td><td class="text-center" style="vertical-align: middle; color: black"><span id="polje'+vrednost.generacijaID+''+vrednost.ucenikID+'" onmouseover="generacija_hover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onmouseout="generacija_unhover('+vrednost.generacijaID+', '+vrednost.ucenikID+')" onclick="prikazUcenikaGenPretraga('+vrednost.generacijaID+', '+vrednost.generacija+')">'+vrednost.generacija+'</span></td><td class="text-center" style="vertical-align: middle">'+zaokruzeniProsekPretraga+'</td><td class="text-center" style="vertical-align: middle">'+zaokruzeniMatematikaPretraga+'</td></tr>');
            $('#poljePretragaUcenika').val("");
          });
        });
      }
    </script>
  </body>
</html>
