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

use Eccube\Common\EccubeNav;

class MakerNav implements EccubeNav
{
    /**
     * {@inheritdoc}
     */
    public static function getNav(): array
    {
        return [
            'product' => [
                'children' => [
                    'maker4' => [
                        'id' => 'maker4',
                        'name' => 'maker.admin.title',
                        'url' => 'maker_admin_index',
                    ],
                ],
            ],
        ];
    }
}
