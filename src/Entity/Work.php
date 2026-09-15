<?php

namespace App\Entity;

use App\Repository\InstrumentRepository;
use App\Repository\WorkRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\JeopardyRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(

        )],
//    shortName: 'jeopardy',
    normalizationContext: [
        'groups' => ['work.read'],
    ]
)]
#[Groups(['work.read'])]
class Work
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::GUID)]
        private(set) string $id,
//        {
//            set => is_string($value) ? Guid::fromString($value) : $value;
//        }, // probably uuid

        #[ORM\Column(type: Types::TEXT)]
        #[Map(transform: [self::class, 'cleanup'])]
        public /* private(set) */string $title  {
            set => self::cleanup($value);
        },

//        #[ORM\Column(length: 255)]
//        public ?string    $description = null,

        #[ORM\Column(length: 255, nullable: true)]
        public /* private(set) */ ?string $type,
//        {
//            set => self::cleanupCategory($value);
//        },

        #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]
        public private(set) array $tags,

    )
    {

    }

    private function cleanupCategory(string $s): string
    {
        $s = u($s)->lower()->title($s)->wordwrap(32)->split("\n")[0]->toString();
        return $s;
    }
    private function cleanup(string $s): string
    {
        // or markdown?
        $s = strip_tags($s);
        $s = trim($s, "'");
        return $s;
    }


}
