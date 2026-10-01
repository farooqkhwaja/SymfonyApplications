<?php

namespace App\Tests\Twig\Components;

use App\Factory\CommentFactory;
use App\Factory\ConferenceFactory;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CommentListTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testPageLinksAreLiveActions(): void
    {
        $client = static::createClient();
        $conference = ConferenceFactory::createOne(['city' => 'Berlin', 'year' => '2021', 'slug' => 'berlin-2021']);
        CommentFactory::createMany(5, ['conference' => $conference]);

        $crawler = $client->request('GET', '/en/conference/berlin-2021');

        // the page link keeps a real href (works without JS) and calls goToPage when JS runs
        $link = $crawler->filter('[data-controller="live"] nav a[href*="page=2"]');
        $this->assertCount(2, $link, 'the "2" and "Next" links');
        $this->assertSame('live#action:prevent', $link->attr('data-action'));
        $this->assertSame('goToPage', $link->attr('data-live-action-param'));
        $this->assertSame('2', $link->attr('data-live-page-param'));
    }

    public function testGoToPageRendersTheNextComments(): void
    {
        $client = static::createClient();
        $conference = ConferenceFactory::createOne(['city' => 'Berlin', 'year' => '2021', 'slug' => 'berlin-2021']);
        CommentFactory::createMany(5, ['conference' => $conference]);

        $component = $this->createLiveComponent('CommentList', ['conference' => $conference], $client);
        $this->assertCount(CommentRepository::COMMENTS_PER_PAGE, $component->render()->crawler()->filter('h4'));

        // what a click on "3" does: POST to /_components/CommentList/goToPage
        $component->call('goToPage', ['page' => 3]);

        $this->assertSame(3, $component->component()->page);
        $html = $component->render()->crawler();
        $this->assertCount(1, $html->filter('h4'));
        $this->assertStringContainsString('There are 5 comments', $html->text());
    }

    public function testOnlyPublishedCommentsAreListed(): void
    {
        $client = static::createClient();
        $conference = ConferenceFactory::createOne(['city' => 'Berlin', 'year' => '2021', 'slug' => 'berlin-2021']);
        CommentFactory::createOne(['conference' => $conference, 'author' => 'Visible']);
        CommentFactory::createOne(['conference' => $conference, 'author' => 'Hidden', 'state' => 'submitted']);

        $html = $this->createLiveComponent('CommentList', ['conference' => $conference], $client)->render()->crawler();

        $this->assertSame(['Visible'], $html->filter('h4')->each(fn ($h4) => trim($h4->text())));
    }
}
