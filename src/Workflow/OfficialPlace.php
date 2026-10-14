<?php

declare(strict_types=1);

namespace App\Workflow;

use Survos\WorkflowExtrasBundle\Attribute\Place;

enum OfficialPlace: string
{
    #[Place('Loaded from JSON', initial: true)]
    case New = 'new';

    #[Place('Enhanced with Wikidata; P18 images registered as media')]
    case Details = 'details';
}
