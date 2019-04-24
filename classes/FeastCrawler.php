<?php

namespace Cleanse\Feast\Classes;

use Config;
use Log;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

class FeastCrawler
{
    public $players = [];
    public $season;
    public $type;
    public $datacenter;
    public $tier;
    public $day;

    /**
     * FeastCrawler constructor.
     * @param integer $season
     * @param string  $type
     * @param string  $datacenter
     * @param integer $tier
     * @param string  $day
     */
    public function __construct($season, $type, $datacenter, $tier, $day)
    {
        $this->season     = $season;
        $this->type       = $type;
        $this->datacenter = $datacenter;
        $this->tier       = $tier;
        $this->day        = $day;
    }

    public function crawl()
    {
        $dataCenterPlayers = $this->guzzle();

        $crawler = new Crawler($dataCenterPlayers);

        //If this tier has no players
        if (!$crawler->filterXPath('//*[@id="ranking"]/div[3]/div/div[2]/article/table/tbody/tr')->count()) {
            Log::info('The crawler found no data in: ' . $this->datacenter . ' ' . $this->day);
            return;
        }

        $crawler->filterXPath('//*[@id="ranking"]/div[3]/div/div[2]/article/table/tbody/tr')
            ->each(function (Crawler $node) {
                $player = [];

                $segments = explode('/', rtrim($node->attr('data-href'), '/'));
                $characterId = end($segments);

                $player['character'] = $characterId;
                $player['name'] = $node->filterXPath('//td[4]/div/h3')->text();
                $player['data_center'] = $this->datacenter;
                $player['server'] = $node->filterXPath('//td[4]/span')->text();
                $player['wins'] = 0;
                $player['matches'] = 0;
                $player['rating'] = $node->filterXPath('//td[5]/p[1]')->text();
                $player['change'] = $node->filterXPath('//td[5]/p[2]')->count() ? $node->filterXPath('//td[5]/p[2]')->text() : '0';
                $player['percent'] = 0;
                $player['avatar'] = $node->filterXPath('//td[3]/div/img')->attr('src');
                $player['season'] = $this->season;
                $player['day'] = $this->day;

                $this->players[] = $player;
            });

        return $this->players;
    }

    private function guzzle()
    {
        if ($this->season === Config::get('cleanse.feast::season', 1)) {
            $link = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/';
        } else {
            $link = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/result/'.$this->season.'/';
        }

        $urlVars = '?solo_party='.$this->type;
        $urlVars .= '&dcgroup='.$this->datacenter;

        if ($this->tier <= 4) {
            $urlVars .= '&rank_type='.$this->tier;
        }

        $client = new GuzzleClient;

        $res = $client->get($link.$urlVars);

        return $res->getBody()->getContents();
    }
}
