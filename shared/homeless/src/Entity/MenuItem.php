<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Для настройки отображения пунктов меню в анкете клиента
 */
#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
#[UniqueEntity("code")]
class MenuItem extends BaseEntity
{
    const CODE_SHELTER_HISTORY = 'shelter_history';
    const CODE_CERTIFICATE = 'certificate';
    const CODE_GENERATED_DOCUMENT = 'generated_document';
    const CODE_NOTIFICATIONS = 'notifications';
    const CODE_STATUS_HOMELESS = 'status_homeless';
    const CODE_QUESTIONNAIRE_LIVING = 'questionnaire_living';

    /**
     * Название
     */
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    /**
     * Код
     */
    #[ORM\Column(type: "string", unique: true, nullable: true)]
    private ?string $code = null;

    /**
     * Включено
     */
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $enabled = true;

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     *
     * @return MenuItem
     */
    public function setName(?string $name): MenuItem
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string|null $code
     *
     * @return MenuItem
     */
    public function setCode(?string $code): MenuItem
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return MenuItem
     */
    public function setEnabled(?bool $enabled): MenuItem
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }
}
