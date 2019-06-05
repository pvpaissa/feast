<?php

namespace Cleanse\Feast\Classes\LightParty;

use Config;
use Log;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

class PartyCrawler
{
    public $parties = [];
    public $season;
    public $datacenter;
    public $day;

    public $url;

    /**
     * PartyCrawler constructor.
     * @param integer $season
     * @param string  $datacenter
     * @param string  $day
     */
    public function __construct($season, $datacenter, $day)
    {
        $this->season     = $season;
        $this->datacenter = $datacenter;
        $this->day        = $day;
    }

    public function crawl()
    {
        $dataCenterTeams = $this->guzzle();

        $crawler = new Crawler($dataCenterTeams);

        //If this tier has no teams
        $teamNodes = '//*[@id="ranking"]/div[3]/div/div[2]/article/div[2]/table/tbody/tr';
        if (!$crawler->filterXPath($teamNodes)->count()) {
            Log::info('The DOM crawler found no data for teams at ' . $this->url . ': ' . $this->datacenter . ' ' . $this->day);
            return;
        }

        $crawler->filterXPath($teamNodes)
            ->each(function (Crawler $node) {
                $team = [];

                $segments = explode('/', rtrim($node->attr('data-href'), '/'));
                $teamId = end($segments);

                $team['lodestone'] = $teamId;
                $team['name'] = $node->filterXPath('//td[4]/p')->text();
                $team['dc_group'] = $this->datacenter;
                $team['day'] = $this->day;
                $team['rating'] = $node->filterXPath('//td[6]/p')->text();
                $team['change'] = $node->filterXPath('//td[6]/p[2]')
                    ->count() ? $node->filterXPath('//td[6]/p[2]')->text() : '0';

                $crestWrapper = '//td[3]/div/img';
                $crest = [];
                foreach ($node->filterXPath($crestWrapper) as $cImage) {
                    $cImage = new Crawler($cImage);
                    $crest[] = $cImage->attr('src');
                }
                $team['crest'] = $crest;

                $rosterWrapper = '//td[5]/ul/li';
                $roster = [];
                foreach ($node->filterXPath($rosterWrapper) as $member) {
                    $member = new Crawler($member);
                    $roster[] = $member->filterXPath('//strong')->text();
                }
                $team['roster'] = $roster;

                $team['division'] = $node->filterXpath('//td[7]/img')
                    ->count() ? $node->filterXpath('//td[7]/img')->attr('data-tooltip') : '';

                $this->parties[] = $team;
            });

        return $this->parties;
    }

    private function guzzle()
    {
        $this->url = 'https://na.finalfantasyxiv.com/lodestone/ranking/thefeast/team';

        $urlVars = '?dcgroup='.$this->datacenter;

        $client = new GuzzleClient;

        $res = $client->get($this->url.$urlVars);

        return $res->getBody()->getContents();
    }
}
