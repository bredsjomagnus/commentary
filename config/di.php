<?php
/**
 * Configuration file for DI container.
 */
return [

    // Services to add to the container.
    "services" => [
        "request" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Request\Request();
                $obj->init();
                return $obj;
            }
        ],
        "response" => [
            "shared" => true,
            //"callback" => "\Anax\Response\Response",
            "callback" => function () {
                $obj = new \Anax\Response\ResponseUtility();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "url" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Url\Url();
                $request = $this->get("request");
                $obj->setSiteUrl($request->getSiteUrl());
                $obj->setBaseUrl($request->getBaseUrl());
                $obj->setStaticSiteUrl($request->getSiteUrl());
                $obj->setStaticBaseUrl($request->getBaseUrl());
                $obj->setScriptName($request->getScriptName());
                $obj->configure("url.php");
                $obj->setDefaultsFromConfiguration();
                return $obj;
            }
        ],
        "router" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Route\Router();
                $obj->setDI($this);
                $obj->configure("route.php");
                return $obj;
            }
        ],
        "view" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\View\ViewCollection();
                $obj->setDI($this);
                $obj->configure("view.php");
                return $obj;
            }
        ],
        "viewRenderFile" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\View\ViewRenderFile2();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "session" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Session\SessionConfigurable();
                $obj->configure("session.php");
                $obj->start();
                return $obj;
            }
        ],
        "textfilter" => [
            "shared" => true,
            "callback" => "\Anax\TextFilter\TextFilter",
        ],
        "comm" => [
            "shared" => false,
            "callback" => function () {
                $comm = new \Maaa16\Commentary\Commentary();
                $comm->setDI($this);
                return $comm;
            }
        ],
        "commController" => [
            "shared" => false,
            "callback" => function () {
                $commController = new \Maaa16\Commentary\CommController();
                $commController->setDI($this);
                return $commController;
            }
        ],
        "commAssembler" => [
            "shared" => false,
            "callback" => function () {
                $commAssembler = new \Maaa16\Commentary\CommAssembler();
                $commAssembler->setDI($this);
                return $commAssembler;
            }
        ],
        "articleFactory" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Maaa16\Commentary\ArticleFactory();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "db" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Database\DatabaseQueryBuilder();
                $obj->configure("databaseconfig.php");
                return $obj;
            }
        ],
        "pageRender" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Page\PageRender();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "errorController" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Page\ErrorController();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "debugController" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Page\DebugController();
                $obj->setDI($this);
                return $obj;
            }
        ],
        "flatFileContentController" => [
            "shared" => true,
            "callback" => function () {
                $obj = new \Anax\Page\FlatFileContentController();
                $obj->setDI($this);
                return $obj;
            }
        ],
    ],
];
