const { createApp, ref, reactive } = Vue

import form from "/assets/components/form.js"
import search from "/assets/components/search.js"
import card from "/assets/components/card.js"

createApp({
    components: {
        beers: card,
        searching: search,
        forms: form
    },
    setup() {
        let translations = ref({})
        let info = ref([])
        const showSearch = ref(false);

        function toggleSearchBar() {
            showSearch.value = !showSearch.value;
        }

        fetch(`/assets/json/${sessionStorage.getItem("lang")}.json`)
            .then(request => request.json())
            .then(data => {
                info.value = data.beers;
                translations.value = data;
            })

        console.log(info);

        function fillBeerInput(beerName) {
            const input = document.getElementById("select-beer");
            if(input) input.value = beerName;
        }

        return {
            showSearch,
            toggleSearchBar,
            info,
            translations,
            fillBeerInput
        }
    }
}).mount("#app")