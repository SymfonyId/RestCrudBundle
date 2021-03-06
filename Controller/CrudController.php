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
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterEntityEvent;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterFormEvent;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterPersistEvent;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterQueryEvent;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterRequestEvent;
use Symfonian\Indonesia\RestCrudBundle\Event\FilterValidationEvent;
use Symfonian\Indonesia\RestCrudBundle\Form\FormInterface;
use Symfonian\Indonesia\RestCrudBundle\SymfonianIndonesiaRestCrudEvents as Event;
use Symfonian\Indonesia\RestCrudBundle\Util\Paging;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class CrudController extends Controller
{
    /**
     * @var \Symfonian\Indonesia\RestCrudBundle\Manager\CrudManager
     */
    protected $manager;

    /**
     * @var \Symfonian\Indonesia\RestCrudBundle\Form\FormInterface
     */
    protected $form;

    /**
     * @Get("/form")
     *
     * @ApiDoc()
     */
    public function formAction()
    {
        return $this->handleView(View::create($this->getForm()->toArray()));
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
            $queryBuilder->orWhere(sprintf('%s.%s LIKE :%s', $alias, $key, $key));
            $queryBuilder->setParameter($key, sprintf('%%%s%%', $filterUppercase ? strtoupper($value) : $value));
        }

        $event = new FilterQueryEvent();
        $event->setQueryBuilder($queryBuilder);
        $event->setAlias($alias);
        $this->fireEvent(Event::FILTER_LIST, $event);

        $pagination = $this->paginate($event->getQueryBuilder(), $page, $limit);
        $currentPage = $pagination->getCurrentPageNumber();
        $previous = 1 === $currentPage ? 1 : $currentPage - 1;
        $next = $pagination->getTotalItemCount() > ($limit * $page) ? $currentPage + 1 : $currentPage;

        $output = new Paging();
        $output->setCurrent($currentPage);
        $output->setNext($next);
        $output->setPrevious($previous);
        $output->setRecords($pagination->getItems());

        return $this->handleView(View::create(array('data' => $output)));
    }

    /**
     * @Get("/{id}")
     *
     * @ApiDoc()
     */
    public function detailAction($id)
    {
        $entity = $this->findOr404Error($id);
        $event = new FilterEntityEvent();
        $event->setEntity($entity);
        $event->setManager($this->getManager());
        $this->fireEvent(Event::FILTER_ENTITY, $event);

        if ($response = $event->getResponse()) {
            return $response;
        }

        return $this->handleView(View::create($event->getEntity()));
    }

    /**
     * @Post("")
     * @Post("/new", name="_post_new")
     *
     * @ApiDoc()
     */
    public function createAction(Request $request)
    {
        return $this->createOrUpdate($this->getManager()->createNew(), $request);
    }

    /**
     * @Put("/{id}")
     * @Post("/{id}/update", name="_post_update")
     *
     * @ApiDoc()
     */
    public function updateAction(Request $request, $id)
    {
        return $this->createOrUpdate($this->findOr404Error($id), $request);
    }

    /**
     * @Delete("/{id}")
     * @Post("/{id}/delete", name="_post_delete")
     *
     * @ApiDoc()
     */
    public function deleteAction($id)
    {
        $entity = $this->findOr404Error($id);
        $event = new FilterEntityEvent();
        $event->setEntity($entity);
        $event->setManager($this->getManager());
        $this->fireEvent(Event::FILTER_ENTITY, $event);

        if ($response = $event->getResponse()) {
            return $response;
        }
        $this->getManager()->delete($event->getEntity());

        return $this->handleView(View::create(null, Response::HTTP_NO_CONTENT));
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return \Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Manager
     */
    protected function getManager()
    {
        if (!$this->manager) {
            throw new \RuntimeException('You must override `getManager()` on Controller or use Crud annotation to define the manager.');
        }

        return $this->container->get($this->manager);
    }

    /**
     * @return FormInterface
     */
    protected function getForm()
    {
        if (!$this->form) {
            throw new \RuntimeException('You must override `getForm()` on Controller or use Crud annotation to define the form.');
        }

        try {
            $formObject = $this->container->get($this->form);
        } catch (\Exception $ex) {
            if ($this->form instanceof FormInterface) {
                $formObject = new $this->form();
            } else {
                throw new \InvalidArgumentException(sprintf('%s class is must implement \Symfonian\Indonesia\RestCrudBundle\Form\FormInterface', $this->form));
            }
        }

        return $formObject;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $page
     * @param $limit
     *
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

    protected function findOr404Error($id)
    {
        $entity = $this->getManager()->find($id);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Entity with id %d not found.', $id));
        }

        return $entity;
    }

    protected function createOrUpdate(EntityInterface $entity, Request $request)
    {
        $requestEvent = new FilterRequestEvent();
        $requestEvent->setRequest($request);
        $requestEvent->setController($this);

        $this->fireEvent(Event::FILTER_REQUEST, $requestEvent);

        $response = $requestEvent->getResponse();
        if ($response) {
            return $response;
        }

        $formEvent = new FilterFormEvent();
        $formEvent->setForm($this->getForm());

        $this->fireEvent(Event::FILTER_FORM, $formEvent);

        $formData = $formEvent->getData();
        if (!$formData) {
            $formData = $this->getManager()->createNew();
            $formEvent->setData($formData);
        }

        $validationEvent = new FilterValidationEvent();
        $validationEvent->setForm($formEvent->getForm());
        $validationEvent->setData($formEvent->getData());

        $this->fireEvent(Event::FILTER_VALIDATION, $validationEvent);

        $response = $validationEvent->getResponse();
        if ($response) {
            return $response;
        }

        $form = $validationEvent->getForm();
        $form->handleRequest($requestEvent->getRequest());

        $view = new View();
        if (!$form->isValid()) {
            $view->setData($form->getErrors());
            $view->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);

            return $this->handleView($view);
        }

        $this->getManager()->save($entity, $form->getData());

        $persistEvent = new FilterPersistEvent();
        $persistEvent->setEntity($entity);
        $persistEvent->setManager($this->getManager());
        $persistEvent->setRequest($request);

        $this->fireEvent(Event::FILTER_PERSIST, $persistEvent);

        $view->setData($persistEvent->getEntity());

        return $this->handleView($view);
    }
}
