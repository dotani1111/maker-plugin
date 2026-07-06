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

namespace Plugin\Maker44\Tests\Web\Admin;

use Eccube\Common\Constant;
use Eccube\Repository\ProductRepository;
use Faker\Generator;
use Plugin\Maker44\Tests\Web\MakerWebCommon;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class ProductMakerTest.
 */
class ProductMakerTest extends MakerWebCommon
{
    public const MAKER = 'Maker';
    public const MAKER_URL = 'maker_url';

    /**
     * @var int
     */
    protected $productId;

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

        $this->productRepository = self::getContainer()->get(ProductRepository::class);
    }

    /**
     * Test render
     */
    public function testProductNewRender(): void
    {
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_product_new'));
        $this->assertStringContainsString('メーカー', $crawler->filter('body .c-container')->html());
    }

    /**
     * Test new
     */
    public function testProductNewWithoutMaker(): void
    {
        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_product_new'));
        $this->assertStringContainsString('メーカー', $crawler->filter('body .c-container')->html());
    }

    /**
     * Test new
     */
    public function testProductNewWithMakerWithoutMakerSelect(): void
    {
        $Maker = $this->createMaker();

        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_product_new'));
        $this->assertStringContainsString($Maker->getName(), $crawler->filter('body .c-container')->html());
    }

    /**
     * Test new
     */
    public function testProductNewWithAddMakerURLWithoutMakerSelect(): void
    {
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = $faker->url;

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

        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check layout
        $this->assertStringContainsString($formData[self::MAKER_URL], $crawler->filter('body .c-container')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test new
     */
    public function testProductNewWithAddMakerInvalid(): void
    {
        $Maker = $this->createMaker();
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId() + 1;
        $formData[self::MAKER_URL] = $faker->url;

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $crawler = $client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // Check message
        $errorMessages = $crawler->filter('.form-error-message')->each(function (Crawler $node): string {
            return $node->text();
        });
        $versionParts = explode('-', Constant::VERSION);
        $expectedMessage = version_compare(reset($versionParts), '4.3.0', '<') ? '有効な値ではありません。' : '選択した値は無効です。';
        $this->assertStringContainsString($expectedMessage, implode("\n", $errorMessages));

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);
        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test new
     */
    public function testProductNewWithAddMakerWithoutMakerUrl(): void
    {
        $Maker = $this->createMaker();

        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = '';

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

        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker()->getId();
        $this->expected = $formData[self::MAKER];
        $this->verify();
    }

    /**
     * Test render
     */
    public function testProductNewWithAddMakerAndMakerUrlInValid(): void
    {
        $Maker = $this->createMaker();

        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = $faker->word;

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $crawler = $client->request(
            'POST',
            $this->generateUrl('admin_product_product_new'),
            ['admin_product' => $formData]
        );

        // Check message
        $errorMessages = $crawler->filter('.form-error-message')->each(function (Crawler $node): string {
            return $node->text();
        });
        $this->assertStringContainsString('有効なURLではありません。', implode("\n", $errorMessages));

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test new
     */
    public function testProductNewWithAddMakerAndMakerUrlSuccess(): void
    {
        $Maker = $this->createMaker();

        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = $faker->url;

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

        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = [$Product->getMaker()->getId(), $Product->getMakerUrl()];
        $this->expected = [$formData[self::MAKER], $formData[self::MAKER_URL]];
        $this->verify();
    }

    /**
     * Test render
     */
    public function testProductEditRender(): void
    {
        $Product = $this->createProduct();

        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]));
        $this->assertStringContainsString('メーカー', $crawler->filter('body .c-container')->html());
    }

    /**
     * Test render
     */
    public function testProductEditWithMaker(): void
    {
        $Product = $this->createProduct();
        $Maker = $this->createMaker();

        $crawler = $this->client->request('GET', $this->generateUrl('admin_product_product_edit', ['id' => $Product->getId()]));
        $this->assertStringContainsString($Maker->getName(), $crawler->filter('body .c-container')->html());
    }

    /**
     * Test new
     */
    public function testProductEditWithAddMakerURLWithoutMakerSelect(): void
    {
        $this->createMaker();

        // New product
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = '';

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

        // Edit product test
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = $faker->url;

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );

        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test Edit
     */
    public function testProductEditWithAddMakerInvalid(): void
    {
        $this->createMaker();

        // New product
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = '';

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

        // Edit product test
        $formData = $this->createFormData();
        $formData[self::MAKER] = 99999;
        $formData[self::MAKER_URL] = $faker->url;

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $crawler = $client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );

        // Check message
        $errorMessages = $crawler->filter('.form-error-message')->each(function (Crawler $node): string {
            return $node->text();
        });
        $versionParts = explode('-', Constant::VERSION);
        $expectedMessage = version_compare(reset($versionParts), '4.3.0', '<') ? '有効な値ではありません。' : '選択した値は無効です。';
        $this->assertStringContainsString($expectedMessage, implode("\n", $errorMessages));

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test Edit
     */
    public function testProductEditWithAddMakerWithoutMakerUrl(): void
    {
        $Maker = $this->createMaker();

        // New product
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = '';

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

        // Edit product test
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = '';

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );

        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        // 空の任意 URL は 4.4 コアのフォーム挙動で null として保存される
        $this->actual = [$Product->getMaker()->getId(), $Product->getMakerUrl()];
        $this->expected = [$Maker->getId(), null];
        $this->verify();
    }

    /**
     * Test render
     */
    public function testProductEditWithAddMakerAndMakerUrlInValid(): void
    {
        $Maker = $this->createMaker();

        // New product
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = '';

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

        // Edit product test
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = $faker->word; // invalid

        $crawler = $client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );

        // Check message
        $errorMessages = $crawler->filter('.form-error-message')->each(function (Crawler $node): string {
            return $node->text();
        });
        $this->assertStringContainsString('有効なURLではありません。', implode("\n", $errorMessages));

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = $Product->getMaker();
        $this->expected = null;
        $this->verify();
    }

    /**
     * Test Edit
     */
    public function testProductEditWithAddMakerAndMakerUrlSuccess(): void
    {
        $Maker = $this->createMaker();
        // New product
        /**
         * @var Generator
         */
        $faker = $this->getFaker();
        $formData = $this->createFormData();
        $formData[self::MAKER] = '';
        $formData[self::MAKER_URL] = '';

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

        // edit product test
        $formData = $this->createFormData();
        $formData[self::MAKER] = $Maker->getId();
        $formData[self::MAKER_URL] = $faker->url;

        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        $client->request(
            'POST',
            $this->generateUrl('admin_product_product_edit', ['id' => $productId]),
            ['admin_product' => $formData]
        );

        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        // Check message
        $this->assertStringContainsString('保存しました', $crawler->filter('.alert')->html());

        // Check database
        $Product = $this->productRepository->findOneBy([], ['id' => 'DESC']);

        $this->actual = [$Product->getMaker()->getId(), $Product->getMakerUrl()];
        $this->expected = [$Maker->getId(), $formData[self::MAKER_URL]];
        $this->verify();
    }
}
