<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\MarketFetcher;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$cacheSeconds = 120;
header("Cache-Control: public, max-age={$cacheSeconds}, must-revalidate");
header("Expires: " . gmdate('D, d M Y H:i:s', time() + $cacheSeconds) . ' GMT');

$fetcher = new MarketFetcher();

$type = $_GET['type'] ?? trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$allowed = ['gold', 'coin', 'currency', 'all'];

if (!in_array($type, $allowed, true)) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Not Found'], JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($type) {
    case 'gold':
        $data = $fetcher->gold();
        break;
    case 'coin':
        $data = $fetcher->coin();
        break;
    case 'currency':
        $data = $fetcher->currency();
        break;
    case 'all':
    default:
        $data = $fetcher->all();
        break;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
