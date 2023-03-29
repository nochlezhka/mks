<?php declare(strict_types=1);
// SPDX-License-Identifier: BSD-3-Clause

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Для настройки отображения пунктов меню в анкете клиента
 */
#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
#[UniqueEntity('code')]
class MenuItem extends BaseEntity
{
    public const CODE_SHELTER_HISTORY = 'shelter_history';
    public const CODE_CERTIFICATE = 'certificate';
    public const CODE_GENERATED_DOCUMENT = 'generated_document';
    public const CODE_NOTIFICATIONS = 'notifications';
    public const CODE_STATUS_HOMELESS = 'status_homeless';
    public const CODE_QUESTIONNAIRE_LIVING = 'questionnaire_living';

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
