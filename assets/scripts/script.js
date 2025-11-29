const {createApp, ref, reactive} = Vue

import form from "/components/form.js"
import card from "./card.js"
import card from ""

createApp({
    components:{
        beers:card,
        forms:form
    },
    setup(){
        let info = ref([
        ])

        fetch(`/assets/json/${sessionStorage.getItem("lang")}.json`)
        .then(request => request.json())
        .then(data =>{
            info.value = data.beers;
        })

        console.log(info);

        return{
            info
        }
    }
}).mount("#app")

const body = document.body;
const langMenu = document.getElementById("langMenu");
const overlay = document.getElementById("overlay")
const langMenuBtn = document.getElementById("langMenuBtn")

function openLangMenu() {
    overlay.style.display = 'block';
    langMenu.style.display = 'flex';
    langMenu.style.visibility = 'visible';
    body.addEventListener("click", outsideClickLangMenu);
};

function closeLangMenu() {
    overlay.style.display = 'none';
    langMenu.style.display = 'none';
    langMenu.style.visibility = 'hidden';
    body.removeEventListener("click", outsideClickLangMenu);
};

function outsideClickLangMenu(e) {
    if (!langMenu.contains(e.target) && e.target !== langMenuBtn) closeLangMenu();
}