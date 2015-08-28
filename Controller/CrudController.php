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
    protected $manager;

    protected $template;

    protected $form;

    /**
     * @Get("/form")
     *
     * @ApiDoc()
     */
    public function formAction(Request $request)
    {
        return new JsonResponse($this->form);
    }

    /**
     * @Get("")
     *
     * @ApiDoc()
     */
    public function listAction(Request $request)
    {
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

    public function setForm($form)
    {
        $this->form = $form;
    }

    protected function getForm()
    {
        return $this->form;
    }
}