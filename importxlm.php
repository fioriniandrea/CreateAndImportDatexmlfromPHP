<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento senza titolo</title>
</head>

<body>
<?php
/*
  Genera File XML per esportazione annunci su www.immobilclick.it
  
  Basato su Specifiche tecniche "DataLoad-Batch versione 1.5 del 21/12/2011
  
  Creato da Alessandro Scola il 12/03/2012
  
  Aggiornato da Leonardo Telesca il 12/02/2018
  
  
*/
$inserzionista_id="70"; // NON CAMBIARE QUESTO VALORE !!!
?>
<?php
require '../config.inc.php';
require_once '../PHPMailer_v5.1/class.phpmailer.php';

header ('content-type: text/xml; charset: ISO-8859-1');
header ("Pragma: no-cache");
header ("Expires: 0");

$link=mysql_pconnect($db_host, $db_user, $db_password) or toErrorLog ("config.php -> impossibile connettersi a $db_host"); 
mysql_select_db($db_name);    


$xml="<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\r\n
\t<inserzionista id=\"".$inserzionista_id."\">\r\n";

$sql_utenti="select id, username, email, ragione, id_immobil_click from utenti where visualizzainconsimm=1";
$result_utenti=mysql_query($sql_utenti) or die(mysql_error());
while($row_utenti=mysql_fetch_array($result_utenti))
{
  $xml.="\t\t<agenzia id=\"".$row_utenti['id_immobil_click']."\">\r\n";
  $xml.="\t\t\t<email><![CDATA[".$row_utenti['email']."]]></email>\r\n";
  $xml.="\t\t\t<annunci>\r\n";

  // Seleziona solo gli annunci NON esteri cioè codice_regione<>21!!
  $sql_annunci="select * from annunci inner join istat_comuni on (annunci.codice_comune=istat_comuni.codice_comune) inner join istat_province on (annunci.codice_provincia=istat_province.codice_provincia) where annunci.codice_regione<>21 and utente='".$row_utenti['username']."' and annunci.id>23000 and annunci.attivo=1 order by id DESC"; 
  

  $result_annunci=mysql_query($sql_annunci) or die(mysql_error());
  while($row_annunci=mysql_fetch_array($result_annunci))
  {
	// Stampa gli annunci dell'utente:
	// --- INIZIO DATI OBBLIGATORI:
	$xml.="\t\t\t\t<annuncio id=\"".$row_annunci['id']."\">\r\n";
	$xml.="\t\t\t\t\t<data>".date("d/m/Y",strtotime($row_annunci['ultimamodifica']))."</data>\r\n";

	if(trim($row_annunci['rinnovabile'])!='') $riferimento=$row_annunci['rinnovabile'];
	else $riferimento=$row_annunci['id'];
	$riferimento=substr($riferimento,0,20);
	
	$xml.="\t\t\t\t\t<riferimento><![CDATA[".$riferimento."]]></riferimento>\r\n";
	$xml.="\t\t\t\t\t<provincia>".$row_annunci['sigla_provincia']."</provincia>\r\n";
	$xml.="\t\t\t\t\t<comune><![CDATA[".substr($row_annunci['nome_comune'],0,100)."]]></comune>\r\n";
	if(!empty($row_annunci['indirizzo'])) $xml.="\t\t\t\t\t<indirizzo><![CDATA[".$row_annunci['indirizzo']."]]></indirizzo>\r\n";
	
	if($row_annunci['categoria']=="Vendita residenziale")
	{  $contratto="1"; $categoria="1"; }
	elseif($row_annunci['categoria']=="Vendita commerciale")
	{  $contratto="1"; $categoria="1"; }
    elseif($row_annunci['categoria']=="Affitto residenziale")
	{  $contratto="2"; $categoria="2"; }
	elseif($row_annunci['categoria']=="Affitto commerciale")
	{  $contratto="2"; $categoria="2"; }
	
	$xml.="\t\t\t\t\t<contratto>".$contratto."</contratto>\r\n";
	$xml.="\t\t\t\t\t<categoria>".$categoria."</categoria>\r\n";
	
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
	
	$xml.="\t\t\t\t\t<tipologia>".$tipologia."</tipologia>\r\n";
	$xml.="\t\t\t\t\t<descrizione><![CDATA[".$row_annunci['descrizione']."]]></descrizione>\r\n";
	if($row_annunci['descrizioneinglese']!=""){
		$xml.="\t\t\t\t\t<descrizione_EN><![CDATA[".$row_annunci['descrizioneinglese']."]]></descrizione_EN>\r\n";;
	}
	if($row_annunci['descrizionespagnolo']!=""){
		$xml.="\t\t\t\t\t<descrizione_ES><![CDATA[".$row_annunci['descrizionespagnolo']."]]></descrizione_ES>\r\n";;
	}
	if($row_annunci['descrizionefrancese']!=""){
		$xml.="\t\t\t\t\t<descrizione_FR><![CDATA[".$row_annunci['descrizionefrancese']."]]></descrizione_FR>\r\n";;
	}
	if($row_annunci['descrizionetedesco']!=""){
		$xml.="\t\t\t\t\t<descrizione_DE><![CDATA[".$row_annunci['descrizionetedesco']."]]></descrizione_DE>\r\n";;
	}
	if($row_annunci['descrizionerusso']!=""){
		$xml.="\t\t\t\t\t<descrizione_RU><![CDATA[".$row_annunci['descrizionerusso']."]]></descrizione_RU>\r\n";;
	}
	
	// --- FINE DATI OBBLIGATORI:
	
	//if(!empty($row_annunci['cap'])) $xml.="\t\t\t\t\t<cap>".$row_annunci['cap']."</cap>\r\n";
    
	if(!empty($row_annunci['prezzo'])) $xml.="\t\t\t\t\t<prezzo>".$row_annunci['prezzo']."</prezzo>\r\n";
	if(!empty($row_annunci['mq']) and is_numeric($row_annunci['mq'])) $xml.="\t\t\t\t\t<mq>".$row_annunci['mq']."</mq>\r\n";
	 $xml.="\t\t\t\t\t<servizi>".$row_annunci['numerobagni']."</servizi>\r\n";
	 
	 if($row_annunci['riscaldamento']=="Autonomo") $riscaldamento=1;
	 elseif($row_annunci['riscaldamento']=="Centralizzato") $riscaldamento=2;
	 else $riscaldamento=0;
	 $xml.="\t\t\t\t\t<riscaldamento>".$riscaldamento."</riscaldamento>\r\n";
	 
	 if($row_annunci['statofabbricato']=="Buono") $condizioni=3;
	 elseif($row_annunci['statofabbricato']=="Da ristrutturare") $condizioni=4;
	 elseif($row_annunci['statofabbricato']=="Ristrutturato") $condizioni=2;
	 elseif($row_annunci['statofabbricato']=="Nuovo" || $row_annunci['statofabbricato']=="In Costruzione" )$condizioni=1;
	 else $condizioni=0;
	 $xml.="\t\t\t\t\t<condizioni>".$condizioni."</condizioni>\r\n";
	 
	 if($row_annunci['statoimmobile']=="Box Auto") $box=3;
	 elseif($row_annunci['statoimmobile']=="Garage") $box=3;
	 elseif($row_annunci['statoimmobile']=="Posto auto") $box=3;
	 elseif($row_annunci['statoimmobile']=="P.auto coperto") $box=3;
	 else $box=0;
	 $xml.="\t\t\t\t\t<box>".$box."</box>\r\n";
	 
	 $numeroimmagine=1;
	 $direct=(substr($row_annunci['id'],0,2)*10000).'/'.$row_annunci['id'];
	 if(@fopen('http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.jpg', "r")){
		 $nomefoto='http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.jpg';
	 }else if(@fopen('http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.jpeg', "r")){
		 $nomefoto='http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.jpeg';
	 }else if(@fopen('http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.png', "r")){
		 $nomefoto='http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'.png';
	 }
	  $xml.="\t\t\t\t\t<foto".$numeroimmagine."><![CDATA[".$nomefoto."]]></foto".$numeroimmagine.">\r\n";
	

	for($numeroimmagine==2; $numeroimmagine<=10; $numeroimmagine++)
	 {
	   if(file_exists('../gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'-'.$numeroimmagine.'.jpg')){
	  $xml.="\t\t\t\t\t<foto".$numeroimmagine."><![CDATA[http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/".$direct."/".$row_annunci['id']."-".$numeroimmagine.".jpg]]></foto".$numeroimmagine.">\r\n";
	 }else if(file_exists('../gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'-'.$numeroimmagine.'.jpeg')){
		 $xml.="\t\t\t\t\t<foto".$numeroimmagine."><![CDATA[http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/".$direct."/".$row_annunci['id']."-".$numeroimmagine.".jpeg]]></foto".$numeroimmagine.">\r\n";
	 }else if(file_exists('../gestionale/pannello/includes/foto/server/php/files/'.$direct.'/'.$row_annunci['id'].'-'.$numeroimmagine.'.png')){
		 $xml.="\t\t\t\t\t<foto".$numeroimmagine."><![CDATA[http://www.consimm.org/gestionale/pannello/includes/foto/server/php/files/".$direct."/".$row_annunci['id']."-".$numeroimmagine.".png]]></foto".$numeroimmagine.">\r\n";
	 }
	 }
	 $xml.="\t\t\t\t\t<codice_istat><![CDATA[".sprintf("%06d",$row_annunci['codice_comune'])."]]></codice_istat>\r\n";
    
	if($row_annunci['terrazzo']=="Terrazzo"){
		$xml.="\t\t\t\t\t<terrazzo>1</terrazzo>\r\n";
	}else if($row_annunci['terrazzo']=="Balcone"){
		$xml.="\t\t\t\t\t<balcone>1</balcone>\r\n";
	}else if($row_annunci['terrazzo']=="Balcone e terrazzo"){
		$xml.="\t\t\t\t\t<terrazzo>1</terrazzo>\r\n";
		$xml.="\t\t\t\t\t<balcone>1</balcone>\r\n";
	}

	 
	 if($row_annunci['giardino']=="Si")  
	 $xml.="\t\t\t\t\t<giardino>1</giardino>\r\n";
	 
	 
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
	 $xml.="\t\t\t\t\t<piano>".$piano."</piano>\r\n";
	 
	 if($row_annunci['cucina']=="Cucina") $cucina=1;
	 elseif($row_annunci['cucina']=="Cucina abitabile") $cucina=1;
	 elseif($row_annunci['cucina']=="Cucinotto") $cucina=3;
	 else $cucina=0;
	 $xml.="\t\t\t\t\t<cucina>".$cucina."</cucina>\r\n";
		
	 if(!empty($row_annunci['ipe']) and is_numeric(str_replace(",",".",$row_annunci['ipe'])) ) 
     {
	   $ipe=$row_annunci['ipe'];	  
	   $xml.="\t\t\t\t\t<ipe>".$ipe."</ipe>\r\n";
	   //$xml.="\t\t\t\t\t<ipe_um>2</ipe_um>>\r\n";
	 }
	 
     if($row_annunci['classeenergetica']=="A") $classe_energetica=2;
	 elseif($row_annunci['classeenergetica']=="B") $classe_energetica=3;
	 elseif($row_annunci['classeenergetica']=="C") $classe_energetica=4;
	 elseif($row_annunci['classeenergetica']=="D") $classe_energetica=5;
	 elseif($row_annunci['classeenergetica']=="E") $classe_energetica=6;
	 elseif($row_annunci['classeenergetica']=="F") $classe_energetica=7;
	 elseif($row_annunci['classeenergetica']=="G") $classe_energetica=8;
	 else $classe_energetica=0;
	 $xml.="\t\t\t\t\t<classe_energetica>".$classe_energetica."</classe_energetica>\r\n";
	
	 
	 $xml.="\t\t\t\t</annuncio>\r\n";
  }  
  $xml.="\t\t\t</annunci>\r\n";
  $xml.="\t\t</agenzia>\r\n";
}
$xml.="\t</inserzionista>";

file_put_contents($inserzionista_id.".xml",$xml);
?>
<?php
 //elimina file "esito.htm"
//unlink("esito.htm");

//Importa il file XML nel gestionale di immobilclick:
//$pagina = file_get_contents("http://www.portaliimmobiliari.com/EDITORI/dataload/GetXML.asp?id=70&url=http://www.noicollaboriamo.com/immobilclick/70.xml");

//file_put_contents("esito.htm",$pagina);

// Invia mail ad admin con esito importazione
/*$mail = new PHPmailer();

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8';
$body="<html><body>Esito importazione XML su immobilclick.it<br /><br />";
$esito=file_get_contents("esito.htm");
$body.=$esito;
$body.="</body></html>";
$mail->Body=$body;	

$mail->Subject="Esito importazione XML su immobilclick.it";
$mail->SetFrom ("info@noicollaboriamo.com","noicollaboriamo.com");

// aggiunge uno ad uno i destinatari dellamail
$destinatari="info@noicollaboriamo.com";
$tok = strtok($destinatari, ", ");
while ($tok !== false) {
 	$mail->AddAddress($tok);
    $tok = strtok(", ");
}

$mail->Send();*/
?>

</body>
</html>
