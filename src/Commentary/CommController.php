<?php

namespace Maaa16\Commentary;

use \Anax\DI\InjectionAwareInterface;
use \Anax\DI\InjectionAwareTrait;
use \Maaa16\Commentary\HTMLForm\CreateArticleForm;
use \Maaa16\Commentary\HTMLForm\UpdateArticleForm;
use \Maaa16\Commentary\HTMLForm\DeleteArticleForm;
use \Maaa16\Commentary\HTMLForm\UpdateAnswerForm;

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
    public function overview()
    {
        $title          = "Översikt | Allt om jakt";
        $textfilter     = $this->di->get("textfilter");
        $comm           = $this->di->get("comm");

        //-----------------------------------------------------------

        // ['id' => id, 'user' => user, 'title' => title, 'tags' => tags, 'created' => created]
        // $articles       = $comm->getArticlesArray($tagpath);
        // $tag            = ($tagpath == 'alla') ? 'Alla' : $comm->getTag($tagpath);
        $tagbar         = $comm->getTagBar('5');

        $data = [
            // "articles"      => $articles,
            // "tag"           => $tag,
            "tagbar"        => $tagbar,
        ];

        $this->di->get("view")->add("commentary/overview", $data);
        $this->di->get("pageRender")->renderPage(["title" => $title]);
    }

    /**
     * Commetnarypage.
     *
     * @return void
     */
    public function articles($tagpath)
    {
        $title          = "Frågor | Allt om spel";
        $textfilter     = $this->di->get("textfilter");
        $comm           = $this->di->get("comm");

        //-----------------------------------------------------------

        // ['id' => id, 'user' => user, 'title' => title, 'tags' => tags, 'created' => created]
        $articles       = $comm->getArticlesArray($tagpath);
        $tag            = ($tagpath == 'alla') ? 'Alla' : $comm->getTag($tagpath);
        $tagbar         = $comm->getTagBar('5');

        $data = [
            "articles"      => $articles,
            "tag"           => $tag,
            "tagbar"        => $tagbar,
        ];

        $this->di->get("view")->add("commentary/articles", $data);
        $this->di->get("pageRender")->renderPage(["title" => $title]);
    }

    public function articlePage($id)
    {
        $title          = "Fråga | Allt om brädspel";
        $view           = $this->di->get("view");
        $artFact        = $this->di->get("articleFactory");
        $commAss        = $this->di->get("commAssembler");
        $comm           = $this->di->get("comm");
        $session        = $this->di->get("session");

        //------------------------------------------------------

        //-- return ["article" => dbobj, "aritcledata" => markdown filtered data] --//
        $article                    = $artFact->getArticle($id);

        //-- menu with tags --//
        $tagbar                     = $comm->getTagBar('5');

        //-- return html form --//
        $form                       = $commAss->getForm($id);

        //-- return dbobj --//
        $answers                    = $comm->getAnswers($id);
        $hasAnswers                 = empty($answers) ? false : true;

        //-- return dbobj --//
        $articlecomments            = $comm->getArticleComments($id);
        $hasArticleComments         = empty($articlecomments) ? false : true;

        //-- return dbobj --//
        $answercomments             = $comm->getAnswerComments($id);

        //-- return dbobj all votes on this article with id = $id --//
        $articlevotes               = $comm->getArticlevotes($id);

        //-- return boolean --//
        $ownarticle                 = $comm->ownArticle($id);

        //-- return boolean --//
        $hasvotedonarticle          = $comm->userHasVotedOnArticle($id);

        //-- sum of article votes --//
        $articlevotesum             = $comm->getArticleVoteSum($id);

        $totnumbofarticlevotes      = $comm->getTotNumbOfAricleVotes($id);

        //-- return dbobj of answers for this article with articleid = $id --//
        $answervotes                = $comm->getAnswervotes($id);

        //------------------------------------------------------

        $data = [
            "article"                   => $article,
            "form"                      => $form,
            "answers"                   => $answers,
            "hasAnswers"                => $hasAnswers,
            "articlecomments"           => $articlecomments,
            "hasArticleComments"        => $hasArticleComments,
            "answercomments"            => $answercomments,
            "tagbar"                    => $tagbar,
            "articlevotes"              => $articlevotes,
            "ownarticle"                => $ownarticle,
            "hasvotedonarticle"         => $hasvotedonarticle,
            "articlevotesum"            => $articlevotesum,
            "articlevotesum"            => $articlevotesum,
            "totnumbofarticlevotes"     => $totnumbofarticlevotes,
            "answervotes"               => $answervotes,
        ];

        $view->add("commentary/article", $data);
        $this->di->get("pageRender")->renderPage(["title" => $title]);
    }

    public function addAnswerProcess()
    {
        $request    = $this->di->get("request");
        $response   = $this->di->get("response");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            if (null !== $request->getPost("addanswerbtn")) {
                $articleid      = $request->getPost('answerto');

                $answerdata = [
                    "answerto"  => htmlentities($request->getPost('answerto')),
                    "user"      => htmlentities($request->getPost('user')),
                    "data"      => htmlentities($request->getPost('data')),
                ];



                if($comm->notEmpty($answerdata)){
                    $comm->addAnswer($answerdata);
                }

                $response->redirect("commentary/article/".$articleid);
            }
        } else {
            $response->redirect("login");
        }
    }

    public function addArticleCommentProcess()
    {
        $request    = $this->di->get("request");
        $response   = $this->di->get("response");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            if (null !== $request->getPost("addarticlecommentbtn")) {
                $articleid      = $request->getPost('commentto');

                $articlecommentdata = [
                    "commentto" => htmlentities($request->getPost('commentto')),
                    "user"      => htmlentities($request->getPost('user')),
                    "data"      => htmlentities($request->getPost('data')),
                ];

                if($comm->notEmpty($articlecommentdata)){
                    $comm->addArticleComment($articlecommentdata);
                }

                $response->redirect("commentary/article/".$articleid);
            }
        } else {
            $response->redirect("login");
        }
    }


    public function addAnswerCommentProcess()
    {
        $request    = $this->di->get("request");
        $response   = $this->di->get("response");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            if (null !== $request->getPost("addanswercommentbtn")) {
                $articleid      = $request->getPost('articleid');

                $answercommentdata = [
                    "articleid" => htmlentities($request->getPost('articleid')),
                    "commentto" => htmlentities($request->getPost('commentto')),
                    "user"      => htmlentities($request->getPost('user')),
                    "data"      => htmlentities($request->getPost('data')),
                ];

                if ($comm->notEmpty($answercommentdata)) {
                    $comm->addAnswerComment($answercommentdata);
                }

                $response->redirect("commentary/article/".$articleid);
            }
        } else {
            $response->redirect("login");
        }
    }

    public function userInfo($id)
    {
        $title      = "Användare | Allt om brädspel";
        $view       = $this->di->get("view");
        $pagerender = $this->di->get("pageRender");
        $response   = $this->di->get("response");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $articleview        = $comm->getArticleView($id);
            $answerview         = $comm->getAnswerView($id);
            $tagbar             = $comm->getTagBar('5');

            $data = [
                "articleview"   => $articleview,
                "answerview"   => $answerview,
                "uid"           => $id,
                "tagbar"        => $tagbar,
            ];

            $this->di->get("view")->add("commentary/userinfo", $data);
            $this->di->get("pageRender")->renderPage(["title" => $title]);

        } else {
            $response->redirect("login");
        }
    }


    public function voteArticleProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $artFact    = $this->di->get("articleFactory");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $article            = $artFact->getArticle($articleid);

            $authorid           = $article['article']->user;
            $voterid            = $session->get("userid");

            $vote               = ($request->getGet('vote') === 'up') ? 1 : -1;

            $votedata = array();
            $votedata = [
                "articleid"     => $articleid,
                "authorid"      => $authorid,
                "voterid"       => $voterid,
                "vote"          => $vote,
            ];

            $comm->voteArticle($votedata);
            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }

    public function voteArticleCommentProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $artFact    = $this->di->get("articleFactory");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $voterid            = $session->get("userid");
            $articlecommentid   = ($request->getGet('articlecommentid') != null) ? htmlentities($request->getGet('articlecommentid')) : null;
            $vote               = ($request->getGet('vote') === 'up') ? 1 : -1;

            $session->set("vote", $request->getGet('vote'));
            $authorid           = $comm->getArticleCommentsAuthor($articlecommentid);

            $votedata = array();
            $votedata = [
                "articleid"         => $articleid,
                "articlecommentid"  => $articlecommentid,
                "authorid"          => $authorid,
                "voterid"           => $voterid,
                "vote"              => $vote,
            ];

            if ($comm->notEmpty($votedata)) {
                $comm->voteArticleComment($votedata);
            }

            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }

    public function cancelArticleCommentVoteProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $articlecommentid   = ($request->getGet('articlecommentid') != null) ? htmlentities($request->getGet('articlecommentid')) : null;
            $voterid            = $session->get("userid");

            $votedata = array();
            $votedata = [
                "articlecommentid"  => $articlecommentid,
                "voterid"           => $voterid,
            ];

            if ($comm->notEmpty($votedata)) {
                $comm->cancelVoteArticleComment($votedata);
            }

            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }


    public function voteAnswerProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $artFact    = $this->di->get("articleFactory");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            /*
            * Samla in vote (up eller down), answerid, authorid för att kunna lägga
            * in i RVIXanswervotes
            */
            $vote               = ($request->getGet('vote') === 'up') ? 1 : -1;
            $answerid           = ($request->getGet('answerid') != null) ? htmlentities($request->getGet('answerid')) : null;
            $authorid           = ($request->getGet('authorid') != null) ? htmlentities($request->getGet('authorid')) : null;

            $voterid            = $session->get("userid");

            $votedata = array();
            $votedata = [
                "articleid"     => $articleid,
                "answerid"      => $answerid,
                "authorid"      => $authorid,
                "voterid"       => $voterid,
                "vote"          => $vote,
            ];

            $session->delete("answervotedata");
            if ($comm->notEmpty($votedata)) {
                $session->set("answervotedata", $votedata);
                $comm->voteAnswer($votedata);
            }



            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }

    public function voteAnswerCommentProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $artFact    = $this->di->get("articleFactory");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $voterid            = $session->get("userid");
            $answerid           = ($request->getGet('answerid') != null) ? htmlentities($request->getGet('answerid')) : null;
            $answercommentid    = ($request->getGet('answercommentid') != null) ? htmlentities($request->getGet('answercommentid')) : null;
            $vote               = ($request->getGet('vote') === 'up') ? 1 : -1;

            $authorid           = $comm->getAnswerCommentsAuthor($answercommentid);

            $votedata = array();
            $votedata = [
                "articleid"         => $articleid,
                "answerid"          => $answerid,
                "answercommentid"   => $answercommentid,
                "authorid"          => $authorid,
                "voterid"           => $voterid,
                "vote"              => $vote,
            ];

            if ($comm->notEmpty($votedata)) {
                $comm->voteAnswerComment($votedata);
            }

            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }

    public function cancelAnswerCommentVoteProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $answercommentid   = ($request->getGet('answercommentid') != null) ? htmlentities($request->getGet('answercommentid')) : null;
            $voterid            = $session->get("userid");

            $votedata = array();
            $votedata = [
                "answercommentid"  => $answercommentid,
                "voterid"           => $voterid,
            ];



            if ($comm->notEmpty($votedata)) {
                $comm->cancelVoteAnswerComment($votedata);
            }

            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }


    public function cancelArticleVoteProcess($articleid)
    {
        $response   = $this->di->get("response");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {

            $voterid            = $session->get("userid");

            $votedata = array();
            $votedata = [
                "articleid"     => $articleid,
                "voterid"       => $voterid,
            ];

            $comm->cancelVoteArticle($votedata);
            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }


    public function cancelAnswerVoteProcess($articleid)
    {
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $session    = $this->di->get("session");
        $comm       = $this->di->get("comm");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            $answerid           = ($request->getGet('answerid') != null) ? htmlentities($request->getGet('answerid')) : null;
            $voterid            = $session->get("userid");

            $votedata = array();
            $votedata = [
                "answerid"      => $answerid,
                "voterid"       => $voterid,
            ];

            if ($comm->notEmpty($votedata)) {
                $comm->cancelVoteAnswer($votedata);
            }

            $response->redirect("commentary/article/".$articleid);

        } else {
            $response->redirect("login");
        }
    }


    /**
     * Add comment to page
     *
     * @return void
     */
    // public function addComment()
    // {
    //     if ($this->checkUserRole()) {
    //         if (null !== $this->di->get("request")->getPost("commentbtn")) {
    //             $commentOn = $this->di->get("request")->getPost("article");
    //             $comment = $this->di->get("request")->getPost("comment");
    //             $username = $this->di->get("request")->getPost("username");
    //             $email = $this->di->get("request")->getPost("email");
    //             $path = $this->di->get("request")->getGet("path");
    //             // Kontroll om textarean är tom innan den läggs till.
    //             if (strlen(trim($comment))) {
    //                 $this->di->get("comm")->addComment($commentOn, $username, $email, $comment);
    //             }
    //         } elseif (null !== $this->di->get("request")->getPost("resetdbbtn")) {
    //             $this->di->get("comm")->resetComment();
    //         }
    //         // $this->commentarypage();
    //         $this->di->get("response")->redirect("article/".$path);
    //     } else {
    //         $this->di->get("response")->redirect("login");
    //     }
    // }

    /**
     * Edit comment
     *
     * @return void
     */
    public function editComment()
    {
        $view       = $this->di->get("view");
        if (null !== $this->di->get("request")->getGet("id")) {
            $path = $this->di->get("request")->getGet("path");
            $id = $this->di->get("request")->getGet("id");
            $res = $this->di->get("comm")->editCommentLoad($id);

            $data = [
                "comment" => $res[0]->comm,
                "email" => $res[0]->email,
                "id" => $res[0]->id,
                "path" => $path
            ];

            $view->add("commentary/editcomment", $data);
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
        } elseif (null !== $this->di->get("request")->getPost("deletecommentbtn")) {
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
        // Om användaren stämmer med vad som skickas. Så att ingen annan via url kan 'Gilla' kommentar som annan user.
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
        $view->add("admin/admincontent", $data);
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
     * Handler with form to update a article.
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
            "form"  => $form->getHTML(),
            "id"    => $id
        ];

        $view->add("commentary/updatearticle", $data);

        $pageRender->renderPage(["title" => $title]);
    }

    /**
     * Handler with form to update an answer.
     *
     * @return void
     */
    public function updateAnswer($articleid)
    {
        $title      = "Redigera innehåll | Maaa16";
        $view       = $this->di->get("view");
        $pageRender = $this->di->get("pageRender");
        $response   = $this->di->get("response");
        $request    = $this->di->get("request");
        $artFact    = $this->di->get("articleFactory");

        //---------------------------------------------------------

        if ($this->checkUserRole()) {
            //-- return ["article" => dbobj, "aritcledata" => markdown filtered data] --//
            $article        = $artFact->getArticle($articleid);
            $answerid       = ($request->getGet('answerid') != null) ? htmlentities($request->getGet('answerid')) : null;

            $form           = new UpdateAnswerForm($this->di, $answerid, $articleid);
            $form->check();

            $data   = array();
            $data   = [
                "article"   => $article,
                "form"      => $form->getHTML(),
            ];

            $view->add("commentary/updateanswer", $data);
            $pageRender->renderPage(["title" => $title]);

        } else {
            $response->redirect("login");
        }
    }

    /**
     * Get to page with all tags.
     *
     * @return void
     */
    public function tagsPage()
    {
        $title          = "Taggar | Allt om jakt";
        $view           = $this->di->get("view");
        $pageRender     = $this->di->get("pageRender");
        $comm           = $this->di->get("comm");

        //---------------------------------------------------

        $alltags        = $comm->getTags();
        $tagbar         = $comm->getTagBar('5');
        $tagcloud       = $comm->getTagCloud();

        $data = [
            "alltags"       => $alltags,
            "tagbar"        => $tagbar,
            "tagcloud"      => $tagcloud,
        ];

        $view->add("commentary/tags", $data);
        $pageRender->renderPage(["title" => $title]);
    }



    public function checkUserRole()
    {
        return ($this->di->get("session")->get("role") == "user" || $this->di->get("session")->get("role") == "admin") ? true : false;
    }
}
