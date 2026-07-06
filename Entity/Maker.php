<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Maker44\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Plugin\Maker44\Repository\MakerRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Maker.
 */
#[ORM\Table(name: 'plg_maker')]
#[ORM\Entity(repositoryClass: MakerRepository::class)]
#[UniqueEntity('name')]
class Maker extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER)]
    private ?int $sort_no = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     */
    public function setName(?string $name): Maker
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get sort_no.
     */
    public function getSortNo(): ?int
    {
        return $this->sort_no;
    }

    /**
     * Set sort no.
     */
    public function setSortNo(?int $sortNo): Maker
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Set create_date.
     */
    public function setCreateDate(\DateTime $createDate): Maker
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set update_date.
     */
    public function setUpdateDate(\DateTime $updateDate): Maker
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }
}
