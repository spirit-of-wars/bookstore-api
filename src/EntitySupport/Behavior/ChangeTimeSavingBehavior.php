<?php

namespace App\EntitySupport\Behavior;

/**
 * Trait ChangeTimeSaving
 * @package App\Entity\Behavior
 */
trait ChangeTimeSavingBehavior
{
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * #App\Field(inForm="hidden")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * #App\Field(inForm="hidden")
     */
    private $updatedAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}
