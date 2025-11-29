import card from "./card.js"

let search = {
    props: ["beers"],
    template: `
    <div id="search">
        <input id="searchbar" type="text" placeholder="<?= $t['search_placeholder'] ?>">
        <button id="search-bt"><img id="mag-glass" src="/assets/img/magnifying_glass.png" alt="magnifying_glass"></button>
    </div>

    <beers beer="beer"></beers>
    `
}

export default search