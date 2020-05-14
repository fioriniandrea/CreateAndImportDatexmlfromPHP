<?php 
set_time_limit(0);
ignore_user_abort(1);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/xml; charset=ISO-8859-1" />
<title>Documento senza titolo</title>
</head>

<body>
<?php 
//header ('content-type: text/xml; charset:ISO-8859-7');
require '../config.inc.php';
require_once '../PHPMailer_v5.1/class.phpmailer.php';


//header ("Pragma: no-cache");
//header ("Expires: 0");
$xml=new DOMDocument('1.0');
$inserzionista=$xml->createElement("inserzionista");
$inserzionista->setAttribute("id",70);
$xml->appendChild($inserzionista);
$link=mysql_pconnect($db_host, $db_user, $db_password) or toErrorLog ("config.php -> impossibile connettersi a $db_host"); 
mysql_select_db($db_name);    
$sql_utenti="select id, username, id_immobil_click, email, ragione from utenti where visualizzainconsimm=1 order by id ASC";
$result_utenti=mysql_query($sql_utenti) or die(mysql_error());

while($row_utenti=mysql_fetch_array($result_utenti)){
	
	$agenzia=$xml->createElement("agenzia");
	$agenzia->setAttribute("id",$row_utenti['id_immobil_click']);
	$inserzionista->appendChild($agenzia);
	
	$email=$xml->createElement("email");
	$agenzia->appendChild($email);
	$email2=$xml->createCDATASection($row_utenti['email']);
	$email->appendChild($email2);
	
	$annunci=$xml->createElement("annunci");
	$agenzia->appendChild($annunci);
	
	$queryannunci="SELECT annunci.*, istat_regioni.*, istat_province.*, istat_comuni.* from annunci
	JOIN istat_comuni ON annunci.codice_comune=istat_comuni.codice_comune
	JOIN istat_province ON annunci.codice_provincia=istat_province.codice_provincia
	JOIN istat_regioni ON annunci.codice_regione=istat_regioni.codice_regione
	WHERE istat_regioni.codice_regione <>21 AND annunci.utente='$row_utenti[username]' AND annunci.id>27000 AND annunci.attivo=1 ORDER BY id DESC";
	$result_annunci=mysql_query($queryannunci) or die(mysql_error());
	while($row_annunci=mysql_fetch_array($result_annunci)){
		$descr=$row_annunci['descrizione'];
		$annuncio=$xml->createElement("annuncio");
		$annuncio->setAttribute("id",$row_annunci['id']);
		$annunci->appendChild($annuncio);
		
		$data=$xml->createElement("data", date("d/m/Y",strtotime($row_annunci['ultimamodifica'])));
		$annuncio->appendChild($data);
		
		if($row_annunci['rinnovabile']!=''){
			$riferimento=$row_annunci['rinnovabile'];
		}else{ $riferimento=$row_annunci['id'];
		}
		
		$riferimento2=$xml->createElement("riferimento");
		$annuncio->appendChild($riferimento2);
		$riferimento3=$xml->createCDATASection(htmlentities($riferimento, ENT_NOQUOTES, 'UTF-8', false));
		$riferimento2->appendChild($riferimento3);
		
		$provincia=$xml->createElement("provincia", $row_annunci['sigla_provincia']);
		$annuncio->appendChild($provincia);
		
		$comune=$xml->createElement("comune");
		$annuncio->appendChild($comune);
		$comune2=$xml->createCDATASection($row_annunci['nome_comune']);
		$comune->appendChild($comune2);
		
		$indirizzo=$xml->createElement("indirizzo");
		$annuncio->appendChild($indirizzo);
		$indirizzo2=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['indirizzo'])));
		$indirizzo->appendChild($indirizzo2);
		
		if($row_annunci['categoria']=="Vendita residenziale")
	{  $contratto="1"; $categoria="1"; }
	elseif($row_annunci['categoria']=="Vendita commerciale")
	{  $contratto="1"; $categoria="2"; }
    elseif($row_annunci['categoria']=="Affitto residenziale")
	{  $contratto="2"; $categoria="1"; }
	elseif($row_annunci['categoria']=="Affitto commerciale")
	{  $contratto="2"; $categoria="2"; }
	
	$contratto=$xml->createElement("contratto", $contratto);
	$annuncio->appendChild($contratto);
	
	$categoria=$xml->createElement("categoria", $categoria);
	$annuncio->appendChild($categoria);
	
	if($row_annunci['tipologia']=="Altro") $tipologia=1;
    elseif($row_annunci['tipologia']=="Appartamento") $tipologia=1;
    elseif($row_annunci['tipologia']=="Attico") $tipologia=2;
    elseif($row_annunci['tipologia']=="Attivita' Commerciale") $tipologia=21;
    elseif($row_annunci['tipologia']=="Bilocale") $tipologia=1;
    elseif($row_annunci['tipologia']=="Capannoni") $tipologia=24;
    elseif($row_annunci['tipologia']=="Casa Bi/Trifamiliare") $tipologia=16;
    elseif($row_annunci['tipologia']=="Casa indipendente") $tipologia=5;
    elseif($row_annunci['tipologia']=="Case Vacanze") $tipologia=34;
    elseif($row_annunci['tipologia']=="Cinque vani") $tipologia=1;
    elseif($row_annunci['tipologia']=="Complesso Residenziale") $tipologia=3;
    elseif($row_annunci['tipologia']=="Complesso Turistico") $tipologia=34;
    elseif($row_annunci['tipologia']=="Fondo Commerciale") $tipologia=27;
    elseif($row_annunci['tipologia']=="Garage/Box") $tipologia=4;
    elseif($row_annunci['tipologia']=="Hotel") $tipologia=20;
    elseif($row_annunci['tipologia']=="Locale Commerciale") $tipologia=27;
    elseif($row_annunci['tipologia']=="Loft residenziale") $tipologia=8;
    elseif($row_annunci['tipologia']=="Magazzino artigianale") $tipologia=28;
    elseif($row_annunci['tipologia']=="Monolocale") $tipologia=1;
    elseif($row_annunci['tipologia']=="Nuova costruzione") $tipologia=1;
    elseif($row_annunci['tipologia']=="Posto Auto ") $tipologia=4;
    elseif($row_annunci['tipologia']=="Quattro Vani") $tipologia=1;
    elseif($row_annunci['tipologia']=="Rustico") $tipologia=10;
    elseif($row_annunci['tipologia']=="Sei Vani ed oltre") $tipologia=1;
    elseif($row_annunci['tipologia']=="Terratetto") $tipologia=5;
    elseif($row_annunci['tipologia']=="Terreno") $tipologia=13;
    elseif($row_annunci['tipologia']=="Terreno edificabile") $tipologia=14;
    elseif($row_annunci['tipologia']=="Tre Vani") $tipologia=1;
    elseif($row_annunci['tipologia']=="Ufficio") $tipologia=33;
    elseif($row_annunci['tipologia']=="Viareggina") $tipologia=18;
    elseif($row_annunci['tipologia']=="Villa") $tipologia=17;
    else $tipologia=1;
	
	$tipologia=$xml->createElement("tipologia", $tipologia);
	$annuncio->appendChild($tipologia);
	
	$descrizione=$xml->createElement("descrizione");//, htmlspecialchars($row_annunci['descrizione'], ENT_NOQUOTES, 'UTF-8', false));
	$annuncio->appendChild($descrizione);
	$descrizione1=$xml->createCDATASection(utf8_encode(html_entity_decode($descr)));
	$descrizione->appendChild($descrizione1);	//htmlspecialchars_decode(// html_entity_decode()
	
	if($row_annunci['descrizioneinglese']!=""){
	$descrizioneing=$xml->createElement("descrizione_EN");
	$annuncio->appendChild($descrizioneing);
	$descrizioneing1=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['descrizioneinglese'])));
	$descrizioneing->appendChild($descrizioneing1);
	}
	if($row_annunci['descrizionespagnolo']!=""){
	$descrizionespa=$xml->createElement("descrizione_ES");
	$annuncio->appendChild($descrizionespa);
	$descrizionespa1=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['descrizionespagnolo'])));
	$descrizionespa->appendChild($descrizionespa1);
	}
	if($row_annunci['descrizionefrancese']!=""){
	$descrizionefra=$xml->createElement("descrizione_FR");
	$annuncio->appendChild($descrizionefra);
	$descrizionefra1=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['descrizionefrancese'])));
	$descrizionefra->appendChild($descrizionefra1);
	}
	
	if($row_annunci['descrizionetedesco']!=""){
	$descrizionede=$xml->createElement("descrizione_DE");
	$annuncio->appendChild($descrizionede);
	$descrizionede1=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['descrizionetedesco'])));
	$descrizionede->appendChild($descrizionede1);
	}
	if($row_annunci['descrizionerusso']!=""){
	$descrizioneruss=$xml->createElement("descrizione_RU");
	$annuncio->appendChild($descrizioneruss);
	$descrizioneruss1=$xml->createCDATASection(utf8_encode(html_entity_decode($row_annunci['descrizionerusso'])));
	$descrizioneruss->appendChild($descrizioneruss1);
	}
	
	
	$prezzo=$xml->createElement("prezzo", $row_annunci['prezzo']);
	$annuncio->appendChild($prezzo);
	
	/*$locali=$xml->createElement("locali");
	$annuncio->appendChild($locali);*/
	
	$mq=$xml->createElement("mq", $row_annunci['mq']);
	$annuncio->appendChild($mq);
	
	$servizi=$xml->createElement("servizi", $row_annunci['numerobagni']);
	$annuncio->appendChild($servizi);
	
	 if($row_annunci['riscaldamento']=="Autonomo") $riscaldamento=1;
	 elseif($row_annunci['riscaldamento']=="Centralizzato") $riscaldamento=2;
	 else $riscaldamento=0;
	$riscaldamento2=$xml->createElement("riscaldamento", $riscaldamento);
	$annuncio->appendChild($riscaldamento2);
	
	if($row_annunci['statofabbricato']=="Buono") $condizioni=3;
	 elseif($row_annunci['statofabbricato']=="Da ristrutturare") $condizioni=4;
	 elseif($row_annunci['statofabbricato']=="Ristrutturato") $condizioni=2;
	 elseif($row_annunci['statofabbricato']=="Nuovo" || $row_annunci['statofabbricato']=="In Costruzione" )$condizioni=1;
	 else $condizioni=0;
	$condizioni2=$xml->createElement("condizioni", $condizioni);
	$annuncio->appendChild($condizioni2);
	
	if($row_annunci['statoimmobile']=="Box Auto") $box=3;
	 elseif($row_annunci['statoimmobile']=="Garage") $box=3;
	 elseif($row_annunci['statoimmobile']=="Posto auto") $box=3;
	 elseif($row_annunci['statoimmobile']=="P.auto coperto") $box=3;
	 else $box=0;
	$box2=$xml->createElement("box", $box);
	$annuncio->appendChild($box2);
	
	 $numeroimmagine=1;
	 $direct=(substr($row_annunci['id'],0,2)*10000).'/'.$row_annunci['id'];
	 if(@fopen('http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.jpg', "r")){
		 $foto1=$xml->createElement("foto1");
		 $annuncio->appendChild($foto1);
		 $foto1cd=$xml->createCDATASection("http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/".$direct."/".$row_annunci['id'].".jpg");
		 $foto1->appendChild($foto1cd); 
	 }
	 
	 
	 for($numeroimmagine==2; $numeroimmagine<=10; $numeroimmagine++)
	 {
	   if(@fopen('http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct."/".$row_annunci['id'].'-'.$numeroimmagine.'.jpg', "r")){
			$nuovafoto=$foto.$numeroimmagine;
			$nuovafoto=$xml->createElement("foto".$numeroimmagine);
			$annuncio->appendChild($nuovafoto);
			$nuovafoto2=$foto.$numeroimmagine;
			$nuovafoto2=$xml->createCDATASection("http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/".$direct."/".$row_annunci['id']."-".$numeroimmagine.".jpg");
			$nuovafoto->appendChild($nuovafoto2);
	   }
		}
		
		$codice_istat=$xml->createElement("codice_istat");
		$annuncio->appendChild($codice_istat);
		$codice_istatcd=$xml->createCDATASection($row_annunci['codice_comune']);
		$codice_istat->appendChild($codice_istatcd);
		
		if($row_annunci['terrazzo']=="Terrazzo"){
		$terrazzo=$xml->createElement("terrazzo", 1);
		$annuncio->appendChild($terrazzo);
		}else if($row_annunci['terrazzo']=="Balcone"){
		$balcone=$xml->createElement("balcone", 1);
		$annuncio->appendChild($balcone);
		}else if($row_annunci['terrazzo']=="Balcone e terrazzo"){
		$terrazzo=$xml->createElement("terrazzo", 1);
		$annuncio->appendChild($terrazzo);
		$balcone=$xml->createElement("balcone", 1);
		$annuncio->appendChild($balcone);
		}
		
		if($row_annunci['giardino']=="Si"){
			$giardino=$xml->createElement("giardino", 1);
			$annuncio->appendChild($giardino);
		}
		if($row_annunci['piano']=="Attico") $piano=15;
	 elseif($row_annunci['piano']=="Piano rialzato") $piano=16;
	 elseif($row_annunci['piano']=="Seminterrato") $piano=2;
	 elseif($row_annunci['piano']=="Su due livelli") $piano=17;
	 elseif($row_annunci['piano']=="Terra") $piano=3;
	 elseif($row_annunci['piano']=="Ultimo piano") $piano=15;
	 elseif($row_annunci['piano']=="1") $piano=4;
	 elseif($row_annunci['piano']=="2") $piano=5;
	 elseif($row_annunci['piano']=="3") $piano=6;
	 elseif($row_annunci['piano']=="4") $piano=7;
	 elseif($row_annunci['piano']=="5") $piano=8;
	 elseif($row_annunci['piano']=="6") $piano=9;
	 elseif($row_annunci['piano']=="7") $piano=10;
	 elseif($row_annunci['piano']=="8") $piano=11;
	 elseif($row_annunci['piano']=="9") $piano=12;
	 elseif($row_annunci['piano']=="10") $piano=13;
	 elseif(!empty($row_annunci['piano']) and is_numeric($row_annunci['piano']) and intval($row_annunci['piano'])>10) $piano=14;
	 else $piano=0;
		$piano2=$xml->createElement("piano",$piano);
		$annuncio->appendChild($piano2);
		
		 if($row_annunci['cucina']=="Cucina") $cucina=1;
	 elseif($row_annunci['cucina']=="Cucina abitabile") $cucina=1;
	 elseif($row_annunci['cucina']=="Cucinotto") $cucina=3;
	 else $cucina=0;
	 
	 $cucina2=$xml->createElement("cucina", $cucina);
	 $annuncio->appendChild($cucina2);
	 
	 $ipe=$xml->createElement("ipe", $row_annunci['ipe']);
	 $annuncio->appendChild($ipe);
	 
	 $classe_energetica=$xml->createElement("classe_energetica", $row_annunci['classeenergetica']);
	 $annuncio->appendChild($classe_energetica);
	 
	}
	
}
$xml->saveXML();
$xml->save("immobilclick.xml") or die("errore");
?>
</body>
</html>
