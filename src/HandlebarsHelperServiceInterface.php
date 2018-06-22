<?php

namespace Drupal\handlebars_theme_handler;


interface HandlebarsHelperServiceInterface
{
    /**
     * registers a new helper
     * @param string $id
     * @param $helper
     */
    public function addHelper($id, $helper);

    /**
     * returns the registered helper ids
     * @return array
     */
    public function getHelperMethods();

    /**
     * returns the instances of the registered helpers
     * @return mixed
     */
    public function getHelpers();
}
