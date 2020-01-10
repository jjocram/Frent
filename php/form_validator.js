// i metodi che controllano un dato cominciano il check_nomecampo
// i metodi che controlano un form, ovvero tanti campi, hanno il nome validazione_ecc.

function mostra_errore(input, testoErrore) {
    togli_errore(input);
    const p = input.parentElement;
    console.log(p);
    const strong = document.createElement("strong");
    strong.appendChild(document.createTextNode(testoErrore));
    p.appendChild(strong);
}

//da richiamare sempre prima di mostraErrore
function togli_errore(input) {
    const p = input.parentNode;
    if (p.children.length > 2) {
        p.removeChild(p.children[2]);
    }
}

function check_nome(input) {

}

function check_immagini() {

}

function check_citta(n) {
    const reg = new RegExp('^[a-zA-Z]{3,}$');
    if (reg.test(n.value)) {
        togli_errore(n);
        return true;
    }
    mostra_errore(n, "Il nome della citt&agrave; non va bene.")
}

function check_data(data) {
    const reg = new RegExp('');

    if (reg.test(data.value)) {
        togli_errore(data);
        return true;
    }
    mostra_errore(data, "data non valida.");
    return false;

}

function check_dateInizioEFine(dataInizio, dataFine) {

}

function check_numOspiti(num) {
    const v = num.value;

    if (isNaN(v) && v <= 99) {
        togli_errore(num);
        return true;
    }

    mostra_errore(num, "valore inserito non valido.");
    return false;

}

function check_numeroTelefonico(input) {

    const reg = new RegExp('^(\\((00|\\+)39\\)|(00|\\+)39)?(38[890]|34[7-90]|36[680]|33[3-90]|32[89])\\d{7}$');

    if (reg.test(input.value)) {
        togli_errore(input);
        return true;
    }

    mostra_errore(input, "Numero non valido!");
    return false;

}

