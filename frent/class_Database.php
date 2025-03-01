<?php
require_once("class_Eccezione.php");

/**
 * Class Database
 * Classe che permette di in con un database MySQL.
 */
class Database {
    /**
     * Hostname per la connessione al database MySQL.
     * Tipo: string
     * Tipicamente è "localhost".
     */
    private $host_name;

    /**
     * Username per la connessione al database MySQL.
     * Tipo: string
     * Username per accedere a MySQL.
     */
    private $user_name;

    /**
     * Password per la connessione al database MySQL.
     * Tipo: string
     * Varia in base all'utente. E' in plain text.
     */
    private $password;

    /**
     * Database MySQL in cui sono presenti le tabelle da interrogare.
     * Tipo: string
     */
    private $db_name;

    /**
     * Flag per verificare se c'è una connessione aperta con il database.
     * Tipo: bool
     */
    private $is_connected;

    /**
     * Oggetto che si interfaccia con MySQL
     * Tipo: mysqli
     */
    private $db;

    /**
     * Costruisce un'istanza del gestore del database, memorizzando le credenziali, senza connettersi.
     * @param string $host_name string Hostname per la connessione al database MySQL
     * @param string $user_name string Username per la connessione al database MySQL
     * @param string $password string Password per la connessione al database MySQL
     * @param string $db_name string MySQL in cui sono presenti le tabelle da interrogare
     */
    public function __construct($host_name, $user_name, $password, $db_name) {
        $this->host_name = $host_name;
        $this->user_name = $user_name;
        $this->password = $password;
        $this->db_name = $db_name;
        $this->is_connected = FALSE;
    }

    /**
     * Effettua la connessione al database, se presenti delle credenziali corrette.
     * Lancia un'eccezione se si sono verificati errori nell'apertura della connessione
     * @throws Eccezione
     */
    public function connect() {
        if ($this->db == null) {
            $this->db = new mysqli($this->host_name, $this->user_name, $this->password, $this->db_name);
            if ($this->db->connect_errno > 0) {
                throw new Eccezione("C'è stato un errore nella connessione con il database. L'errore che si è verificato è il seguente: " . $this->db->connect_error);
            } else {
                $this->is_connected = TRUE;
            }
        }
    }

    /**
     * Effettua la disconnessione dal database a cui si è connessi.
     * @return bool TRUE se la connessione è stata chiusa, FALSE altrimenti.
     */
    public function disconnect(): bool {
        $this->is_connected = !($this->db->close());
        return !($this->is_connected);
    }

    /**
     * Imposta un set di caratteri da utilizzare durante l'invio delle richieste e la ricezione delle risposte con MySQL.
     * Ha valenza dal momento dell'invocazione del metodo (se c'è una connessione aperta). fino alla chiusura della connessione della connessione mediante il metodo disconnect.
     * @param string $charset set di caratteri da utilizzare nella connessione
     * @return bool TRUE se il set di caratteri è stato impostato, FALSE altrimenti
     */
    public function setCharset($charset): bool {
        if ($this->is_connected)
            return $this->db->set_charset($charset);
        else return FALSE;
    }
    
    /**
     * Wrapper per mysqli_real_escape_string($link, $escapestr).
     * @param string $string di cui ottenere l'escape
     * @return string $string di cui è stato fatto l'escape
     * @throws Eccezione
     */
    public function escapeString($string): string {
        if (!($this->is_connected)) {
            throw new Eccezione("Non è attiva una connessione con il database.");
        }
        return $this->db->real_escape_string($string);
    }

    /**
     * Esegue una query nel database a cui si è connessi richiamando una procedura MySQL.
     * @param string $procedure nome della procedura e relativi parametri, se presenti.
     * Per esempio: nome_procedura('p1', 2 ,'p3').
     * @return array di hash/array associativi.
     * @throws Eccezione se la query non è andata a buon fine o se non attiva una connessione con il database.
     */
    public function queryProcedure($procedure): array {
        if (!($this->is_connected)) {
            throw new Eccezione("Non è attiva una connessione con il database.");
        }
        // viene interrogato il database, essendo una procedure di MySQL viene usato l'operatore CALL
        $procedure_result = $this->db->query("CALL $procedure;", MYSQLI_STORE_RESULT);
        
        // se la query è andata a buon fine $procedure_result vale TRUE, altrimenti FALSE
        if ($procedure_result && $procedure_result->num_rows >= 0) {
            // NOTA BENE: verificare che con un record set di 0 righe $procedure_result sia comunque TRUE
            $returned_array = array();

            while ($row = $procedure_result->fetch_array(MYSQLI_ASSOC)) {
                array_push($returned_array, $row);
            }

            // va pulito tutto il result set rimasto inusato
            while($this->db->next_result()){
                if($a = $this->db->store_result()){
                    $a->free();
                }
            }

            $procedure_result->free();

            return $returned_array;
        } else {
            throw new Eccezione("Errore nell'esecuzione della procedura $procedure.");
        }
    }

    /**
     * Esegue una query nel database a cui si è connessi richiamando una funzione.
     * @param string $function string della funzione e relativi parametri, se presenti.
     * Per esempio: nome_funzione('p1', 2 ,'p3').
     * @return string risultato dell'interrogazione sotto forma di variabile e non di array.
     * @throws Eccezione se la query non è andata a buon fine o se non attiva una connessione con il database.
     */
    public function queryFunction($function): string {
        if (!($this->is_connected)) {
            throw new Eccezione("Non è attiva una connessione con il database.");
        }
        // viene interrogato il database, essendo una function di MySQL viene usato l'operatore SELECT, come le interrogazioni di selezione/proiezione
        $function_result = $this->db->query("SELECT $function;");
        
        // se la query è andata a buon fine $procedure_result vale TRUE, altrimenti FALSE
        if ($function_result && $function_result->num_rows == 1) {
            $returned_value = $function_result->fetch_array(MYSQLI_NUM); // array a una posizione, contenente una stringa
            $function_result->free();
            return $returned_value[0];
        } else {
            throw new Eccezione("Errore nell'esecuzione della funzione $function.");
        }
    }
}