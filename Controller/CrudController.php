<?php

namespace Symfonian\Indonesia\RestCrudBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\RestCrudBundle\SymfonianIndonesiaRestCrudEvents as Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends Controller
{
    /**
     * @var \Symfonian\Indonesia\RestCrudBundle\Manager\CrudManager
     */
    protected $manager;

    protected $template;

    abstract protected function getForm();

    /**
     * @Get("/form")
     *
     * @ApiDoc()
     */
    public function formAction()
    {
        return new JsonResponse($this->getForm());
    }

    /**
     * @Get("")
     *
     * @ApiDoc()
     */
    public function listAction(Request $request)
    {
        $alias = 'o';
        $page = $request->query->get('page', 1) - 1;
        $limit = $request->query->get('max_record', $this->container->getParameter('symfonian_id.rest_crud.per_page'));
        $filterUppercase = $request->query->get('normalize', false);

        $queryBuilder = $this->manager->createQueryBuilder($alias);

        $orderBy = $this->container->getParameter('symfonian_id.rest_crud.order_fields');
        foreach ($orderBy as $order) {
            $queryBuilder->addOrderBy(sprintf('%s.%s', $alias, $order['field']), $order['order']);
        }

        $filter = $request->query->get('filter', array());
        foreach ($filter as $key => $value) {
            $queryBuilder->orWhere(sprintf('%s.%s LIKE ?%d', $alias, $key, $key));
            $queryBuilder->setParameter($key, sprintf('%%s%', $filterUppercase? strtoupper($value): $value));
        }

        $event = new FilterQueryEvent();
        $event->setQueryBuilder($queryBuilder);
        $event->setAlias($alias);
        $this->fireEvent(Event::FILTER_LIST, $event);

        $paginator = $this->container->get('knp_paginator');
        $query = $this->manager->getQuery($queryBuilder, true, 1);
        $pagination = $paginator->paginate($query, $page, $limit);

        $currentPage = $pagination->getCurrentPageNumber();

        $output = array(
            'current' => $currentPage,
            'previous' => $currentPage - 1,
            'next' => $currentPage + 1,
            'records' => $this->manager->serialize($pagination->getItems()),
        );

        $view = new View();
        $view->setData($output);
        $view->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * @Get("/{id}")
     *
     * @ApiDoc()
     */
    public function detailAction(Request $request, $id)
    {
        return new JsonResponse('detail');
    }

    /**
     * @Post("")
     * @Post("/new", name="_post_new")
     *
     * @ApiDoc()
     */
    public function createAction(Request $request)
    {
        return new JsonResponse('create');
    }

    /**
     * @Put("/{id}")
     * @Post("/{id}/update", name="_post_update")
     *
     * @ApiDoc()
     */
    public function updateAction(Request $request, $id)
    {
        return new JsonResponse('update');
    }

    /**
     * @Delete("/{id}")
     * @Post("/{id}/delete", name="_post_delete")
     *
     * @ApiDoc()
     */
    public function deleteAction(Request $request, $id)
    {
        return new JsonResponse('delete');
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    protected function getManager()
    {
        return $this->manager;
    }

    protected function getTemplate()
    {
        return $this->template;
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}