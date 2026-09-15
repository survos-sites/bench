<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SortFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\QueryParameter;
use App\Repository\OfficialRepository;
use App\Workflow\OfficialWorkflowInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Survos\FieldBundle\Attribute\RouteIdentity;
use Survos\FieldBundle\Entity\RouteIdentityTrait;
use Survos\FieldBundle\Entity\RouteParametersInterface;
use Survos\StateBundle\Traits\MarkingInterface;
use Survos\StateBundle\Traits\MarkingTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfficialRepository::class)]
#[ApiResource(
    operations: [new Get(),
//        new Put(), new Delete(), new Patch(),
        new GetCollection(name: 'doctrine-officials'
//            provider: MeilliSearchStateProvider::class,
        )],
    shortName: 'congress',
    normalizationContext: [
        'groups' => ['official.read', 'project.related', 'marking', 'rp', 'preview', 'translation'],
    ],
    parameters: [
        'order[:property]' => new QueryParameter(filter: new SortFilter(), properties: [
            'id',
            'firstName',
            'lastName',
            'officialName',
            'gender',
            'imageCount',
            'house',
            'currentParty',
            'birthday',
        ]),
    ],
)]
#[Groups(['official.read'])]
#[UniqueEntity(['id'])]
#[RouteIdentity(field: 'id', key: 'id')]
class Official implements RouteParametersInterface, MarkingInterface
{
    use RouteIdentityTrait;
    use MarkingTrait;
    public const GENDERS = ['M', 'F']; // @todo: map to string?

    #[ORM\Id]
    #[ApiProperty("the wikiCode is unique, so we use it", identifier: true)]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTerms(Collection $terms): void
    {
        $this->terms = $terms;
    } // wikidata ID

    #[ORM\Column(length: 16, nullable: true)]
    #[SerializedName('name.first')]
    private ?string $firstName = null;

    #[ORM\Column(length: 32)]
    #[SerializedName('name.last')]
    private ?string $lastName = null;

    #[ORM\Column(length: 48)]
    #[SerializedName('name.official_full')]
    private ?string $officialName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthday = null;

    #[ORM\Column(length: 1, nullable: true)]
    #[Assert\Choice(choices: self::GENDERS)]
    private ?string $gender = null;

    #[ORM\OneToMany(targetEntity: Term::class, mappedBy: 'official', cascade: ['remove', 'persist'], orphanRemoval: true)]
    private Collection $terms;

    #[ORM\Column(length: 12, nullable: true)]
    #[Assert\Length(min: 0, max: 12)]
    #[Assert\Choice(choices: ['Democrat', 'Republican','Independent'])]
    private ?string $currentParty = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $house = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(nullable: true)]
    private ?int $district = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wikidataId = null;

    #[ORM\Column(type: Types::JSON,  nullable: true, options: ['jsonb' => true])]
    private ?array $wikiData = null;

    // now an object with code => [{filter: size, filter2: size}]
    #[ORM\Column(type: Types::JSON,  nullable: true, options: ['jsonb' => true])]
    #[Groups(['official.read'])]
    private ?array $imageCodes = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageCount = null;

    #[ORM\Column(nullable: true)]
    private ?array $ids = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalImageUrl = null;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->marking = OfficialWorkflowInterface::PLACE_NEW;
        $this->terms = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getOfficialName(): ?string
    {
        return $this->officialName;
    }

    public function setOfficialName(string $officialName): static
    {
        $this->officialName = $officialName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeImmutable
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeImmutable $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Term>
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function addTerm(Term $term): static
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->setOfficial($this);
        }

        return $this;
    }

    public function removeTerm(Term $term): static
    {
        if ($this->terms->removeElement($term)) {
            // set the owning side to null (unless already changed)
            if ($term->getOfficial() === $this) {
                $term->setOfficial(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getOfficialName();
    }

    public function getCurrentParty(): ?string
    {
        return $this->currentParty;
    }

    public function setCurrentParty(?string $currentParty): static
    {
        $this->currentParty = $currentParty;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function setHouse(?string $house): static
    {
        $this->house = $house;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getDistrict(): ?int
    {
        return $this->district;
    }

    public function setDistrict(?int $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getWikidataId(): ?string
    {
        return $this->wikidataId;
    }

    public function setWikidataId(?string $wikidataId): static
    {
        $this->wikidataId = $wikidataId;

        return $this;
    }

    public function     getWikiData(): ?array
    {
        return $this->wikiData;
    }

    public function setWikiData(?array $wikiData): static
    {
        $this->wikiData = $wikiData;

        return $this;
    }

    public function getImageCodes(): array
    {
        return $this->imageCodes??[];
    }

    public function setImageCodes(?array $imageCodes): static
    {
        $this->imageCodes = $imageCodes;
        $this->setImageCount(count($imageCodes));

        return $this;
    }

    public function getImageCount(): ?int
    {
        return $this->imageCount;
    }


    public function setImageCount(?int $imageCount): static
    {
        $this->imageCount = $imageCount;

        return $this;
    }

    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function setIds(?array $ids): static
    {
        $this->ids = $ids;

        return $this;
    }

    public function getOriginalImageUrl(): ?string
    {
        return $this->originalImageUrl;
    }

    public function setOriginalImageUrl(?string $originalImageUrl): static
    {
        $this->originalImageUrl = $originalImageUrl;

        return $this;
    }
}
