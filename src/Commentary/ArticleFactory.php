<?php
namespace Maaa16\Commentary;

use \Anax\DI\InjectionAwareInterface;
use \Anax\DI\InjectionAwareTrait;
use \Maaa16\Commentary\Article;

class ArticleFactory implements InjectionAwareInterface
{
    use InjectionAwareTrait;
    /**
     * Create a slug of a string, to be used as url.
     *
     * @param string $str the string to format as slug.
     *
     * @return str the formatted slug.
     */
    public function slugify($str)
    {
        $str = mb_strtolower(trim($str));
        $str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
        $str = preg_replace('/[^a-z0-9-]/', '', $str);
        $str = trim(preg_replace('/-+/', '', $str), '');
        $slug = $this->makeSlugUnique(strlen($str), $str);
        return $slug;
    }

    /**
    * See to it that every slug is unique
    *
    * @param integer $sluglength length of slug
    * @param string $slug the slug it self
    *
    * @return string $slug a unique slug
    */
    public function makeSlugUnique($sluglength, $slug)
    {
        $counter = 2;
        $this->di->get("db")->connect();
        $sql = "SELECT slug FROM RV1content WHERE slug = ?";
        while ($this->di->get("db")->executeFetchAll($sql, [$slug])) {
            if (strlen($slug) == $sluglength) {
                $slug = $slug ."".$counter."";
            } else {
                $slug = substr($slug, 0, $sluglength);
                $slug = $slug ."".$counter."";
            }
            $counter += 1;
        }

        return $slug;
    }

    /**
    * See to it that if path already exists it will be set to null
    *
    * @param object $app
    * @param string $path the path to check
    * @param integer $id to check database with
    *
    * @return string $path which is null if other exact same path existed before
    */
    public function checkPath($path, $id)
    {
        $sql = "SELECT path FROM RV1content WHERE path = ? AND NOT id = ?";
        if ($this->di->get("db")->executeFetchAll($sql, [$path, $id])) {
            $path = null;
        }

        return $path;
    }

    /**
    * Collect right filters in array
    *
    * @param boolean $markdown is markdown choosen
    * @param boolean $bbcode is bbcode choosen
    * @param boolean $link is link choosen
    * @param boolean $nl2br is nl2br choosen
    *
    * @return string $blogfilter with correct filters in correct order.
    */
    public function getFilters($markdown, $bbcode, $link, $nl2br)
    {
        $bloggarray = [];
        if ($markdown) {
            $bloggarray[] = 'markdown';
        }
        if ($bbcode) {
            $bloggarray[] = 'bbcode';
        }
        if ($link) {
            $bloggarray[] = 'link';
        }
        if ($nl2br) {
            $bloggarray[] = 'nl2br';
        }

        $blogfilter = implode(",", $bloggarray);

        return $blogfilter;
    }

    public function getFilteredHTML($id)
    {
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $id);
        // $article->find("status", "published");
        $filteredData = $this->di->get("textfilter")->parse($article->data, ["markdown"]);
        return $filteredData;
    }

    public function getId($slug)
    {
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("slug", $slug);
        return $article->id;
    }

    public function getTitle($id)
    {
        $article = new Article();
        $article->setDb($this->di->get("db"));
        $article->find("id", $id);
        return $article->title;
    }

    // public function getFilteredHTML($id)
    // {
    //     $content = new Content();
    //     $content->setDb($this->di->get("db"));
    //     $content->find("id", $id);
    //     $content->find("status", "published");
    //     $filteredData = $this->di->get("textfilter")->parse($content->data, [$content->$filter]);
    //     return $filteredData;
    // }
}
