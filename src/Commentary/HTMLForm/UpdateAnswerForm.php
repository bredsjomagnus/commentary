<?php

namespace Maaa16\Commentary\HTMLForm;

use \Anax\HTMLForm\FormModel;
use \Anax\DI\DIInterface;
use \Maaa16\Commentary\Answer;

/**
 * Form to update an item.
 */
class UpdateAnswerForm extends FormModel
{
    /**
     * Constructor injects with DI container and the id to update.
     *
     * @param Anax\DI\DIInterface $di a service container
     * @param integer             $id to update
     */
    public function __construct(DIInterface $di, $answerid, $articleid)
    {
        parent::__construct($di);
        $answer = $this->getItemDetails($answerid);
        $this->form->create(
            [
                "id" => __CLASS__,
                "legend" => "Redigera svar",
            ],
            [
                "data" => [
                    "label"         => "Fråga",
                    "type"          => "textarea",
                    "class"         => "form-control",
                    "data"          => "markdown",
                    "value"         => $this->di->get("comm")->utf8Filter($answer->data)
                ],

                "id" => [
                    "type"      => "hidden",
                    "value"     => $answer->id
                ],

                "articleid" => [
                    "type"      => "hidden",
                    "value"     => $articleid
                ],



                "submit" => [
                    "type"          => "submit",
                    "class"         => "btn btn-primary",
                    "value"         => "Redigering",
                    "callback"      => [$this, "callbackSubmit"]
                ],

                // "delete" => [
                //     "type"          => "submit",
                //     "class"         => "btn btn-danger",
                //     "value"         => "Ta bort",
                //     "callback"      => [$this, "callbackDelete"]
                // ],

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
        $answer = new Answer();
        $answer->setDb($this->di->get("db"));
        $answer->find("id", $id);
        return $answer;
    }



    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return boolean true if okey, false if something went wrong.
     */
    public function callbackSubmit()
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");
        $artFact    = $this->di->get("articleFactory");
        $comm       = $this->di->get("comm");

        $answer = new Answer();
        $answer->setDb($this->di->get("db"));
        $answer->find("id", $this->form->value("id"));

        $answer->data = $this->form->value("data");
        $answer->updated = date("Y-m-d H:i:s");
        $answer->save();

        // $this->form->addOutput($article->title . " redigerad");

        $this->di->get("response")->redirect("commentary/article/".$this->form->value("articleid"));
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
