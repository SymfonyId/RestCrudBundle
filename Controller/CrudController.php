<?php

namespace Symfonian\Indonesia\RestCrudBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\RestCrudBundle\SymfonianIndonesiaRestCrudEvents as Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        return $this->handleView(View::create($this->getForm()));
    }

    /**
     * @Get("")
     *
     * @ApiDoc(
     *      filters={
     *          {"name"="page", "dataType"="integer", "description"="Page number"},
     *          {"name"="max_record", "dataType"="integer", "description"="Max result per page"},
     *          {"name"="filter", "dataType"="array", "description"="Format: filter[field1]=value1&filter[field2]=value2"},
     *          {"name"="normalize", "dataType"="boolean", "description"="0 = false; 1 = true"}
     *      }
     * )
     */
    public function listAction(Request $request)
    {
        $alias = 'o';
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('max_record', 10);
        $filterUppercase = $request->query->get('normalize', 0);

        $queryBuilder = $this->getManager()->createQueryBuilder($alias);

        $orderBy = array(array('field' => 'id', 'order' => 'asc'));
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

        $pagination = $this->paginate($queryBuilder, $page, $limit);
        $currentPage = $pagination->getCurrentPageNumber();
        $previous = 1 === $currentPage? 1: $currentPage - 1;
        $next = $pagination->getTotalItemCount() > ($limit * $page) ? $currentPage + 1: $currentPage;

        $output = array(
            'current' => $currentPage,
            'previous' => $previous,
            'next' => $next,
            'records' => $this->getManager()->serialize($pagination->getItems()),
        );

        return $this->handleView(View::create($output));
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
    public function deleteAction($id)
    {
        $manager = $this->getManager();
        $entity = $manager->find($id);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Record with id %s not found', $id));
        }
        
        $manager->delete($entity);

        return $this->handleView(View::create(null, Response::HTTP_NO_CONTENT));
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
        if (! $this->manager) {
            throw new \RuntimeException('You must override `getManager()` on Controller or use Crud annotation to define the manager.');
        }

        return $this->manager;
    }

    protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $page
     * @param $limit
     * @return \Knp\Component\Pager\Pagination\AbstractPagination
     */
    protected function paginate(QueryBuilder $queryBuilder, $page, $limit)
    {
        $paginator = $this->container->get('knp_paginator');
        $query = $this->getManager()->getQuery($queryBuilder, true, 1);

        return $paginator->paginate($query, $page, $limit);
    }

    protected function fireEvent($name, $handler)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($name, $handler);
    }
}