<?php
// funziona e valida.
$pagina = file_get_contents("./components/riepilogo_prenotazione.html");
require_once "load_Frent.php";
if (isset($_SESSION["user"])) {
    try {
        $pagina = str_replace("<HEADER/>", file_get_contents("./components/header_logged.html"), $pagina);
        $pagina = str_replace("<FOOTER/>", file_get_contents("./components/footer.html"), $pagina);
//    todo sistemare il modo per arrivare al riepilogo prenotazione
    
    
        if (isset($_SESSION["prenotazione"]) or isset($_GET["id"])) {
            $prenotazione = "";
            $id_prenotazione = 0;
            if (isset($_SESSION["prenotazione"])) {// ci arrivo facendo una prenotazione
//            echo "piglio la prenotazione";
                $prenotazione = $_SESSION["prenotazione"];
                $id_prenotazione = $frent->insertOccupazione($prenotazione->getIdAnnuncio(), $prenotazione->getNumOspiti(),
                    $prenotazione->getDataInizio(),
                    $prenotazione->getDataFine()
                );
                unset($_SESSION["prenotazione"]);
            
            } else {// ci arrivo dalla pagine le mie prenotazioni. 
                $id_prenotazione = intval($_GET["id"]);
                if ($frent->getOccupazione($id_prenotazione)->getIdUtente()!=$_SESSION["user"]->getIdUtente())
                    header("Location: ./404.php");
            }
//        echo "id prenotazione = $id_prenotazione";
            $prenotazioni = $frent->getOccupazione(intval($id_prenotazione));
            $annuncio = $frent->getAnnuncio($prenotazioni->getIdAnnuncio());
            $host=$frent->getUser($annuncio->getIdHost());
            $durata=abs(strtotime($prenotazioni->getDataFine())-strtotime($prenotazioni->getDataInizio()))/(3600*24);
            $totale = $durata* $annuncio->getPrezzoNotte()*$prenotazioni->getNumOspiti();
            $pagina = str_replace("<IDPRENOTAZIONE/>", $prenotazioni->getIdOccupazione(), $pagina);
            $pagina = str_replace("<DATAINIZIO/>", $prenotazioni->getDataInizio(), $pagina);
            $pagina = str_replace("<DATAFINE/>", $prenotazioni->getDataFine(), $pagina);
            $pagina = str_replace("", $prenotazioni->getIdOccupazione(), $pagina);
        
            $pagina = str_replace("<MAILPROPRIETARIO/>",$host->getMail(),$pagina);
            $pagina = str_replace("<NUMOSPITI/>",$prenotazioni->getNumOspiti(),$pagina);
            $pagina = str_replace("<NOMEANNUNCIO/>", $annuncio->getTitolo(), $pagina);
            $pagina = str_replace("<INDIRIZZO/>", $annuncio->getIndirizzo(), $pagina);
            $pagina = str_replace("<CITTA/>", $annuncio->getCitta(), $pagina);
            $pagina = str_replace("<PROPRIETARIO/>",$host->getUserName(), $pagina);
            $pagina = str_replace("<PREZZO/>",$totale, $pagina);
        
        
            echo $pagina;
        } else {
//            echo "header";
        header("Location: ./404.php");
        }
    }catch (Eccezione $ex){
        $_SESSION["msg"] = "Non hai il permesso di accedere a questa pagina!";
        header("Location: ./error_page.php");
    }
    
} else {
    header("Location: ./login.php");
}
