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

namespace Plugin\Maker44\Tests\Web;

use Eccube\Entity\Product;
use Eccube\Repository\ProductRepository;
use Faker\Generator;
use Plugin\Maker44\Entity\Maker;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ProductDetailTest
 * Hook point test
 */
class ProductDetailTest extends MakerWebCommon
{
    /**
     * @var Maker
     */
    protected $Maker;

    /**
     * @var Product
     */
    protected $Product;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Set up function.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteAllRows(['plg_maker']);

        $this->Maker = $this->createMaker();
        $this->Product = $this->createProductMaker($this->Maker);
        $this->productRepository = self::getContainer()->get(ProductRepository::class);
    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenHasMakerButUnRegister(): void
    {
        $this->markTestSkipped('Skipped due to need include template on twig file manually');
    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenRegisterMakerWithoutMakerUrl(): void
    {
        $this->markTestSkipped('Skipped due to need include template on twig file manually');
    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenRegisterMakerAndMakerUrl(): void
    {
        $this->markTestSkipped('Skipped due to need include template on twig file manually');
    }

    /**
     * Create maker
     *
     * @param Maker   $Maker
     * @param Product $Product
     *
     * @return Product
     */
    protected function createProductMaker(Maker $Maker, ?Product $Product = null): Product
    {
        /**
         * @var Generator
         */
        $faker = $this->getFaker();

        if (!$Product) {
            // New product
            /**
             * @var Generator
             */
            $faker = $this->getFaker();
            $formData = $this->createFormData();
            $formData['Maker'] = $Maker->getId();
            $formData['maker_url'] = $faker->url;

            /**
             * @var KernelBrowser
             */
            $client = $this->client;
            $client->request(
                'POST',
                $this->generateUrl('admin_product_product_new'),
                ['admin_product' => $formData]
            );

            $this->assertTrue($client->getResponse()->isRedirection());

            $response = $client->getResponse();
            self::assertInstanceOf(RedirectResponse::class, $response);
            $arrTmp = explode('/', $response->getTargetUrl());
            $productId = $arrTmp[count($arrTmp) - 2];

            $client->followRedirect();

            $this->productRepository = self::getContainer()->get(ProductRepository::class);
            $Product = $this->productRepository->find($productId);
        }

        return $Product;
    }
}
