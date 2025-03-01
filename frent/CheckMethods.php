<?php

/**
 * Controlla se l'indirizzo e-mail passato come parametro è valido, ovvero è nella forma A@B.C, con A, B, C stringhe e 
 * che sia di lunghezza inferiore a $lunghezza
 * @param string $mail mail da controllare
 * @param int $lunghezza lunghezza massima che la mail può assumere
 * @return bool sse $mail è un indirizzo email valida e strlen(mail) <= lunghezza
 */
function checkIsValidMail($mail, $lunghezza=191): bool{
    return is_string($mail) and filter_var(trim($mail), FILTER_VALIDATE_EMAIL) and checkStringMaxLen($mail, $lunghezza);
}

/**
 * Controlla se in una stringa è contenuto il carattere SPAZIO
 * @param string $str la stringa da controllare
 * @return bool restituisce true sse la stringa non contiene nessun spazio
 */
function checkStringContainsNoSpace($str): bool {
    // return is_string($str) and !strpos(trim($str), " "); // funziona, eventualmente
    return is_string($str) and !preg_match("/\\s/",trim($str));
}

/**
 * Controlla se il valore passato è una stringa e se è di lunghezza <= a $len
 * @param string $str stringa da controllare
 * @param int $len indica la lunghezza massima che la stringa può avere.
 * @return bool se str è stringa e len è un intero e la stringa ha la lunghezza <= a int, altrimenti false;
 */
function checkStringMaxLen($str, $len): bool {
    return is_string($str) and is_int($len) and strlen($str) <= $len;
}

/**
 * Controlla se il valore passato è una stringa e se è di lunghezza >= a $len
 * @param string $str stringa da controllare
 * @param int $len indica la lunghezza massima che la stringa può avere.
 * @return bool se str è stringa e len è un intero e la stringa ha la lunghezza <= a int, altrimenti false;
 */
function checkStringMinLen($str, $len): bool{
    return is_string($str) and is_int($len) and strlen($str) >= $len;
}

/**
 * controlla se il valore $date contiene una data valida secondo il formato yyyy-mm-dd.
 * @param string $date la stringa che dovrebbe contenere la data.
 * @return bool restituisce true sse $date è una data valida rispetto al formato specificato.
 */
function checkIsValidDate($date): bool {
    $str = explode("-",$date);
    if (count($str)!=3)
        return false;
    return checkdate(intval($str[1]),intval($str[2]),intval($str[0]));
}

/**
 * La funzione controlla che in una stringa non siano presenti caratteri numerici.
 * @param string $str è la stringa da controllare
 * @return bool restituisce true sse la stringa non contiene nessun carattere numerico.
 */
function checkStringNoNumber($str): bool {
    return is_string($str) and !preg_match("/[0-9]+/", $str); // la regex restituisce true se trova numeri, false se non li trova: io voglio che non li trovi
}

/**
 * Verifica che $dataI sia una data precedente a $dataF (che avviene prima).
 * @param string $dataI prima string contenente una data da verificare che sia inferiore a $dataF
 * @param string $dataF seconda string contenente una data da verificare che sia superiore a $dataF
 * @return bool restituisce true sse la dataI è antecedente alla dataF
 */
function checkDateBeginAndEnd($dataI, $dataF): bool {
    return checkIsValidDate($dataI) and checkIsValidDate($dataF) and strtotime($dataI) <= strtotime($dataF);
}

/**
 * Verifica che la stringa $telefono contenga un numero di telefono valido.
 * @param string $telefono stringa contenente (possibilmente) un numero di telefono
 * @return bool true sse $telefono rappresenta un numero di telefono
 */
function checkPhoneNumber($telefono): bool {
    return is_string($telefono) and preg_match("/^([+][0-9]{1,3})?[0-9]{4,13}$/", $telefono);
}