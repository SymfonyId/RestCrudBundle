<?php

namespace Symfonian\Indonesia\RestCrudBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
        $queryBuilder->addOrderBy(sprintf('%s.%s', $alias, $this->container->getParameter('symfonian_id.rest_crud.order_field')), 'DESC');
        $filter = $filterUppercase? strtoupper($request->query->get('filter')) : $request->query->get('filter');

        return new JsonResponse('list');
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
}