<?php

namespace App\Tests\Controller;

use App\Tests\Repository\RepositoryTestCase;
use Symfony\Component\DomCrawler\Crawler;

class PlaylistsControllerTest extends RepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::ensureKernelShutdown();
    }

    public function testPlaylistsSortByNameAsc(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/playlists/tri/name/ASC');

        $this->assertResponseIsSuccessful();
        $this->assertSame('PHP', $this->getFirstLineColumnText($crawler, 0));
    }

    public function testPlaylistsFilterByName(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/playlists/recherche/name', [
            'recherche' => 'Sym',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('tbody tr'));
        $this->assertSame('Symfony', $this->getFirstLineColumnText($crawler, 0));
    }
    
    public function testPlaylistsSortByNbFormationsAsc(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/playlists/tri/nbFormations/ASC');

        $this->assertResponseIsSuccessful();
        $this->assertSame('PHP', $this->getFirstLineColumnText($crawler, 0));
    }

    public function testPlaylistsLinkToDetailPage(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/playlists');

        $link = $crawler->filter('tbody tr')->first()->filter('a')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'PHP');
        $this->assertSelectorTextContains('body', 'Formation PHP');
    }

    private function getFirstLineColumnText(Crawler $crawler, int $column): string
    {
        return trim($crawler->filter('tbody tr')->first()->filter('td')->eq($column)->text());
    }
}
