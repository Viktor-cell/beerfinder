<?php
require_once "./openai.php";

$languages = [
    'de' => ["name" => "Deutsch"],
    'en' => ["name" => "English"],
    'sk' => ["name" => "Slovenčina"],
    'pl' => ["name" => "Polski"],
    'cz' => ["name" => "Česki"]
];
$lang = 'en';

if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages)) {
    $lang = $_GET['lang'];
}

$jsonContent = file_get_contents(__DIR__ . "/assets/json/" . $lang . ".json");
$t = json_decode($jsonContent, true);

$beers_to_display = [];
$pubs_to_display = [];
$view_state = 'beers';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['selected_card_name'])) {
    $view_state = 'pubs';
    $beerName = $_POST['selected_card_name'];
    $city = $_POST['city'] ?? 'Košice';

    $beer_query_json = json_encode(["name" => $beerName, "city" => $city]);

    try {
        $pubs_json = recomend_pub($beer_query_json, $lang);
        $pubs_to_display = json_decode($pubs_json, true);
    } catch (Exception $e) {
        $pubs_to_display = [];
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['select-beer2'])) {
    $user_input = $_POST['select-beer2'];

    try {
        $ai_json = recommend_beer($user_input, $lang);
        $beers_to_display = json_decode($ai_json, true);
    } catch (Exception $e) {
        $beers_to_display = [];
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/styles/beers.css">
    <link rel="stylesheet" href="/assets/styles/style.css">
    <link rel="stylesheet" href="/assets/styles/responsive.css">
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon-32x32.png">
    <title>Beer Finder</title>
</head>

<body>
    <header>
        <img src="/assets/img/logo.png" alt="logo">
        <div id="nav">
            <a href="/index.php?lang=<?= $lang ?>"><?= $t["header"]["finder"] ?></a>
            <a href="#footer"><?= $t['header']['contacts'] ?></a>
            <div id="langMenuBtn" onclick="openLangMenu()"><?= strtoupper($lang) ?></div>
        </div>

        <div id="langMenu">
            <form action="" method="GET">
                <a href="javascript:void(0)" onclick="closeLangMenu()">&times;</a>

                <label for="lang"><?= $t["header"]["langMenu"]["text"] ?></label>
                <select name="lang" id="lang">
                    <?php foreach ($languages as $alias => $language): ?>
                        <option value="<?= $alias ?>" <?= ($lang == $alias) ? 'selected' : '' ?>>
                            <?= $language["name"] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <input id="langMenuSubmit" type="submit" value="<?= $t["header"]["langMenu"]["button"] ?>">
                <p style="color: #ff9900; font-size: 0.9em; margin-bottom: 15px; font-weight: bold;">
                    <?= $t["header"]["langMenu"]["warning"] ?? "Warning: Changing the language will reset the current search." ?>
                </p>
            </form>
        </div>

        <div id="overlay"></div>
    </header>

    <main id="app">

        <?php if ($view_state === 'pubs'): ?>
            <div class="pubs-container">
                <h2>Pubs serving:
                    <?= htmlspecialchars($_POST['selected_card_name']) ?>
                </h2>

                <?php if (!empty($pubs_to_display)): ?>
                    <?php foreach ($pubs_to_display as $pub): ?>
                        <div class="pub">
                            <div class="left">
                                <div class="name">
                                    <?= htmlspecialchars($pub['name_of_pub'] ?? 'Unknown Pub') ?>
                                </div>
                                <div class="address"><?= htmlspecialchars($pub['address'] ?? '') ?></div>
                                <div class="city"><?= htmlspecialchars($pub['city'] ?? '') ?></div>
                            </div>
                            <?php if (!empty($pub['price_per_500ml'])): ?>
                                <div class="price"><?= $t['pub']['price']?>:
                                    <?= htmlspecialchars($pub['price_per_500ml']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($pub['link']) && $pub['link'] !== 'unknown'): ?>
                                <div class="map">
                                    <a href="<?= htmlspecialchars($pub['link']) ?>" target="_blank"><?= $t['pub']['map']?></a>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="none">No pubs found matching this beer.</p>
                <?php endif; ?>

                <div class="back">
                    <a href="/index.php?lang=<?= $lang ?>" class="map-button">Back to Search</a>
                </div>
            </div>

        <?php else: ?>

            <div id="select-beer">
                <form action="/index.php?lang=<?= $lang ?>" method="POST">

                    <label for="select-beer2"><?= $t["formBeer"]["selectBeer"] ?? "Enter beer type or name:" ?></label>
                    <br>

                    <div id="inputs">
                        <input type="text" id="select-beer2" name="select-beer2" placeholder="e.g. Lager, IPA, Dark..."
                            value="<?= htmlspecialchars($_POST['select-beer2'] ?? '') ?>">

                        <input id="sent" type="submit" value="Search">
                    </div>
                </form>
            </div>

            <div id="show">
                <div class="cards-container">
                    <?php if (!empty($beers_to_display)): ?>
                        <?php foreach ($beers_to_display as $beer): ?>

                            <div class="card" onclick="findPubsForBeer('<?= addslashes($beer['name']) ?>')">
                                <div class="card-description">
                                    <h2 class="name-beer"><?= htmlspecialchars($beer['name']) ?></h2>
                                    <p class="color-beer"><?= htmlspecialchars($beer['color'] ?? '') ?></p>
                                    <p class="type-beer"><?= htmlspecialchars($beer['type'] ?? '') ?></p>

                                    <p class="alcohol-beer"><?= htmlspecialchars(
                                        ($beer['alcohol'] ?? $beer['alcohol_amount'] ?? '')
                                        ? ($beer['alcohol'] ?? $beer['alcohol_amount']) . "%"
                                        : ''
                                    ) ?></p>

                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p id="no-beers">
                            <?php
                            if (isset($_POST['select-beer2'])) {
                                echo "No beers found for that type.";
                            } else {
                                echo "Enter your beer preference above to see recommendations!";
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <form id="hiddenCardForm" method="POST" action="/index.php?lang=<?= $lang ?>" style="display:none;">
                <input type="hidden" name="selected_card_name" id="hidden_beer_name">
                <input type="hidden" name="city" value="<?= htmlspecialchars($_POST['city'] ?? 'Košice') ?>">
            </form>

        <?php endif; ?>

    </main>

    <footer id="footer">
        <div class="footer-container">
            <div class="footer-about">
                <h3>BeerFinder</h3>
                <p><?= $t['footer']['footer-about'] ?></p>
            </div>
            <div class="footer-links">
                <h4><?= $t['footer']['footer-links']['links'] ?></h4>
                <ul>
                    <li><a href="#"><?= $t['footer']['footer-links']['home'] ?></a></li>
                    <li><a href="#"><?= $t['footer']['footer-links']['about'] ?></a></li>
                    <li><a href="#"><?= $t['footer']['footer-links']['finder'] ?></a></li>
                    <li><a href="#"><?= $t['footer']['footer-links']['contact'] ?></a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h4><?= $t['footer']['footer-social'] ?></h4>
                <div class="social-icons">
                    <a href="https://www.facebook.com/matej.kavcak.5?locale=sk_SK"><img
                            src="/assets/img/facebook-icon.png" alt="Facebook"></a>
                    <a href="https://www.instagram.com/beer.finder.sk//"><img src="/assets/img/instagram-icon.png"
                            alt="Instagram"></a>
                    <a href="https://x.com/beer_finder_sk"><img src="/assets/img/twitter-icon.png" alt="Twitter"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 BeerFinder. <?= $t['footer']['footer-bottom'] ?></p>
        </div>
        <footer>

            <script src="/assets/scripts/global.js"></script>
            <script>
                function findPubsForBeer(beerName) {
                    document.getElementById('hidden_beer_name').value = beerName;
                    document.getElementById('hiddenCardForm').submit();
                }
            </script>
</body>

</html>