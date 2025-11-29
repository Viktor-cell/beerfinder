<?php

require_once "./utils.php";

$OPENAI_KEY = 'replace_me';

function ask_ai($text)
{
    global $OPENAI_KEY;
    $data = [
        "model" => "gpt-4.1-mini",
        "messages" => [
            ["role" => "user", "content" => $text]
        ]
    ];


    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer $OPENAI_KEY"
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($response, true);

    $response = $response["choices"][0]["message"]["content"];

    return $response;
}

function html_to_beer_json($url) {
    $html = html_from_url($url);

    $prompt = <<<EOT
You are given HTML content from a pub's drink list. 
Extract and return ONLY a single JSON object with the following structure:

{
    "nameOfPub": "",
    "address": "",
    "beers": [
        {
            "name": "",
            "price_per_500ml": ""
        }
    ]
}

Rules:
1. Always return EXACTLY ONE JSON object—not an array.
2. Do not add any text before or after the JSON.
3. If pub name or address is missing, leave them as empty strings.
4. Extract all beers with their names and prices.
5. If price is not in 500ml, convert when possible; if not possible, leave empty.
6. Output must be valid JSON.

Do not include backticks in the response. 
Do not format the output as a code block. 
Return only the raw JSON string.
HTML content: $html
EOT;

    return ask_ai($prompt);
}



$URLS = [
    "https://www.geronimogrill.sk/napojovy-listok",
    "https://riderspub.sk/?utm_source=chatgpt.com",
    "https://www.pivovarhostinec.sk/nase-piva/",
    "https://www.centralpubkosice.sk/-napojovy-listok",
    "https://www.krcma-letna.sk/napojovy-listok/",
    "https://www.pilsnerurquellpub.sk/kosice/napojovy-listok/capovane-pivo#region-menu",
    "https://www.goldenroyal.sk/napojovy-listok/",
    "https://vicolo.sk/napojovy-listok/",
    "https://www.pivarenbokovka.sk/napojovy-listok/",
    "https://bancodelperu.sk/napojovy-listok/",
    "https://restauraciabojnice.sk/napojovy-listok/",
    "https://yuza.sk/napojovy-listok/",
    "https://restauraciabenvenuti.sk/napojovy-listok/",
    "https://www.mmpub.sk/restauracia/napojovy-listok",
    "https://www.restauraciasramek.sk/napojovy-listok/",
    "https://www.cactus.sk/restauracia-grill/napojovy-listok",
    "https://www.paparazzirestaurant.sk/napojovy-listok/",
    "https://bereknz.sk/napojovy-listok/",
    "https://restaurant.brixhotel.sk/napojovy-listok/",
    "https://www.daniels.sk/napojovy-listok-daniels-pub-restaurant/",
    "https://www.galaxyrestauracia.sk/napojovy-listok/",
    "https://savagebistro.sk/napojovy-listok/"
];


function create_json_of_bears($urls) {
    $results = [];

    foreach ($urls as $index => $url) {
        $json = html_to_beer_json($url);

        echo $index + 1 . " of " . count($urls) . "\n";

        $decoded = json_decode($json, true);

        if ($decoded !== null) {
            $results[] = $decoded;
        }
    }

    echo "==================================================================";
    $result_json = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo $result_json;
    file_put_contents("beers.json", $result_json);
}

create_json_of_bears($urls);