function check_userMail(mailInput) {
    const reg = new RegExp(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
    // const reg = new RegExp('^\\w+([._-]?\\w+)*@\\w+([.-]?\\w+)*(.\\w{2,6})+$');
    if (reg.test(mailInput.value.trim())) {

        togli_errore(mailInput);
        return true;
    }
    mostra_errore(mailInput, "La mail inserita non è valida!");
    return false;

}

function check_password(pwdInput) {
    var value = pwdInput.value;
    value = value.trim();
    if (value.length > 0) {
        togli_errore(pwdInput);
        return true;
    }
    mostra_errore(pwdInput, "Password non valido!");
    return false;
}

function check_data(gg, mm, aa) {
    const giorno = parseInt(gg.value);
    const mese = parseInt(mm.value);
    const anno = parseInt(aa.value);


}

function check_descrizione_foto() {

}

function check_descrizione(input) {
    const val = input.value.trim();
    if (val.length > 512) {
        togli_errore(input);
        return true;
    }
    mostra_errore(input, "Descrizione troppo lunga");
    return false;
}

function check_titoloAnnuncio(input) {

}

function check_prezzo_notte(input) {
    const val = input.value;

    if (!isNaN(val) && parseInt(val) > 0) {
        togli_errore(input);
        return true;
    }
    mostra_errore(input, "Il prezzo inserito non valido!");
    return false;
}

function check_via(input) {

}

function check_civico(input) {

}

function validazione_form_ricerca() {
    const citta = document.getElementById("cities");
    const dataInizio = document.getElementById("dataInizio");
    const dataFine = document.getElementById("dataFine");
    const numOspiti = document.getElementById("numOspiti");

    const resCitta = check_citta(citta);
    const resDataInizio = check_data(dataInizio);
    const resDataFine = check_data(dataFine);
    const resDataIF = check_dateInizioEFine(dataInizio, dataFine);
    const resNumOspiti = check_numOspiti(numOspiti);
    return resCitta && resDataInizio && resDataFine && resDataIF && resNumOspiti;
}

function validazione_form_registrazione() {
    const inputNome = document.getElementById("nome");
    const inputCognome = document.getElementById("cognome");
    const inputMail = document.getElementById("mail");
    const inputUserName = document.getElementById("username");
    const inputPwd = document.getElementById("password");
    const inputPwdR = document.getElementById("ripeti_password");
    const inputNumTelefono = document.getElementById("telefono");

    //controlla la data di nascita
    const inputGiorno = document.getElementById("giorno_nascita");
    const inputMese = document.getElementById("mese_nascita");
    const inputAnno = document.getElementById("anno_nascita");

    if (inputPwd.value.toString() === inputPwdR.toString()) {

        const res_nome = check_nome(inputNome);
        const res_cognome = check_nome(inputCognome);
        const res_mail = check_nome(inputMail);
        const res_username = check_nome(inputUserName);
        const res_pwd = check_nome(inputPwd);
        const res_pwdR = check_nome(inputPwdR);
        const res_telefono = check_numeroTelefonico(inputNumTelefono);
        const res_data = check_data(inputGiorno.value, inputMese.value, inputAnno.value);

        return res_telefono && res_nome && res_cognome && res_mail && res_pwd && res_pwdR && res_username && res_data;

    } else {
        mostra_errore(inputPwdR, "Password inseriti non sono identici");
        return false;
    }
}

//usato sia per login utente che login amministratore
function validazione_form_login() {
    const userMail = document.getElementById("user");
    const userPass = document.getElementById("password");
    const res_mail = check_userMail(userMail);
    const res_pass = check_password(userPass);
    return res_mail && res_pass;
}

function validazione_form_aggiungi_annuncio() {
    const input_titolo = document.getElementById("titolo");

    const res_titolo = check_titoloAnnuncio(input_titolo);

    return res_titolo && validazione_form_modifica_annuncio();

}

function validazione_form_ricerca_dettaglio_annuncio() {

}

function validazione_form_modifica_annuncio() {
    const input_descrizione = document.getElementById("descrizione");
    const input_max_ospiti = document.getElementById("max_ospiti");
    const input_img_anteprima = document.getElementById("img_anteprima");
    const input_desc_anteprima = document.getElementById("desc_anteprima");
    const input_prezzoNotte = document.getElementById("prezzo_notte");
    const input_via = document.getElementById("via");
    const input_citta = document.getElementById("citta");

    const res_descrizione = check_descrizione(input_descrizione);
    const res_maxOspiti = check_numOspiti(input_max_ospiti);
    const res_img_anteprima = check_immagini(input_img_anteprima);
    const res_desc_anteprima = check_descrizione_foto(input_desc_anteprima);
    const res_prezzoNotte = check_prezzo_notte(input_prezzoNotte);
    const res_via = check_via(input_via);
    const res_citta = check_citta(input_citta);
    return res_desc_anteprima && res_descrizione && res_img_anteprima && res_maxOspiti && res_prezzoNotte &&
        res_via && res_citta;
}

function validazione_form_cambio_password() {

}

function validazione_form_modifica_profilo() {

    const inputNome = document.getElementById("nome");
    const inputCognome = document.getElementById("cognome");
    const inputMail = document.getElementById("mail");
    const inputUserName = document.getElementById("username");
    const inputNumTelefono = document.getElementById("telefono");

    //controlla la data di nascita
    const inputGiorno = document.getElementById("giorno_nascita");
    const inputMese = document.getElementById("mese_nascita");
    const inputAnno = document.getElementById("anno_nascita");

    const res_cognome = check_nome(inputCognome);
    const res_nome = check_nome(inputNome);
    const res_mail = check_nome(inputMail);
    const res_username = check_nome(inputUserName);
    const res_telefono = check_numeroTelefonico(inputNumTelefono);
    const res_data = check_data(inputGiorno.value, inputMese.value, inputAnno.value);

    return res_telefono && res_nome && res_cognome && res_mail && res_username && res_data;


}

function validazione_form_prenota_annuncio() {
    const dataInizio = document.getElementById("dataInizio");
    const dataFine = document.getElementById("dataFine");
    const numOspiti = document.getElementById("numOspiti");

    const res_dataInizio = check_data(dataInizio);
    const res_dataFine = check_data(dataFine);
    const res = check_dateInizioEFine(dataInizio, dataFine);
    const res_numOspiti = check_numOspiti(numOspiti);
    return res_dataInizio && res_dataFine && res && res_numOspiti;
}
