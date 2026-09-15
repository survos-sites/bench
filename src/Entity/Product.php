<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ComparisonFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\SortFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\FieldBundle\Attribute\Field;
use Survos\FieldBundle\Attribute\RouteIdentity;
use Survos\FieldBundle\Entity\RouteIdentityTrait;
use Survos\FieldBundle\Entity\RouteParametersInterface;
use Survos\FieldBundle\Enum\Widget;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Survos\BabelBundle\Entity\Traits\BabelHooksTrait;
use Doctrine\ORM\Mapping\Column;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['product.read', 'product.details'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['product.read'],
            ],
//            parameters: [
//                // ?category=foo
//                'brand' => new QueryParameter(
//                    filter: new ExactFilter() // instance, ignored if not registered
//                ),
//            ],
            parameters: [
                // it appears that this won't work without a custom filter
                // create a "tag" entity and then filter on it
                // https://chatgpt.com/share/69357edd-e82c-8010-b119-aa3723ba84da
//                'tags' => new QueryParameter(
//                    filter: new ExactFilter(),
//                    property: 'tags',
//                ),
                'range[:property]' => new QueryParameter(
                    filter: new ComparisonFilter(new ExactFilter()),
                    properties: self::RANGE_PROPS,
                    schema: ['type' => 'integer'],
                    castToNativeType: true,
                ),
                'search[:property]' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    properties: self::SEARCH_PROPS
                ),
                'filter[:property]' => new QueryParameter(
                    filter: new ExactFilter(),
                    properties: ['category','brand'], // self::FILTER_PROPS
                ),
                'order[:property]' => new QueryParameter(
                    filter: new SortFilter(),
                    properties: [...self::SORT_PROPS, 'exactPrice']
                ),
            ]
        )],
    normalizationContext: ['groups' => ['product.read', 'product.details','rp']],
)]

#[RouteIdentity(field: 'sku')]
class Product implements RouteParametersInterface
{
    use RouteIdentityTrait;

    private const RANGE_PROPS = ['rating', 'stock'];
    private const FILTER_PROPS = ['category','brand']; // single values only without custom filter
    private const FILTER_ARRAY_PROPS = ['tags'];
    private const SEARCH_PROPS = ['title', 'description'];
    private const SORT_PROPS = ['rating']; // 'exactPrice' for doctrine

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        #[ORM\Id]
        #[Groups(['product.read'])]
        public ?string $sku,


        #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
//        #[Groups(['product.details'])]
        private(set) array $data


    )
    {
        $this->images = new ArrayCollection();
        $this->stock = $this->data->stock??0;
        $this->rating = round($this->data->rating??0);
    }
    public string $id { get => $this->sku; }

    public function getId(): string
    {
        return $this->sku;
    }


    // virtual property
    #[Groups(['product.read'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty("category from extra, virtual but needs index")]
    #[Field(filterable: true, widget: Widget::Select, facet: true, order: 30)]
    public ?string $category;

    #[Groups(['product.read'])]
    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[ApiProperty("the registered brand name")]
    #[Field(filterable: true, widget: Widget::Select, facet: true, order: 40)]
    public ?string $brand;

    #[Groups(['product.read'])]
    #[ApiProperty("thumbnail from data (not sais)")]
    public ?string $thumbnail {
        get => $this->data['thumbnail']??null;
    }


    #[Groups(['product.read'])]
    #[ApiProperty("virtual price, int for range sliders")]
    #[Field(visible: false)]
    public ?int $price {
        get => (int) round($this->data['price'] ?? 0);
    }

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    #[ApiProperty("exact price, float, for doctrine filters")]
    public ?float $exactPrice;

    #[Groups(['product.read'])]
    #[ApiProperty("rounded rating, for range slider")]
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Range(
        min: 0,
        max: 5
    )]
    #[Field(filterable: true, widget: Widget::Range, facet: true, order: 60)]
    public int $rating;

    #[Groups(['product.read'])]
    #[ApiProperty("rounded rating, for range slider")]
    #[ORM\Column(type: Types::INTEGER)]
    #[Field(filterable: true, widget: Widget::Range, facet: true, order: 70)]
    public int $stock;

    #[Groups(['product.read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    #[ApiProperty("array of tags")]
    public array $tags;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product', orphanRemoval: true)]
    private(set) Collection $images {
        get {
            return $this->images;
        }
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

        #[Column(type: Types::TEXT, nullable: true)]
        #[Field(searchable: true, order: 10)]
        public ?string $title = null;

        #[Column(type: Types::TEXT, nullable: true)]
        #[Field(searchable: true, order: 20)]
        public ?string $description = null;

        public ?string $snippet { get => mb_substr($this->description, 0, 40); }

    /** Real count from the Image relation (not just presence of $thumbnail) -- facetable to filter out no-image products. */
    #[Groups(['product.read'])]
    #[ApiProperty("number of real Image entities linked to this product")]
    public int $imageCount {
        get => $this->images->count();
    }
}
