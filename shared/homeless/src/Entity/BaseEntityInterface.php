<?php
// SPDX-License-Identifier: BSD-3-Clause

declare(strict_types=1);

namespace App\Entity;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
interface BaseEntityInterface
{
    public function getId();

    public function getSyncId(): ?int;

    public function setSyncId(?int $syncId): static;

    public function getSort(): ?int;

    public function setSort(?int $sort): static;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(?\DateTimeInterface $createdAt);

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt);

    public function getCreatedBy(): ?User;

    public function setCreatedBy(?User $createdBy): static;

    public function getUpdatedBy(): ?User;

    public function setUpdatedBy(?User $updatedBy): static;
}
