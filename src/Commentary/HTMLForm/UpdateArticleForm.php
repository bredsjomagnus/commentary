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

                // "status" => [
                //     "type"          => "select",
                //     "label"         => "Välj status",
                //     "class"         => "form-control",
                //     "description"   => "<i>Ett publiserat innehåll visas för användaren.</i>",
                //     "size"          => 2,
                //     "options"       => [
                //         "notPublished"      => "Inte publiserad",
                //         "published"         => "Publiserad",
                //     ],
                //     "value"         => $article->status,
                // ],

                "tags" => [
                    "label"         => "Taggar",
                    "type"          => "text",
                    "class"         => "form-control",
                    "value"         => $article->tags
                ],
                "data" => [
                    "label"         => "Fråga",
                    "type"          => "textarea",
                    "class"         => "form-control",
                    "data"          => "markdown",
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
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");
        $artFact    = $this->di->get("articleFactory");
        $comm       = $this->di->get("comm");

        $title = $this->form->value("title");
        if ($title == "") {
            $this->form->addOutput("Title måste anges");
            return false;
        }

        $tags = $this->form->value("tags");
        if ($tags == "") {
            $this->form->addOutput("Måste ange minst en tagg");
            return false;
        }

        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $this->form->value("id"));

        $article->title  = $this->form->value("title");
        $slug = $artFact->slugify($this->form->value("title"));



        //-------------- HANTERING AV TAGS ------------------

        // -- Gör en array av tags --//
        $tagsarray = explode(", ", $tags);

        //-- Trimmar varje tag och filtrerar så att inga tomma tags kommer med.
        $tagsarray = array_filter($tagsarray, function($value) {
            $value = trim($value);
            return $value !== '';
        });

        //-- Tar bort dubbletter
        $tagsarray  = array_unique($tagsarray);


        //-- Tar fram de gamla tagsen till en array.
        $oldtags    = explode(", ", $article->tags);

        //-- Tar gamla tags - nya tags = den eller de som skall tas bort.
        $trashtags  = array_diff($oldtags, $tagsarray);
        $comm->deleteTags($trashtags);

        //-- Tar nya tags - gamla tags = får ut vilka som skall läggas till.
        $newtags    = array_diff($tagsarray, $oldtags);
        $session->set("newtags", $newtags);
        /*
        * $tagspaths skall byggas upp till den arrays om innehåller alla tagpaths
        * $tagnames skall byggas upp till den arrays om innehåller alla tags
        *
        * För varje tagg i $newtags
        *   Kollar om taggen redan existerar:
        *       om existerar:       -> Ändra tagcount för taggen, hämta den redan existerande tagpathen
        *       om inte existerar:  -> Lägg till ny tagg. Sluggify tagg för att få unik rensad tag till tagpath.
        *   Slugifiera taggen med utf8 så den blir rensad men får behålla åäö. Lägg till tagsnames
        */

        // -- En sväng för de nya taggarna
        $tagpaths = [];
        $tagnames = [];
        foreach ($newtags as $tag) {

            if ($comm->tagExists($tag)) {
                $comm->addToExistingTag($tag);
                $tagpaths[] = $comm->getTagPath($tag);
                // $session->set("tagexists", "taggen existerar");
            } else {
                $tagpath = $artFact->slugifytagpath($tag);
                $comm->addNewTag($tag, $tagpath);
                $tagpaths[] = $tagpath;
                // $session->set("tagexists", "taggen existerar inte");
            }
            $tag = $artFact->slugifytagnameUTF8($tag);
            $tagnames[] = $tag;
        }

        // -- En sväng för de oförändrade taggarna
        $sametags    = array_diff($oldtags, $trashtags);
        foreach ($sametags as $tag) {
            $tagpaths[] = $comm->getTagPath($tag);
            $tag = $artFact->slugifytagnameUTF8($tag);
            $tagnames[] = $tag;
        }




        // // -- Gör tagsen till string för att kunna sparas till artikeln i databasen.
        $tagnames = implode(", ", $tagnames);
        $tagpaths = implode(", ", $tagpaths);

        //-------------- /HANTERING AV TAGS -----------------

        $article->slug = $slug;
        $article->tags = $tagnames;
        $article->tagpaths = $tagpaths;
        // $article->status  = $this->form->value("status");
        $article->data = $this->form->value("data");
        $article->updated = date("Y-m-d H:i:s");
        $article->save();

        // $this->form->addOutput($article->title . " redigerad");

        $this->di->get("response")->redirect("commentary/article/".$this->form->value("id"));
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
