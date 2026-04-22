<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class FormationsControllerTest extends WebTestCase
{
    public function testFormationsSortByTitleAsc(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/formations/tri/title/ASC');

        $this->assertResponseIsSuccessful();
        $this->assertSame('Formation Doctrine', $this->getFirstLineColumnText($crawler, 0));
    }
    
    private const TBODY_TR = 'tbody tr'; 
    
    public function testFormationsFilterByTitle(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/formations/recherche/title', [
            'recherche' => 'Symfony',
        ]);
        
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter(self::TBODY_TR));
        $this->assertSame('Formation Symfony', $this->getFirstLineColumnText($crawler, 0));
    }

    public function testFormationsLinkToDetailPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/formations');

        $link = $crawler->filter(self::TBODY_TR)->first()->filter('a')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Formation');
        $this->assertSelectorTextContains('body', 'Description');
    }

    private function getFirstLineColumnText(Crawler $crawler, int $column): string
    {
        return trim($crawler->filter(self::TBODY_TR)->first()->filter('td')->eq($column)->text());
    }
}
