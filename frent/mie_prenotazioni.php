<?php
//pagina totalmente funzionante e valida
require_once "load_Frent.php";
require_once "./components/form_functions.php";

$pagina = file_get_contents("./components/mie_prenotazioni.html");
if (isset($_SESSION["user"])) {
    require_once "./load_header.php";
    $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
    
    $content = "";
    // pescare le prenotazioni correnti
    $prenotazioni =null;
    try {
        $prenotazioni = $frent->getPrenotazioniGuest();
    } catch (Eccezione $e) {
        $_SESSION["msg"] = $e->getMessage();
        header("Location: ./error_page.php");
    }
    $prenotazioni_future = "<h1>Le mie prenotazioni future</h1><ul id=\"prenotazioni_future\"> ";
    $prenotazioni_passate = "<h1>Le mie prenotazioni passate</h1><ul id=\"prenotazioni_passate\">";
    $prenotazioni_correnti = "<h1>Le mie prenotazioni correnti</h1><ul id=\"prenotazioni_correnti\">";
    $data_corrente = date("Y-m-d");
    
    $numPrenotazioniPassate = 0;
    $numPrenotazioniCorrenti = 0;
    $numPrenotazioniFuture = 0;
    foreach ($prenotazioni as $prenotazione) {
        $id_prenotazione = $prenotazione->getIdPrenotazione();
        $annuncio= null;
        $host = null;
        try {
            $annuncio = $frent->getAnnuncio($prenotazione->getIdAnnuncio());
            $host = $frent->getUser($annuncio->getIdHost());
        } catch (Eccezione $e) {
            $_SESSION["msg"] = $e->getMessage();
            header("Location: ./error_page.php");
        }
        $mail = $host->getMail();
        $nomeAnnuncio = $annuncio->getTitolo();
        $descrizionefoto = $annuncio->getDescAnteprima();
        $luogoAlloggio = $annuncio->getIndirizzo().", ".$annuncio->getCitta();
        $dataInizio = $prenotazione->getDataInizio();
        $dataFine = $prenotazione->getDataFine();
        $totalePrenotazione = 0.0;
        $durata=abs(strtotime($prenotazione->getDataFine())-strtotime($prenotazione->getDataInizio()))/(3600*24);
        $prezzo = $annuncio->getPrezzoNotte()* intval($durata) * $prenotazione->getNumOspiti();
        $path = uploadsFolder() . $annuncio->getImgAnteprima();
        $numeroOspiti = $prenotazione->getNumOspiti();
        if ($prenotazione->getDataFine() < $data_corrente) { // prenotazioni passate
            $numPrenotazioniPassate++;
            $item = file_get_contents("./components/item_prenotazione_passata.html");
            $item = str_replace("<ID/>",$id_prenotazione,$item);
            $item = str_replace("<TITOLO/>",$annuncio->getTitolo(),$item);
            $item = str_replace("<PATH/>",$path,$item);
            $item = str_replace("<INDIRIZZO/>",$luogoAlloggio,$item);
            $item = str_replace("<DI/>",$dataInizio,$item);
            $item = str_replace("<DF/>",$dataFine,$item);
            $item = str_replace("<PREZZO/>",$prezzo,$item);
            $item = str_replace("<NO/>",$numeroOspiti,$item);
            $item = str_replace("<DESC/>", "./get_desc_anteprima_annuncio.php?id_annuncio=".$annuncio->getIdAnnuncio(),$item);
            $prenotazioni_passate .= $item;
        } elseif ($data_corrente <= $prenotazione->getDataFine() and $data_corrente >= $prenotazione->getDataInizio()) { //prenotazioni correnti
            
            $numPrenotazioniCorrenti++;
            $item = file_get_contents("./components/item_prenotazione_corrente.html");
            $item = str_replace("<ID/>",$id_prenotazione,$item);
            $item = str_replace("<MAIL/>", $mail,$item);
            $item = str_replace("<TITOLO/>",$nomeAnnuncio,$item);
            $item = str_replace("<PATH/>",$path,$item);
            $item = str_replace("<INDIRIZZO/>",$luogoAlloggio,$item);
            $item = str_replace("<DI/>",$dataInizio,$item);
            $item = str_replace("<DF/>",$dataFine,$item);
            $item = str_replace("<PREZZO/>",$prezzo,$item);
            $item = str_replace("<NO/>",$numeroOspiti,$item);
            $item = str_replace("<DESC/>", "./get_desc_anteprima_annuncio.php?id_annuncio=".$annuncio->getIdAnnuncio(),$item);
            $prenotazioni_correnti .= $item;
        
        } else if ($data_corrente < $prenotazione->getDataInizio()) { //prenotazioni future
            
            $numPrenotazioniFuture++;
            $item = file_get_contents("./components/item_prenotazione_futura.html");
            $item = str_replace("<ID/>",$id_prenotazione,$item);
            $item = str_replace("<MAIL/>", $mail,$item);
            $item = str_replace("<TITOLO/>",$nomeAnnuncio,$item);
            $item = str_replace("<PATH/>",$path,$item);
            $item = str_replace("<INDIRIZZO/>",$luogoAlloggio,$item);
            $item = str_replace("<DI/>",$dataInizio,$item);
            $item = str_replace("<DF/>",$dataFine,$item);
            $item = str_replace("<PREZZO/>",$prezzo,$item);
            $item = str_replace("<NO/>",$numeroOspiti,$item);
            $item = str_replace("<DESC/>", "./get_desc_anteprima_annuncio.php?id_annuncio=".$annuncio->getIdAnnuncio(),$item);
            $prenotazioni_future .=$item;
        }
    }
    
   if($numPrenotazioniCorrenti>0 or $numPrenotazioniFuture>0 or $numPrenotazioniPassate>0){
       $pagina = str_replace("<FLAG/>","<PRENOTAZIONICORRENTI/><PRENOTAZIONIFUTURE/><PRENOTAZIONIPASSATE/>",$pagina);
       if ($numPrenotazioniPassate > 0) {
           $pagina = str_replace("<LINKPASS/>",
               "<a class=\"aiuti_alla_navigazione\" href=\"#prenotazioni_passate\">Vai alle tue prenotazioni passate</a>" , $pagina);
           $prenotazioni_passate .= "</ul>";
           $pagina = str_replace("<PRENOTAZIONIPASSATE/>", $prenotazioni_passate, $pagina);
        
       }else{
           $pagina = str_replace("<PRENOTAZIONIPASSATE/>", "", $pagina);
    
       }
       if ($numPrenotazioniCorrenti > 0) {
           $pagina = str_replace("<LINKCOR/>",
               "<a class=\"aiuti_alla_navigazione\" href=\"#prenotazioni_correnti\">Vai alle tue prenotazioni correnti</a>" , $pagina);
           $prenotazioni_correnti .= "</ul>";
           $pagina = str_replace("<PRENOTAZIONICORRENTI/>", $prenotazioni_correnti, $pagina);
    
       }else{
           $pagina = str_replace("<PRENOTAZIONICORRENTI/>","" , $pagina);
       }
       if ($numPrenotazioniFuture > 0) {
           $prenotazioni_future .= "</ul>";
           $pagina = str_replace("<LINKFUT/>",
               "<a class=\"aiuti_alla_navigazione\" href=\"#prenotazioni_future\">Vai alle tue prenotazioni future</a>",$pagina);
           $pagina = str_replace("<PRENOTAZIONIFUTURE/>", $prenotazioni_future, $pagina);
       }else{
    
           $pagina = str_replace("<PRENOTAZIONIPASSATE/>", "",$pagina);
    
       }
   }else{
       $pagina= str_replace("<FLAG/>","<h1>Non ci sono prenotazioni!</h1>",$pagina);
   }
    $pagina = str_replace("<LINKPASS/>","" , $pagina);
    $pagina = str_replace("<LINKCOR/>", "", $pagina);
    $pagina = str_replace("<LINKFUT/>", "", $pagina);
    echo $pagina;
} else {
    header("Location: login.php");
}
