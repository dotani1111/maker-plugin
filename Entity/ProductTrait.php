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
use Eccube\Attribute\EntityExtension;
use Eccube\Entity\Product;

#[EntityExtension(Product::class)]
trait ProductTrait
{
    #[ORM\ManyToOne(targetEntity: Maker::class)]
    #[ORM\JoinColumn(name: 'maker_id', referencedColumnName: 'id')]
    private ?Maker $Maker = null;

    #[ORM\Column(name: 'maker_url', type: Types::STRING, length: 1024, nullable: true)]
    private ?string $maker_url = null;

    public function getMaker(): ?Maker
    {
        return $this->Maker;
    }

    public function setMaker(?Maker $Maker = null): self
    {
        $this->Maker = $Maker;

        return $this;
    }

    public function getMakerUrl(): ?string
    {
        return $this->maker_url;
    }

    public function setMakerUrl(?string $maker_url): self
    {
        $this->maker_url = $maker_url;

        return $this;
    }
}
