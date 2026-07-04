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

namespace Plugin\Maker44\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Plugin\Maker44\Entity\Maker;
use Plugin\Maker44\Form\Type\MakerType;
use Plugin\Maker44\Repository\MakerRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class MakerController.
 */
class MakerController extends AbstractController
{
    public function __construct(
        protected MakerRepository $makerRepository,
    ) {
    }

    /**
     * List, add, edit maker.
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route('/%eccube_admin_route%/maker', name: 'maker_admin_index')]
    #[Template('@Maker44/admin/maker.twig')]
    public function index(Request $request): array|RedirectResponse
    {
        $Maker = new Maker();
        $Makers = $this->makerRepository->findBy([], ['sort_no' => 'DESC']);

        /**
         * 新規登録フォーム
         */
        $builder = $this->formFactory->createBuilder(MakerType::class, $Maker);

        $form = $builder->getForm();

        /**
         * 編集用フォーム
         */
        $forms = [];
        foreach ($Makers as $item) {
            $id = $item->getId();
            $forms[$id] = $this->formFactory->createNamed('maker_'.$id, MakerType::class, $item);
        }

        if ('POST' === $request->getMethod()) {
            /*
             * 登録処理
             */
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->makerRepository->save($form->getData());

                $this->addSuccess('maker.admin.save.complete', 'admin');

                return $this->redirectToRoute('maker_admin_index');
            }

            /*
             * 編集処理
             */
            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->makerRepository->save($editForm->getData());

                    $this->addSuccess('maker.admin.save.complete', 'admin');

                    return $this->redirectToRoute('maker_admin_index');
                }
            }
        }

        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'Makers' => $Makers,
            'Maker' => $Maker,
            'forms' => $formViews,
        ];
    }

    /**
     * Delete Maker.
     */
    #[Route('/%eccube_admin_route%/maker/{id}/delete', name: 'maker_admin_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(#[MapEntity(id: 'id')] Maker $Maker): RedirectResponse
    {
        $this->isTokenValid();

        try {
            $this->makerRepository->delete($Maker);

            $this->addSuccess('maker.admin.delete.complete', 'admin');

            log_info('メーカー削除完了', ['Maker id' => $Maker->getId()]);
        } catch (ForeignKeyConstraintViolationException $e) {
            // 商品から参照されている場合のみ外部キー制約エラーとして扱う。
            // それ以外の例外（DB 障害等）は握り潰さず伝播させる。
            log_warning('メーカー削除エラー: 外部キー制約により削除できません', ['Maker id' => $Maker->getId(), 'exception' => $e]);

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Maker->getName()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('maker_admin_index');
    }

    /**
     * Move sort no with ajax.
     */
    #[Route('/%eccube_admin_route%/maker/move_sort_no', name: 'maker_admin_move_sort_no', methods: ['POST'])]
    public function moveSortNo(Request $request): Response
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $makerId => $sortNo) {
                $Maker = $this->makerRepository->find($makerId);
                if (null === $Maker) {
                    continue;
                }
                $Maker->setSortNo($sortNo);
                $this->entityManager->persist($Maker);
            }
            $this->entityManager->flush();
        }

        return new Response();
    }
}
