<?php

namespace Symfonian\Indonesia\RestCrudBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class CrudController extends Controller
{
    protected $manager;

    protected $template;

    /**
     * @Get("")
     * @Get("/list", name="_get_list")
     */
    public function listAction(Request $request)
    {

    }

    /**
     * @Post("")
     * @Post("/new", name="_post_new")
     */
    public function createAction(Request $request)
    {

    }

    /**
     * @Put("/{id}")
     * @Post("/{id}/update", name="_post_update")
     */
    public function updateAction(Request $request, $id)
    {

    }

    /**
     * @Get("/{id}")
     * @Get("/{id}/detail", name="_get_detail")
     */
    public function detailAction(Request $request, $id)
    {

    }

    /**
     * @Delete("/{id}")
     * @Post("/{id}/delete", name="_post_delete")
     */
    public function deleteAction(Request $request, $id)
    {

    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }
}