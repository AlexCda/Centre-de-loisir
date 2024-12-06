// Sélection des éléments du DOM
const activities = document.querySelector(".activities");
const members = document.querySelector(".members");
const attendances = document.querySelector(".attendances");
const acti = document.querySelector(".acti");
const adhe = document.querySelector(".adhe");
const rating = document.querySelector(".rating");
const head = document.querySelector("head");

// Gestion des styles et sélections en fonction de la page active
if (activities) {
    const link = document.createElement('link');
    link.rel = 'stylesheet'; 
    link.href = "./public/css/vert.css"; 
    document.head.appendChild(link);
    acti.classList.add("selec");
} else if(members) {
    const link = document.createElement('link');
    link.rel = 'stylesheet'; 
    link.href = "./public/css/bleu.css";
    document.head.appendChild(link);
    adhe.classList.add("selec");
} else if(attendances) {
    const link = document.createElement('link');
    link.rel = 'stylesheet'; 
    link.href = "./public/css/rose.css"; 
    document.head.appendChild(link);
    rating.classList.add("selec");
} 

// Sélection des éléments du DOM
const session = document.querySelector(".session");
const rating2 = document.querySelector(".rating2");
const nbMax = document.querySelector(".nbMax");
// Redirection vers le formulaire de participation/notation avec le bon nombre de lignes

if (rating2) {
    let nbMaximum = parseInt(nbMax.textContent);
    let numSession;
    if(session) {
        numSession = session.textContent;
    }
    rating2.addEventListener("click", function() {
        let nbLignes;
        do{
            nbLignes = prompt("Combien de participant(s) voulez-vous ajouter pour cette séance? (Max :"+nbMaximum+")");
            nbLignes = parseInt(nbLignes);
        } while (isNaN(nbLignes) || nbLignes <= 0 || nbLignes > nbMaximum);
        
        window.location.href = `index.php?action=getFormRating&count=${nbLignes}&session=${numSession}`;
    })
}

// Sélection des éléments du DOM
const form = document.querySelector(".form");
const call = document.querySelector(".call");
const callForm = document.querySelector(".callForm");
const details =document.querySelector(".details");
const formCreaSession = document.querySelector(".formCreaSession");
const callCreateSession = document.querySelector(".callCreateSession");

// Permet d'afficher les formulaires de création ou de modification et de cacher la partie à remplacer
if (form && details && call) {
    call.addEventListener("click", function() {
        form.classList.remove("d-none");
        details.classList.add("d-none");
    })
} else if (call && form) {
    call.addEventListener("click", function() {
        form.classList.remove("d-none");
        call.classList.add("d-none");
    })
}
if (callCreateSession && formCreaSession) {
    callCreateSession.addEventListener("click", function() {
        formCreaSession.classList.remove("d-none");
        callCreateSession.classList.add("d-none");
    })
}

// script permettant de ne pas pouvoir sélectionner deux fois la même personne dans les participants d'une séance
document.addEventListener('DOMContentLoaded', function() {
    // on sélectionne tous les éléments <select>
    const selects = document.querySelectorAll('.member-select');
    // pour chacun on ajoute l'écoste lors du changement
    selects.forEach(select => {
        select.addEventListener('change', updateSelects);
    });

    function updateSelects() {
        // on récupère les valeurs sélectionnées
        const selectedValues = Array.from(selects).map(select => select.value);
        //  Pour chacune on la désactive dans la liste sauf si c'est celle sélectionnée dans cette liste
        selects.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.value !== '') {
                    option.disabled = selectedValues.includes(option.value) && option.value !== select.value;
                }
            });
        });
    }
});