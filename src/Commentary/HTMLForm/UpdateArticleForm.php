<?php

namespace Maaa16\Commentary\HTMLForm;

use \Anax\HTMLForm\FormModel;
use \Anax\DI\DIInterface;
use \Maaa16\Commentary\Article;

/**
 * Form to update an item.
 */
class UpdateArticleForm extends FormModel
{
    /**
     * Constructor injects with DI container and the id to update.
     *
     * @param Anax\DI\DIInterface $di a service container
     * @param integer             $id to update
     */
    public function __construct(DIInterface $di, $id)
    {
        parent::__construct($di);
        $article = $this->getItemDetails($id);
        $this->form->create(
            [
                "id" => __CLASS__,
                "legend" => "Redigera innehåll",
            ],
            [
                "title" => [
                    "label"         => "Titel*",
                    "type"          => "text",
                    "class"         => "form-control",
                    "validation"    => ["not_empty"],
                    "value"         => $article->title
                ],

                "status" => [
                    "type"          => "select",
                    "label"         => "Välj status",
                    "class"         => "form-control",
                    "description"   => "<i>Ett publiserat innehåll visas för användaren.</i>",
                    "size"          => 2,
                    "options"       => [
                        "notPublished"      => "Inte publiserad",
                        "published"         => "Publiserad",
                    ],
                    "value"         => $article->status,
                ],
                "data" => [
                    "label"         => "Text",
                    "type"          => "textarea",
                    "class"         => "form-control",
                    "value"         => $article->data
                ],

                "id" => [
                    "type"      => "hidden",
                    "value"     => $article->id
                ],

                "submit" => [
                    "type"          => "submit",
                    "class"         => "btn btn-primary",
                    "value"         => "Redigering",
                    "callback"      => [$this, "callbackSubmit"]
                ],

                "delete" => [
                    "type"          => "submit",
                    "class"         => "btn btn-danger",
                    "value"         => "Ta bort",
                    "callback"      => [$this, "callbackDelete"]
                ],

                "Återställ" => [
                    "type"      => "reset",
                    "class"     => "btn btn-default"
                ],
            ]
        );
    }



    /**
     * Get details on item to load form with.
     *
     * @param integer $id get details on item with id.
     *
     * @return object true if okey, false if something went wrong.
     */
    public function getItemDetails($id)
    {
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $id);
        return $article;
    }



    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return boolean true if okey, false if something went wrong.
     */
    public function callbackSubmit()
    {
        // $book = new Book();
        // $book->setDb($this->di->get("db"));
        // $book->find("id", $this->form->value("id"));
        // $book->column1 = $this->form->value("title");
        // $book->column2 = $this->form->value("column2");
        // $book->save();
        // $this->di->get("response")->redirect("book/update/{$book->id}");
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $this->form->value("id"));

        $article->title  = $this->form->value("title");
        $slug = $this->di->get("articleFactory")->slugify($this->form->value("title"));
        $article->slug = $slug;
        $article->status  = $this->form->value("status");
        $article->data = $this->form->value("data");
        $article->updated = date("Y-m-d H:i:s");
        $article->save();

        // $this->form->addOutput($article->title . " redigerad");

        $this->di->get("response")->redirect("admincontent");
        // return true;
    }

    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return boolean true if okey, false if something went wrong.
     */
    public function callbackDelete()
    {
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $this->form->value("id"));
        $article->deleted = date("Y-m-d H:i:s");
        $article->save();

        $this->di->get("response")->redirect("admincontent");
        // return true;
    }
}
