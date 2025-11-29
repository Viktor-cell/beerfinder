<?php
    $languages = [
        'de' => [
            "name" => "Deutsch"
        ],
        'en' => [
            "name" => "English"
        ],
        'sk' => [
            "name" => "Slovenčina"
        ],
        'pl' => [
            "name" => "Polski"
        ],
        'cz' => [
            "name" => "Česki"
        ]
    ];    
    $lang = 'en';

    if(isset($_GET['lang'])){
        if(in_array($_GET['lang'], haystack: array_keys($languages))){
            $lang = $_GET['lang'];
        }
    }

    $json = file_get_contents( __DIR__ . "/assets/json/".$lang.".json");
    $t = json_decode($json, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/styles/beers.css">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/responsive.css">
    <title>Beer Finder</title>
    <script>
        sessionStorage.setItem("lang", "<?= $lang ?>");
    </script>
</head>
<body >
    <header>
        <img src="/assets/img/logo.png" alt="logo">
        <div id="nav">
            <a href="#search"><?= $t["header"]["finder"]?></a>
            <a href="footer"><?= $t['header']['contacts']?></a>
            <div id="langMenuBtn" onclick="openLangMenu()"><?= strtoupper($lang)?></div>
            
        </div>
        <div id="langMenu">
                    <form action="" method="GET">
                        <a href="javascript:void(0)" onclick="closeLangMenu()">&times;</a>
                        <label for="lang"><?= $t["header"]["langMenu"]["text"]?></label>
                        <select name="lang" id="lang">
                            <?php foreach($languages as $alias => $language): ?>
                                <option value="<?= $alias?>"
                                <?php if($lang == $alias) echo 'selected="selected"'?>
                                ><?= $language["name"]?></option>
                            <?php endforeach?>
                        </select>
                        <input type="submit" value="<?= $t["header"]["langMenu"]["button"]?>">
                    </form>
        </div>
        <div id="overlay"></div>
    </header>
    <main id="app">
        <!--<div id="finder">
            <div id="search">
                <input id="searchbar" type="text" placeholder="<?= $t['search_placeholder'] ?>">
                <button id="search-bt"><img id="mag-glass" src="/assets/img/magnifying_glass.png" alt="magnifying_glass"></button>
            </div>
        </div>-->
        <div id="finder">
            <forms> </forms>
        </div>

    </main>
    <footer>

    </footer>
    <script defer src="/assets/scripts/script.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script type="module" src="/assets/scripts/beer.js"></script>
</body>   
</html>