<?php

namespace Maaa16\Commentary\HTMLForm;

use \Anax\HTMLForm\FormModel;
use \Anax\DI\DIInterface;
use \Maaa16\Commentary\Article;

// use \Maaa16\Content\ContentFactory;

/**
 * Form to create an item.
 */
class CreateArticleForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param Anax\DI\DIInterface $di a service container
     */
    public function __construct(DIInterface $di)
    {
        parent::__construct($di);
        $this->form->create(
            [
                "id" => __CLASS__,
                "legend" => "Lägg till fråga",
            ],
            [
                "title" => [
                    "label"         => "Titel*",
                    "type"          => "text",
                    "class"         => "form-control",
                    // "validation"    => ["not_empty"],
                ],

                "tags" => [
                    "label"         => "Taggar*",
                    "type"          => "text",
                    "class"         => "form-control",
                    "placeholder"   => "Minst en tagg på följande vis (tag1, tag2, tag3)",
                    // "validation"    => ["not_empty"],
                ],


                // "type" => [
                //     "label"         => "Klassificering",
                //     "type"          => "text",
                //     "class"         => "form-control",
                //     "value"         => "Fråga",
                //     "readonly"      => true,
                //     // "validation"    => ["not_empty"],
                // ],
                // "status" => [
                //     "type"          => "select",
                //     "label"         => "Välj status publiserad/inte publiserad",
                //     "class"         => "form-control",
                //     // "description"   => "Here you can place a description.",
                //     "size"          => 2,
                //     "options"       => [
                //         "notPublished"      => "Inte publiserad",
                //         "published"         => "Publiserad",
                //     ],
                //     "value"   => "published",
                // ],
                // "filter" => [
                //     "label"         => "Filter",
                //     "type"          => "text",
                //     "class"         => "form-control",
                //     "value"         => "markdown",
                //     "readonly"      => true,
                //     // "validation"    => ["not_empty"],
                // ],

                "data" => [
                    "label"         => "Fråga",
                    "type"          => "textarea",
                    "class"         => "createtextarea",
                    "data"          => "markdown"
                ],

                "submit" => [
                    "type"          => "submit",
                    "class"         => "btn btn-primary",
                    "value"         => "Lägg till fråga",
                    "callback"      => [$this, "callbackSubmit"]
                ],
            ]
        );
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

        $article = new Article();
        $article->setDb($db);

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

        //-------------- HANTERING AV TAGS ------------------

        // -- Gör en array av tags --//
        $tagsarray = explode(", ", $tags);

        //-- Trimmar varje tag och filtrerar så att inga tomma tags kommer med.
        $tagsarray = array_filter($tagsarray, function ($value) {
            $value = trim($value);
            return $value !== '';
        });

        //-- Tar bort dubbletter
        $tagsarray = array_unique($tagsarray);

        /*
        * $tagspaths skall byggas upp till den arrays om innehåller alla tagpaths
        * $tagnames skall byggas upp till den arrays om innehåller alla tags
        *
        * För varje tagg i tagsarray
        *   Kollar om taggen redan existerar:
        *       om existerar:       -> Ändra tagcount för taggen, hämta den redan existerande tagpathen
        *       om inte existerar:  -> Lägg till ny tagg. Sluggify tagg för att få unik rensad tag till tagpath.
        *   Slugifiera taggen med utf8 så den blir rensad men får behålla åäö. Lägg till tagsnames
        */
        $tagpaths = [];
        $tagnames = [];
        foreach ($tagsarray as $tag) {
            $tag = $artFact->slugifytagnameUTF8($tag);
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
            // $tag = $artFact->slugifytagnameUTF8($tag);
            $tagnames[] = $tag;
        }

        // // -- Gör tagsen till string för att kunna sparas till artikeln i databasen.
        $tagnames = implode(", ", $tagnames);
        $tagpaths = implode(", ", $tagpaths);

        //-------------- /HANTERING AV TAGS -----------------

        $slug = $artFact->slugify($title);

        $article->title  = $this->form->value("title");
        $article->slug  = $slug;
        // $article->type = $this->form->value("type");
        $article->tags = $tagnames;
        $article->tagpaths = $tagpaths;
        $article->type = "Fråga";
        $article->filter = "markdown";
        // $article->filter = $this->form->value("filter");
        // $article->status = $this->form->value("status");
        $article->status = 'published';
        $article->data = $this->form->value("data");
        $article->user = $session->get("userid");
        $article->save();

        // $comm->collectTags($sluggedtags, $db->lastInsertId());

        $this->form->addOutput($article->title . " tillagd i databasen");

        $this->di->get("response")->redirect("commentary/articles/alla");
    }
}
