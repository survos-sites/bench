<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Controller\AppController;
use App\DataFixtures\App;
use App\Repository\ImageRepository;
use App\Workflow\IImageWorkflow;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\SaisBundle\Service\SaisClientService;
use Survos\StateBundle\Traits\MarkingInterface;
use Survos\StateBundle\Traits\MarkingTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['image.read','marking']],
)]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Groups(['image.read'])]
class Image implements MarkingInterface, \Stringable
{
    use MarkingTrait;
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
