<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Official;
use Survos\WorkflowExtrasBundle\Attribute\AsWorkflow;
use Survos\WorkflowMessengerBundle\Attribute\Transition;
use Survos\MediaBundle\Service\MediaRegistry;
use Survos\WikiBundle\Service\WikidataService;
use Symfony\Component\Workflow\Attribute\AsGuardListener;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;


// See events at https://symfony.com/doc/current/workflow.html#using-events

#[AsWorkflow(supports: [Official::class], name: self::WORKFLOW_NAME, places: OfficialPlace::class, description: 'Enrich officials with Wikidata and register their images')]
final class OfficialWorkflow
{
    public const WORKFLOW_NAME = 'OfficialWorkflow';

    #[Transition(OfficialPlace::New, OfficialPlace::Details, 'Fetch Wikidata and register P18 images as media', async: true, transport: 'official_fetch_wiki')]
    public const TRANSITION_FETCH_WIKI = 'fetch_wiki';


    public function __construct(
        private readonly WikidataService $wikiService,
        private readonly MediaRegistry   $mediaRegistry,
    ) {
    }

    private function getOfficial(Event $event): Official
    {
        /** @var Official */ return $event->getSubject();
    }

    #[AsGuardListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
    public function onGuard(GuardEvent $event): void
    {
        // @todo: move to guard: in workflow
        if (!$this->getOfficial($event)->getWikidataId()) {
            $event->setBlocked(true, "missing wiki id.");
        }
    }

    #[AsTransitionListener(self::WORKFLOW_NAME, self::TRANSITION_FETCH_WIKI)]
    public function onFetchWiki(TransitionEvent $event): void
    {
        $official = $this->getOfficial($event);
        // Pass ['P18'] so get() also fetches the image claim (it skips claims otherwise).
        $wikiData = $this->wikiService->get($official->getWikidataId(), 'en', ['P18']);
        $official->setWikiData($wikiData);

        // Register each Wikidata P18 image as a media entity. We already have the URL,
        // so there's nothing to download here — imgproxy reads dimensions/classification
        // later via the media workflow's async probe. (Replaces the old SAIS resize step.)
        // P18 (commonsMedia) values come back as full Special:FilePath URLs; older shapes
        // may be bare Commons filenames, so handle both.
        $images = $wikiData['claims']['P18'] ?? [];
        foreach ($images as $image) {
            if (!$image) {
                continue;
            }
            $url = str_starts_with($image, 'http')
                ? str_replace('http://', 'https://', $image)
                : 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($image);
            if (!$official->getOriginalImageUrl()) {
                $official->setOriginalImageUrl($url);
            }
            $this->mediaRegistry->ensureMedia($url, flush: true);
        }
    }

}
