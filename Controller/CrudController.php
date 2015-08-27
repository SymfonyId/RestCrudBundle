<?php

namespace Symfonian\Indonesia\RestCrudBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Delete;

abstract class CrudController extends Controller
{
    protected $manager;

    protected $template;

    /**
     * @Get("")
     * @Get("/list", name="_get_list")
     */
    public function listAction()
    {

    }

    /**
     * @Post("")
     * @Post("/new", name="_post_new")
     */
    public function createAction()
    {

    }

    /**
     * @Put("/{id}")
     * @Post("/{id}/update", name="_post_update")
     */
    public function updateAction($id)
    {

    }

    /**
     * @Get("/{id}")
     * @Get("/{id}/detail", name="_get_detail")
     */
    public function detailAction($id)
    {

    }

    /**
     * @Delete("/{id}")
     * @Post("/{id}/delete", name="_post_delete")
     */
    public function deleteAction($id)
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