<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\AppController;
use App\DataFixtures\App;
use App\Repository\ImageRepository;
use App\Workflow\IImageWorkflow;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\MeiliBundle\Api\Filter\FacetsFieldSearchFilter;
use Survos\MeiliBundle\Metadata\Fields;
use Survos\MeiliBundle\Metadata\MeiliIndex;
use Survos\SaisBundle\Service\SaisClientService;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['image.read','marking']],
)]
#[ApiFilter(FacetsFieldSearchFilter::class,
    properties: ['marking', 'productSku'],
    arguments: [ "searchParameterName" => "facet_filter"]
)]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Groups(['image.read'])]
//#[MeiliIndex(
//    persisted: new Fields(groups: ['image.read']),
//)]
class Image implements \Stringable
{
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[Groups(['transition', 'minimum', 'marking', 'searchable'])]
    public ?string $marking = null;

    public function getMarking(): ?string
    {
        return $this->marking;
    }

    public function setMarking(?string $marking, array $context = []): self
    {
        $this->marking = $marking;

        return $this;
    }

    public function __construct(
    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(referencedColumnName: 'sku', nullable: false)]
    private ?Product $product = null,

    #[ORM\Column(type: Types::TEXT)]
    public ?string $originalUrl = null,

    #[ORM\Id]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $code = null,
    )
    {
        if (!$this->code) {
            $this->code = self::calculateCode($this->originalUrl);
        }
//        $this->marking = IImageWorkflow::PLACE_NEW;
    }
    public string $id { get => $this->code; }
    static public function calculateCode(string $url) {
        return hash('xxh3', $url);
    }

    public function getId(): string
    {
        return $this->code;
    }

    public string $productSku {
        get => $this->product->sku;
    }

    #[ORM\Column(nullable: true)]
    public ?array $resized = null;

    public bool $hasThumbnails {
        get => $this->resized && count($this->resized) > 0;
    }

    #[ORM\Column(nullable: true)]
    public ?int $originalSize = null;


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function addThumbData($filter, ?string $url=null): static
    {
        $this->resized[$filter] = $url;
        return $this;
    }

    public function __toString()
    {
        return $this->originalUrl;
    }
}
