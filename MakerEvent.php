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

namespace Plugin\Maker44;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MakerEvent implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'Product/detail.twig' => ['onTemplateProductDetail', 10],
        ];
    }

    /**
     * Append JS to display maker
     */
    public function onTemplateProductDetail(TemplateEvent $templateEvent): void
    {
        $templateEvent->addSnippet('@Maker44/default/maker.twig');
    }
}
