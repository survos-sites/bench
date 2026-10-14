<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Official;
use App\Workflow\OfficialPlace;
use App\Workflow\OfficialWorkflow;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Workflow\Registry;

final class OfficialWorkflowTest extends WebTestCase
{
    public function testNativeWorkflowPreservesPlacesAndBlocksMissingWikidata(): void
    {
        self::bootKernel();
        $official = new Official('workflow-test');
        $workflow = self::getContainer()->get(Registry::class)->get($official, OfficialWorkflow::WORKFLOW_NAME);

        self::assertSame(OfficialPlace::New->value, $official->getMarking());
        self::assertSame(['new' => 'new', 'details' => 'details'], $workflow->getDefinition()->getPlaces());
        self::assertFalse($workflow->can($official, OfficialWorkflow::TRANSITION_FETCH_WIKI));
        $transition = $workflow->getDefinition()->getTransitions()[0];
        self::assertSame('official_fetch_wiki', $workflow->getMetadataStore()->getMetadata('transport', $transition));
        self::assertTrue($workflow->getMetadataStore()->getMetadata('async', $transition));
    }
    public function testWorkflowExplorerRenders(): void
    {
        $client = self::createClient();
        $client->request('GET', '/workflow/OfficialWorkflow');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Fetch Wikidata');
    }
}
