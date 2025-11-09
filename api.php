<?php
header("Content-Type: text/plain; charset=utf-8"); // texte brut pour fetch
define("DATA_FILE", __DIR__ . "/data.json");

// Mot de passe pour réinitialisation
define("PASSWORD", "Guinness7");

$input = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? ($input['action'] ?? '');

switch ($action) {
    case "save":
        $data = $input['data'] ?? null;
        if (!$data || empty($data['prenom'])) {
            http_response_code(400);
            exit("Erreur : données invalides.");
        }

        $all = json_decode(file_get_contents(DATA_FILE), true);
        if (!is_array($all)) $all = [];

        $all[] = $data;
        if (file_put_contents(DATA_FILE, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            http_response_code(500);
            exit("Erreur : impossible d'enregistrer les données.");
        }

        echo "✅ Données enregistrées !";
        break;

    case "reset":
        $pass = $input['password'] ?? '';
        if ($pass !== PASSWORD) {
            http_response_code(403);
            exit("❌ Mot de passe incorrect.");
        }

        if (file_put_contents(DATA_FILE, json_encode([])) === false) {
            http_response_code(500);
            exit("Erreur : impossible de réinitialiser les données.");
        }
        echo "✅ Toutes les données ont été supprimées.";
        break;

    case "show":
        $all = json_decode(file_get_contents(DATA_FILE), true);
        if (!$all) {
            echo "Aucune donnée enregistrée.";
            exit;
        }

        $output = "";
        foreach ($all as $u) {
            $output .= "---------------------------------\n";
            $output .= "Prénom : " . ($u['prenom'] ?? '-') . "\n";
            $output .= "Génération : " . ($u['generation'] ?? '-') . "\n";
            $output .= "Méga-évolution : " . ($u['mega'] ?? '-') . "\n";
            $output .= "Starter Plante : " . ($u['starterPlante'] ?? '-') . "\n";
            $output .= "Starter Eau : " . ($u['starterEau'] ?? '-') . "\n";
            $output .= "Starter Feu : " . ($u['starterFeu'] ?? '-') . "\n";
            $output .= "Shiny : " . ($u['shiny'] ?? '-') . "\n";
            $pokemons = array_filter([
                $u['poke1'] ?? null, $u['poke2'] ?? null, $u['poke3'] ?? null,
                $u['poke4'] ?? null, $u['poke5'] ?? null, $u['poke6'] ?? null
            ]);
            $output .= "Pokémon : " . ($pokemons ? implode(", ", $pokemons) : '-') . "\n";
        }
        echo $output;
        break;

    default:
        http_response_code(400);
        echo "Action invalide.";
        break;
}