<?php
namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Helpers\ParserHelper;

class MarketFetcher
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function gold(): array
    {
        $content = $this->get('https://www.tgju.org/gold-chart');
        $crawler = new Crawler($content);
        $mainDiv = $crawler->filter('div.fs-cell.fs-sm-12.fs-xs-12.fs-md-12.fs-lg-12.fs-xl-6')->eq(2);
        $tables = $mainDiv->filter('table.data-table.market-table');

        $results = [];
        foreach ($tables as $tableNode) {
            $tableCrawler = new Crawler($tableNode);
            $rows = $tableCrawler->filter('tbody tr');

            foreach ($rows as $rowNode) {
                $rowCrawler = new Crawler($rowNode);
                $slug = $rowCrawler->attr('data-market-nameslug');

                $columns = [];
                foreach ($rowCrawler->filter('td') as $tdNode) {
                    $columns[] = trim($tdNode->textContent);
                }

                $results[] = [
                    'symbol' => $slug,
                    'name' => $rowCrawler->filter('th')->text(),
                    'price' => ParserHelper::processPrice($columns[0] ?? null),
                    'change' => ParserHelper::processChange($columns[1] ?? null),
                    'low' => ParserHelper::processPrice($columns[2] ?? null),
                    'high' => ParserHelper::processPrice($columns[3] ?? null),
                    'time' => $columns[4] ?? null,
                ];
            }
        }
        return $results;
    }

    public function coin(): array
    {
        $content = $this->get('https://www.tgju.org/coin');
        $crawler = new Crawler($content);
        $mainDiv = $crawler->filter('div.fs-cell.fs-sm-12.fs-xs-12.fs-md-12.fs-lg-12.fs-xl-6')->eq(0);
        $tables = $mainDiv->filter('table.data-table.market-table');

        $results = [];
        foreach ($tables as $tableNode) {
            $tableCrawler = new Crawler($tableNode);
            $rows = $tableCrawler->filter('tbody tr');

            foreach ($rows as $rowNode) {
                $rowCrawler = new Crawler($rowNode);
                $slug = $rowCrawler->attr('data-market-nameslug');

                $columns = [];
                foreach ($rowCrawler->filter('td') as $tdNode) {
                    $columns[] = trim($tdNode->textContent);
                }

                $results[] = [
                    'symbol' => $slug,
                    'name' => $rowCrawler->filter('th')->text(),
                    'price' => ParserHelper::processPrice($columns[0] ?? null),
                    'change' => ParserHelper::processChange($columns[1] ?? null),
                    'low' => ParserHelper::processPrice($columns[2] ?? null),
                    'high' => ParserHelper::processPrice($columns[3] ?? null),
                    'time' => $columns[4] ?? null,
                ];
            }
        }
        return $results;
    }

    public function currency(): array
    {
        $content = $this->get('https://www.tgju.org/currency');
        $crawler = new Crawler($content);
        $divs = [
            $crawler->filter('div.fs-cell.fs-sm-12.fs-xs-12.fs-md-12.fs-lg-12.fs-xl-6')->eq(0),
            $crawler->filter('div.fs-cell.fs-sm-12.fs-xs-12.fs-md-12.fs-lg-12.fs-xl-6')->eq(1)
        ];

        $results = [];
        foreach ($divs as $mainDiv) {
            $tables = $mainDiv->filter('table.data-table.market-table');

            foreach ($tables as $tableNode) {
                $tableCrawler = new Crawler($tableNode);
                $rows = $tableCrawler->filter('tbody tr');

                foreach ($rows as $rowNode) {
                    $rowCrawler = new Crawler($rowNode);
                    $slug = $rowCrawler->attr('data-market-nameslug');

                    $columns = [];
                    foreach ($rowCrawler->filter('td') as $tdNode) {
                        $columns[] = trim($tdNode->textContent);
                    }

                    $results[] = [
                        'symbol' => $slug,
                        'name' => $rowCrawler->filter('th')->text(),
                        'price' => ParserHelper::processPrice($columns[0] ?? null),
                        'change' => ParserHelper::processChange($columns[1] ?? null),
                        'low' => ParserHelper::processPrice($columns[2] ?? null),
                        'high' => ParserHelper::processPrice($columns[3] ?? null),
                        'time' => $columns[4] ?? null,
                    ];
                }
            }
        }
        return $results;
    }

    public function all(): array
    {
        return [
            'gold' => $this->gold(),
            'coin' => $this->coin(),
            'currency' => $this->currency()
        ];
    }

    private function get(string $url): string
    {
        return $this->client->get($url)->getBody()->getContents();
    }
}
