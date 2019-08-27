<?php
	function obradaSlike($slika) {
		$sirinaSlike = 80;
		$visinaSlike = 80;
		$slikaSaPutanjom = "../slike/".$slika;
		$info = getimagesize($slikaSaPutanjom);
		$tipSlike = image_type_to_mime_type($info[2]);

    switch ($tipSlike) {
	    case 'image/jpeg':
  		  $radnaSlika = imagecreatefromjpeg($slikaSaPutanjom);
  		  break;
	    case 'image/gif':
  		  $radnaSlika = imagecreatefromgif($slikaSaPutanjom);
  		  break;
	    case 'image/png':
  		  $radnaSlika = imagecreatefrompng($slikaSaPutanjom);
  		  break;
	    default:
		    die('Invalid image type.');
		}

    $novaSirina = $sirinaSlike;
    $novaVisina = $visinaSlike;

		$radnaSlika_sirina = imagesx($radnaSlika);
		$radnaSlika_visina = imagesy($radnaSlika);

		$radnaSlika_proporcija = $radnaSlika_sirina/$radnaSlika_visina;
		if($radnaSlika_sirina > $radnaSlika_visina) {
			$novaVisina = $visinaSlike;
			$novaSirina = $sirinaSlike*$radnaSlika_proporcija;
		} else {
			$novaSirina = $sirinaSlike;
			$novaVisina = $visinaSlike/$radnaSlika_proporcija;
		}

		$izlaznaSlika = imagecreatetruecolor(round($novaSirina), round($novaVisina));
		imagecopyresampled($izlaznaSlika, $radnaSlika, 0, 0, 0, 0, $novaSirina, $novaVisina, $radnaSlika_sirina, $radnaSlika_visina);
		if(imagejpeg($izlaznaSlika, $slikaSaPutanjom, 80)) {
			return true;
		} else {
  	  return false;
		}
	}
?>
