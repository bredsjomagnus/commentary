<?php

namespace Maaa16\Commentary;

use \Anax\DI\InjectionAwareInterface;
use \Anax\DI\InjectionAwareTrait;
use \Maaa16\Commentary\HTMLForm\CreateArticleForm;
use \Maaa16\Commentary\HTMLForm\UpdateArticleForm;
use \Maaa16\Commentary\HTMLForm\DeleteArticleForm;

/**
 * A controller for the Commentary.
 *
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
class CommController implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
     * Commetnarypage.
     *
     * @return void
     */
    public function commentarypage()
    {
        // $path = $this->di->get("request")->getRoute();
        $file = ANAX_INSTALL_PATH . "/content/commentary/index.md";

        // Check that file is really in the right place
        $real = realpath($file);
        $base = realpath(ANAX_INSTALL_PATH . "/content/");
        if (strncmp($base, $real, strlen($base))) {
            return;
        }

        // Get content from markdown file
        $content = file_get_contents($file);
        $content = $this->di->get("textfilter")->parse($content, ["yamlfrontmatter", "shortcode", "markdown", "titlefromheader"]);

        // Render a standard page using layout
        $this->di->get("view")->add("default1/article", [
            "content" => $content->text
        ]);

        // Hämta comments från databasen och montera ihop tabell som skickas vidare till vyn.
        $comments = $this->di->get("comm")->getComment();
        $comments = $this->di->get("commAssembler")->assemble($comments);

        $this->di->get("view")->add("commentary/formfield", [], "formfield");
        $this->di->get("view")->add("commentary/comments", ["comments" => $comments], "comments");

        $this->di->get("pageRender")->renderPage($content->frontmatter, "commentary", 200);
    }

    /**
     * Add comment to page
     *
     * @return void
     */
    public function addComment()
    {
        if (null !== $this->di->get("request")->getPost("commentbtn")) {
            $commentOn = $this->di->get("request")->getPost("article");
            $comment = $this->di->get("request")->getPost("comment");
            $username = $this->di->get("request")->getPost("username");
            $email = $this->di->get("request")->getPost("email");
            $path = $this->di->get("request")->getGet("path");
            // Kontroll om textarean är tom innan den läggs till.
            if (strlen(trim($comment))) {
                $this->di->get("comm")->addComment($commentOn, $username, $email, $comment);
            }
        } else if (null !== $this->di->get("request")->getPost("resetdbbtn")) {
            $this->di->get("comm")->resetComment();
        }
        // $this->commentarypage();
        $this->di->get("response")->redirect("article/".$path);
    }

    /**
     * Edit comment
     *
     * @return void
     */
    public function editComment()
    {
        if (null !== $this->di->get("request")->getGet("id")) {
            $path = $this->di->get("request")->getGet("path");
            $id = $this->di->get("request")->getGet("id");
            $res = $this->di->get("comm")->editCommentLoad($id);

            // $path = $this->di->get("request")->getRoute();
            // $file = ANAX_INSTALL_PATH . "/content/editcomment.md";
            // // Check that file is really in the right place
            // $real = realpath($file);
            // $base = realpath(ANAX_INSTALL_PATH . "/content/");
            // if (strncmp($base, $real, strlen($base))) {
            //     return;
            // }
            //
            // // Get content from markdown file
            // $content = file_get_contents($file);
            // $content = $this->di->get("textfilter")->parse($content, ["yamlfrontmatter", "shortcode", "markdown", "titlefromheader"]);
            //
            // // Render a standard page using layout
            // $this->di->get("view")->add("default1/article", [
            //     "content" => $content->text
            // ]);

            // Hämta comments från databasen och montera ihop tabell som skickas vidare till vyn.
            // $comments = $this->app->comm->getComment($this->app);
            // $comments = $this->app->commAssembler->assemble($this->app, $res);

            $this->di->get("view")->add("commentary/editcomment", ["comment" => $res[0]->comm, "email" => $res[0]->email, "id" => $res[0]->id, "path" => $path]);
            // $this->app->view->add("commentary/comments", ["comments" => $comments], "comments");
            $title = "redigera kommentar | Maaa16";
            $this->di->get("pageRender")->renderPage(["title" => $title], "commentary");
        } else {
            $this->commentarypage();
            // $this->di->get("response")->redirect("article/".$path);
        }
    }

    /**
     * Edit comment
     *
     * @return void
     */
    public function editCommentProcess()
    {
        $path = $this->di->get("request")->getPost("path");
        if (null !== $this->di->get("request")->getPost("editcommentbtn")) {
            $comment = $this->di->get("request")->getPost("comment");
            $id = $this->di->get("request")->getPost("id");
            if (strlen(trim($comment))) {
                $this->di->get("comm")->editCommentSave($id, $comment);
            } else {
                $this->di->get("comm")->deleteComment($id);
            }
        } else if (null !== $this->di->get("request")->getPost("deletecommentbtn")) {
            $id = $this->di->get("request")->getPost("id");
            $this->di->get("comm")->deleteComment($id);
        }
        // $this->commentarypage();
        if ($path != 'admin') {
            $this->di->get("response")->redirect("article/".$path);
        } else {
            $this->di->get("response")->redirect("admincomments");
        }
    }

    /**
     * Add like to comment
     *
     * @return void
     */
    public function addLikeProcess()
    {
        $path = $this->di->get("request")->getGet("path");
        // Om användaren stämmer med vad som skickas. Så att ingen annan via url kan 'Gilla' kommentar som annan användare
        if ($this->di->get("session")->get('userid') == $this->di->get("request")->getGet("userid")) {
            // Om det finns ifylld id för comment
            if (null !== $this->di->get("request")->getGet("commentid")) {
                $userid = $this->di->get("request")->getGet("userid");
                $commentid = $this->di->get("request")->getGet("commentid");
                $this->di->get("comm")->addLike($userid, $commentid);
            }
        }
        $this->di->get("response")->redirect("article/".$path);
    }

    public function articleCommentary($path)
    {
        $id = $this->di->get("articleFactory")->getId($path);
        $title = "testkommentarer";
        $comments = $this->di->get("comm")->getComments($id);
        $form = $this->di->get("commAssembler")->getForm($id, $path);
        $filtereddata = $this->di->get("articleFactory")->getFilteredHTML($id);
        $hasComments = ($comments == null) ? false : true;
        $this->di->get("view")->add("commentary/article", ["article" => $filtereddata, "form" => $form, "comments" => $comments, "hasComments" => $hasComments, "path" => $path, "id" => $id]);

        $this->di->get("pageRender")->renderPage(["title" => $title]);
    }

    /**
     * Books landing page.
     *
     * @return void
     */
    public function getArticles()
    {
        $title      = "Innehåll | Maaa16";
        $view       = $this->di->get("view");
        $pageRender = $this->di->get("pageRender");
        $article = new Article();
        $article->setDb($this->di->get("db"));

        $data = [
            "contents" => $article->findAll(),
        ];
        $view->add("commentary/allarticles", $data);
        $pageRender->renderPage(["title" => $title]);
    }

    /**
     * Handler with form to create a new item.
     *
     * @return void
     */
    public function getPostCreateArticle()
    {
        $title      = "Lägg till innehåll | Maaa16";
        $view       = $this->di->get("view");
        $pageRender = $this->di->get("pageRender");
        $form       = new CreateArticleForm($this->di);

        $form->check();

        $data = [
            "form" => $form->getHTML(),
        ];

        $view->add("commentary/createarticle", $data);

        $pageRender->renderPage(["title" => $title]);
    }

    /**
     * Handler with form to update an book.
     *
     * @return void
     */
    public function getPostUpdateArticle($id)
    {
        $title      = "Redigera innehåll | Maaa16";
        $view       = $this->di->get("view");
        $pageRender = $this->di->get("pageRender");
        $form       = new UpdateArticleForm($this->di, $id);

        $form->check();

        $data = [
            "form" => $form->getHTML(),
        ];

        $view->add("commentary/update", $data);

        $pageRender->renderPage(["title" => $title]);
    }





}
