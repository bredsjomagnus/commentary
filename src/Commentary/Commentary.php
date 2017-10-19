<?php

namespace Maaa16\Commentary;

use \Anax\DI\InjectionAwareInterface;
use \Anax\DI\InjectionAwareTrait;
use \Maaa16\Commentary\Article;
use \Maaa16\Commentary\ArticleView;
use \Maaa16\Commentary\ArticleVotes;
use \Maaa16\Commentary\Answer;
use \Maaa16\Commentary\AnswerVotes;
use \Maaa16\Commentary\ArticleCommentVotes;
use \Maaa16\Commentary\AnswerCommentVotes;
use \Maaa16\Commentary\Articlecomment;
use \Maaa16\Commentary\Tags;

/**
 * Commentary
 */
class Commentary implements InjectionAwareInterface
{
    use InjectionAwareTrait;

    /**
    * Get all articles
    */
    public function getArticlesArray($tagpath)
    {
        $db = $this->di->get("db");

        //----------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXarticle WHERE deleted IS NULL";
        $res = $db->executeFetchAll($sql);

        $articlearray = [];
        foreach ($res as $article) {
            $articletagpaths = explode(", ", $article->tagpaths);
            if (in_array($tagpath, $articletagpaths) || $tagpath == 'alla') {
                $articlearray[$article->id] = [
                    "id"        => $article->id,
                    "user"      => $article->user,
                    "title"     => $article->title,
                    "tags"      => $article->tags,
                    "tagpaths"  => $article->tagpaths,
                    "created"   => $article->created,
                ];
            }
        }

        return $articlearray;
    }

    public function getTag($tagpath)
    {
        $db = $this->di->get("db");

        $db->connect();
        $sql = "SELECT tag FROM RVIXtags WHERE tagpath = ?";
        $params = [$tagpath];
        $res = $db->executeFetchAll($sql, $params);

        return $res[0]->tag;
    }

    /**
    * Get articleview
    */
    public function getArticleView($id)
    {
        $db = $this->di->get("db");

        //----------------------------

        $db->connect();

        $sql = "SELECT * FROM RVIXarticleView WHERE userid = ?";
        $res = $db->executeFetchAll($sql, [$id]);

        return $res;
    }
    /**
    * Get answerview
    */
    public function getAnswerView($id)
    {
        $db = $this->di->get("db");

        //----------------------------

        $db->connect();

        $sql = "SELECT * FROM RVIXanswerView WHERE userid = ?";
        $res = $db->executeFetchAll($sql, [$id]);

        return $res;
    }




    /**
    * Add comment
    *
    * @param string $comment
    * @param object $app
    */
    public function addComment($commentOn, $username, $email, $comment)
    {
        $this->di->get("db")->connect();
        $sql = "INSERT INTO ramverk1comments (comment_on, username, email, comm, edited) VALUES (?, ?, ?, ?, ?)";
        $params = [$commentOn, $username, $email, $comment, null];
        $this->di->get("db")->execute($sql, $params);
    }

    /**
    * Get comment from session
    *
    * @param object $app
    */
    public function getComment()
    {
        $this->di->get("db")->connect();
        $sql = "SELECT * FROM ramverk1comments";
        $res = $this->di->get("db")->executeFetchAll($sql);
        return $res;
    }

    /**
    * Reset database comments
    *
    * @param object $app
    */
    public function resetComment()
    {
        $this->di->get("db")->connect();
        $sql = "DROP TABLE IF EXISTS ramverk1comments";
        $this->di->get("db")->execute($sql);
        $sql = "CREATE TABLE IF NOT EXISTS ramverk1comments
                (
                    id INT AUTO_INCREMENT NOT NULL,
                    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    edited TIMESTAMP NULL,
                    username varchar(100) NOT NULL default 'NA',
                    email varchar(200) NOT NULL default 'na@email.com',
                    comm VARCHAR(1000),
                    likes VARCHAR(1000) DEFAULT '',
                    PRIMARY KEY  (id)
                )
                    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

        $this->di->get("db")->execute($sql);
    }

    /**
    * Load comment to edit
    *
    * @param object $app
    */
    public function editCommentLoad($id)
    {
        $this->di->get("db")->connect();
        $sql = "SELECT * FROM ramverk1comments WHERE id = ?";
        $params = [$id];
        $res = $this->di->get("db")->executeFetchAll($sql, $params);
        return $res;
    }

    /**
    * Save edited comment
    *
    * @param object $app
    * @param integer $id
    * @param string $comment
    */
    public function editCommentSave($id, $comment)
    {
        $this->di->get("db")->connect();
        $sql = "UPDATE ramverk1comments SET comm = ?, edited = CURRENT_TIMESTAMP WHERE id = ?";
        $params = [$comment, $id];
        $this->di->get("db")->execute($sql, $params);
    }

    /**
    * Delete one single comment
    *
    * @param object $app
    * @param integer $id
    */
    public function deleteComment($id)
    {
        $this->di->get("db")->connect();
        $sql = "DELETE FROM ramverk1comments WHERE id = ?";
        $params = [$id];
        $this->di->get("db")->execute($sql, $params);
    }

    /**
    * Add like to comment
    *
    * @param object $app
    * @param integer $id
    */
    public function addLike($userid, $commentid)
    {
        $this->di->get("db")->connect();
        $sql = "SELECT likes FROM ramverk1comments WHERE id = ?";
        $params = [$commentid];
        $res = $this->di->get("db")->executeFetchAll($sql, $params);
        $commentlikes = $res[0]->likes;

        $commentlikes .= ",".$userid;
        if ($commentlikes[0] == ",") {
            $commentlikes = substr($commentlikes, 1);
        }
        $sql = "UPDATE ramverk1comments SET likes = ? WHERE id = ?";
        $params = [$commentlikes, $commentid];
        $this->di->get("db")->execute($sql, $params);
    }

    /**
    * Get usernames of those who liked a comment
    *
    * @param object $app
    * @param array $likersid array of idnumbers of users who liked a comment
    *
    * @return string $usernames of names of those who liked a comment. "name1, name2, name3,..."
    */
    public function getLikersUsernames($likersid)
    {
        $usernames = "";
        $this->di->get("db")->connect();
        foreach ($likersid as $id) {
            if ($id != "") {
                $sql = "SELECT username FROM ramverk1accounts WHERE id = ?";
                $params = [$id];
                $res = $this->di->get("db")->executeFetchAll($sql, $params);
                $usernames .= ", " . $res[0]->username;
            }
        }
        $usernames = substr($usernames, 2);
        return $usernames;
    }

    /**
    * Get comment from session
    *
    * @param object $app
    */
    public function getComments($id)
    {
        $this->di->get("db")->connect();
        $sql = "SELECT * FROM ramverk1comments WHERE comment_on = ? ORDER BY created ASC";
        $res = $this->di->get("db")->executeFetchAll($sql, [$id]);
        return $res;
    }

    /**
    * Get comment from session
    *
    * @param object $app
    */
    public function getAnswers($id)
    {
        $db = $this->di->get("db");

        //--------------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXanswer WHERE answerto = ? ORDER BY created ASC";
        $res = $db->executeFetchAll($sql, [$id]);
        return $res;
    }

    /**
    * Get comment for article
    *
    * @param int $id, id of article
    *
    * @return object $res, resulttable from RVIXarticlecomment
    */
    public function getArticleComments($id)
    {
        $db = $this->di->get("db");

        //--------------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXarticlecomment WHERE commentto = ? ORDER BY created ASC";
        $res = $db->executeFetchAll($sql, [$id]);
        return $res;
    }

    /**
    * Get author for articlecomment with id = $articelcommentid
    *
    * @param int $articlecommentid, id of articlecomment
    *
    * @return int $authorid of articlecomment author
    */
    public function getArticleCommentsAuthor($articlecommentid)
    {
        $db = $this->di->get("db");

        //--------------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXarticlecomment WHERE id = ?";
        $res = $db->executeFetchAll($sql, [$articlecommentid]);

        $authorid = $res[0]->user;
        return $authorid;
    }

    /**
    * Get author for answercomment with id = $answercommentid
    *
    * @param int $answercommentid, id of answercomment
    *
    * @return int $authorid of answercomment author
    */
    public function getAnswerCommentsAuthor($answercommentid)
    {
        $db = $this->di->get("db");

        //--------------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXanswercomment WHERE id = ?";
        $res = $db->executeFetchAll($sql, [$answercommentid]);

        $authorid = $res[0]->user;
        return $authorid;
    }

    /**
    * Get comment for answer
    *
    * @param int $id, id of article
    *
    * @return object $res, resulttable from RVIXanswercomment
    */
    public function getAnswerComments($id)
    {
        $db = $this->di->get("db");

        //--------------------------------

        $db->connect();
        $sql = "SELECT * FROM RVIXanswercomment WHERE articleid = ? ORDER BY created ASC";
        $res = $db->executeFetchAll($sql, [$id]);
        return $res;
    }

    public function notEmpty($array)
    {
        $valid = false;

        foreach ($array as $key => $value) {
            $value = trim($value);
            if (!empty($value)) {
                $valid = true;
            }
        }
        return $valid;
    }
    /**
    * Add answer to article
    *
    * @param array $answerdata ['answerto' => id of article, 'user' => userid, 'data' => answer text]
    */
    public function addAnswer($answerdata)
    {
        $db = $this->di->get("db");
        $answer = new Answer();
        $answer->setDb($db);

        $answer->answerto   = $answerdata['answerto'];
        $answer->user       = $answerdata['user'];
        $answer->data       = $answerdata['data'];
        $answer->save();
    }

    /**
    * Add comment to article
    *
    * @param array $answerdata ['answerto' => id of article, 'user' => userid, 'data' => answer text]
    */
    public function addArticleComment($articlecommentdata)
    {
        $db = $this->di->get("db");
        $articlecomment = new Articlecomment();
        $articlecomment->setDb($db);

        $articlecomment->commentto   = $articlecommentdata['commentto'];
        $articlecomment->user        = $articlecommentdata['user'];
        $articlecomment->data        = $articlecommentdata['data'];
        $articlecomment->save();
    }

    /**
    * Add comment to answer
    *
    * @param array $answerdata ['answerto' => id of article, 'user' => userid, 'data' => answer text]
    */
    public function addAnswerComment($answercommentdata)
    {
        $db             = $this->di->get("db");
        $answercomment  = new Answercomment();
        $answercomment->setDb($db);

        $answercomment->articleid   = $answercommentdata['articleid'];
        $answercomment->commentto   = $answercommentdata['commentto'];
        $answercomment->user        = $answercommentdata['user'];
        $answercomment->data        = $answercommentdata['data'];
        $answercomment->save();
    }


    /**
    * Starts with collecting doubles taking those tags out of the array.
    * Then sending all brand new ones to be added to database.
    *
    * @param array $newtags ['aao' => åäö, 'aao2' => åäö2, ...]
    */
    // public function collectTags($newtags, $articleid)
    // {
    //     $db         = $this->di->get("db");
    //     $session    = $this->di->get("session");
    //     $tags   = new Tags();
    //     $tagpaths = [];
    //     $session->set("newtagsbefore", $newtags);
    //     $tags->setDb($db);
    //     foreach($tags->findAll() as $oldtag) {
    //         if(in_array($oldtag->tag, $newtags)) { // kollar value som tillåter åäö
    //             $oldtag->setDb($db);
    //             $oldtag->tagcount = $oldtag->tagcount + 1;
    //             $newtags = array_diff($newtags, [$oldtag->tag]); // diff med associativ array
    //
    //             $tagpaths[] = $oldtag->tagpath;
    //
    //             $oldtag->save();
    //         }
    //     }
    //     $session->set("newtagsafter", $newtags);
    //     $this->addNewTags($newtags, $tagpaths, $articleid);
    // }

    /**
    * Getting brand new tags to be added to database.
    *
    * @param array $tags [tag1, tag2, ...]
    */
    // public function addNewTags($newtags, $tagpaths, $articleid)
    // {
    //     $article = new Article();
    //     $db = $this->di->get("db");
    //
    //     foreach($newtags as $tagpath => $tagname) {
    //         $tag = new Tags();
    //         $tag->setDb($db);
    //         $tag->tag = $tagname;
    //         $tag->tagpath = $tagpath;
    //         $tag->tagcount = 1;
    //
    //         $tagpaths[] = $tagpath;
    //
    //         $tag->save();
    //     }
    //     $article->setDb($db);
    //     $article->find("id", $articleid);
    //     $article->tagpaths = implode(", ", $tagpaths);
    //     $article->save();
    // }

    public function tagExists($tag)
    {
        $db     = $this->di->get("db");

        $db->connect();

        $sql = "SELECT * FROM RVIXtags WHERE tag = ?";
        $params = [$tag];
        $res = $db->executeFetchAll($sql, $params);

        $exists = (count($res) > 0) ? true : false;

        return $exists;
    }

    public function addToExistingTag($tag)
    {
        $db     = $this->di->get("db");
        $db->connect();

        $sql = "UPDATE RVIXtags SET tagcount = tagcount + 1 WHERE tag = ?";
        $params = [$tag];
        $db->execute($sql, $params);
    }

    public function getTagPath($tag)
    {
        $db     = $this->di->get("db");
        $db->connect();

        $sql = "SELECT * FROM RVIXtags WHERE BINARY tag = BINARY ?";
        $params = [$tag];
        $res = $db->executeFetchAll($sql, $params);

        $this->di->get("session")->set("gettagpath", $res);

        return $res[0]->tagpath;
    }

    public function addNewTag($tag, $tagpath)
    {
        $db     = $this->di->get("db");
        $tags   = new Tags();
        $tags->setDb($db);

        $tags->tag = $tag;
        $tags->tagpath = $tagpath;
        $tags->tagcount = 1;
        $tags->save();
    }

    /**
    * Get tags by tagcount.
    *
    * @param string $limitar, if set will retiev tags with limit.
    *
    * @return object $tags, dbtableresult
    */
    public function getTags($limiter = 'nolimit')
    {
        $db = $this->di->get("db");

        //----------------------------------------------

        $db->connect();
        if ($limiter == 'nolimit') {
            $limit = "";
        } else {
            $limit = "LIMIT ".intval($limiter);
        }

        $sql = "SELECT * FROM RVIXtags ORDER BY tagcount DESC, tag ".$limit;
        $res = $db->executeFetchAll($sql);

        return $res;
    }

    /**
    * Get tagbar.
    *
    * @return string $tagbar
    */
    public function getTagBar($limiter = 'nolimit')
    {
        $url        = $this->di->get("url");
        $tags       = $this->getTags($limiter);

        $tagbar     = "<div class='btn-group' role='group' aria-label='...'>
                        <span class='small'>Se: </span><a class='tags'
                        href='".$url->create('commentary/articles/alla')."'>Alla</a> -
                        <span class='small'>Populära taggar: </span>";
        foreach ($tags as $tag) {
            $tagbar .=      "<a class='tags' href=
                            '".$url->create('commentary/articles/'.$tag->tagpath)."'>".$tag->tag."</a>&nbsp;";
        }
        $tagbar     .= "</div>";

        return $tagbar;
    }



    /**
    * Delete tags.
    *
    * @param array $trashtags, [tag1, tag2,...]
    */
    public function deleteTags($trashtags)
    {
        $db = $this->di->get("db");

        $tags = new Tags();
        $tags->setDb($db);

        foreach ($tags->findAll() as $tag) {
            if (in_array($tag->tag, $trashtags)) {
                if ($tag->tagcount > 1) {
                    $tag->setDb($db);
                    $tag->tagcount = $tag->tagcount - 1;
                    $tag->save();
                } else {
                    $tag->setDb($db);
                    $tag->delete();
                }
            }
        }
    }

    /**
    * Generate tags in different sizes, depending on popularity
    *
    * @return string $tagcloud
    */
    public function getTagCloud()
    {

        $url    = $this->di->get("url");
        $db     = $this->di->get("db");
        $db->connect();

        $sql = "SELECT SUM(tagcount) AS totaltagcount FROM RVIXtags";
        $res = $db->executeFetchAll($sql);
        $totalnumbertags =  $res[0]->totaltagcount;

        $sql = "SELECT * FROM RVIXtags ORDER BY tag";
        $res = $db->executeFetchAll($sql);

        $newline = rand(1, 2);
        $tagCloud = "";
        $counter = 1;
        foreach ($res as $row) {
            // sizeclass
            $percent = floor($row->tagcount/$totalnumbertags*100);

            if ($percent < 10) {
                $sizeclass = 'sizetag1';
            } elseif ($percent >= 10 and $percent < 20) {
                $sizeclass = 'sizetag2';
            } elseif ($percent >= 20 and $percent < 30) {
                $sizeclass = 'sizetag3';
            } elseif ($percent >= 30 and $percent < 40) {
                $sizeclass = 'sizetag4';
            } elseif ($percent >= 40 and $percent < 50) {
                $sizeclass = 'sizetag5';
            } elseif ($percent >= 50 and $percent < 60) {
                $sizeclass = 'sizetag6';
            } elseif ($percent >= 60 and $percent < 70) {
                $sizeclass = 'sizetag7';
            } elseif ($percent >= 70 and $percent < 80) {
                $sizeclass = 'sizetag8';
            } elseif ($percent >= 80 and $percent < 90) {
                $sizeclass = 'sizetag9';
            } else {
                $sizeclass = 'sizetag10';
            }

            // rotationclass not in use
            // $rotrand = rand(0, 1);
            // if ($rotrand == 0) {
            //     $rotateclass = 'norotatetag';
            // } else {
            //     $rotateclass = 'rotatetag';
            // }

            $tagCloud .="<a href='".$url->create('commentary/articles/'.$row->tagpath)."'>
            <div class='tagcloud tagstyle ".$sizeclass."'>".$row->tag."</div></a> ";


            if ($counter % $newline == 0) {
                $tagCloud .= "<br />";
                $newline = rand(3, 4);
                $counter = 1;
            }
            $counter = $counter + 1;
        }

        return $tagCloud;
    }

    /**
    * Vote an article
    *
    * @param array $votedata - ['articleid' => articleid, 'authorid' => authorid, 'voterid' => voterid, 'vote' => vote]
    */
    public function voteArticle($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $articlevote = new ArticleVotes();
        $articlevote->setDb($db);

        $articlevote->articleid = $votedata['articleid'];
        $articlevote->authorid  = $votedata['authorid'];
        $articlevote->voterid   = $votedata['voterid'];
        $articlevote->vote      = intval($votedata['vote']);
        $articlevote->save();
    }

    /**
    * Vote an articlecomment
    *
    * @param array $votedata - ['articleid' => articleid, 'articlecommentid',
    * 'authorid' => authorid, 'voterid' => voterid, 'vote' => vote]
    */
    public function voteArticleComment($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $articlecommentvote = new ArticleCommentVotes();
        $articlecommentvote->setDb($db);

        $articlecommentvote->articleid         = $votedata['articleid'];
        $articlecommentvote->articlecommentid  = $votedata['articlecommentid'];
        $articlecommentvote->authorid          = $votedata['authorid'];
        $articlecommentvote->voterid           = $votedata['voterid'];
        $articlecommentvote->vote              = intval($votedata['vote']);
        $articlecommentvote->save();
    }

    /**
    * Vote an articlecomment
    *
    * @param array $votedata - ['articleid' => articleid, 'answerid' => answerid,
    * 'answercommentid' => answercommentid, 'authorid' => authorid, 'voterid' => voterid, 'vote' => vote]
    */
    public function voteAnswerComment($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $answercommentvote = new AnswerCommentVotes();
        $answercommentvote->setDb($db);

        $answercommentvote->articleid           = $votedata['articleid'];
        $answercommentvote->answerid            = $votedata['answerid'];
        $answercommentvote->answercommentid     = $votedata['answercommentid'];
        $answercommentvote->authorid            = $votedata['authorid'];
        $answercommentvote->voterid             = $votedata['voterid'];
        $answercommentvote->vote                = intval($votedata['vote']);
        $answercommentvote->save();
    }

    /**
    * Vote an article
    *
    * @param array $votedata - ['articleid' => articleid, 'answerid' => answerid,
    * 'authorid' => authorid, 'voterid' => voterid, 'vote' => vote]
    */
    public function voteAnswer($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $answervote = new AnswerVotes();
        $answervote->setDb($db);

        $answervote->articleid  = intval($votedata['articleid']);
        $answervote->answerid   = intval($votedata['answerid']);
        $answervote->authorid   = intval($votedata['authorid']);
        $answervote->voterid    = intval($votedata['voterid']);
        $answervote->vote       = intval($votedata['vote']);
        $answervote->save();
    }

    /**
    * Get total number of votes on article
    *
    * @param int $articleid - id of the current article
    *
    * @return int $totsumarticlevotes
    */
    public function getTotNumbOfAricleVotes($articleid)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "SELECT COUNT(*) AS totnumb FROM RVIXarticlevotes WHERE articleid = ?";
        $param = [$articleid];
        $res = $db->executeFetchAll($sql, $param);

        return $res[0]->totnumb;
    }

    /**
    * Get total number of votes on answer
    *
    * @param int $answerid - id of the current answer
    *
    * @return int $totsumarticlevotes
    */
    public function getTotNumbOfAnswerVotes($answerid)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "SELECT COUNT(*) AS totnumb FROM RVIXanswervotes WHERE answerid = ?";
        $param = [$answerid];
        $res = $db->executeFetchAll($sql, $param);

        return $res[0]->totnumb;
    }



    /**
    * Cancel vote on article
    *
    * @param array $votedata - ['articleid' => articleid, 'voterid' => voterid]
    */
    public function cancelVoteArticle($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "DELETE FROM RVIXarticlevotes WHERE articleid = ? AND voterid = ?";
        $params = [$votedata['articleid'], $votedata['voterid']];
        $db->execute($sql, $params);
    }

    /**
    * Cancel vote on articlecomment
    *
    * @param array $votedata - ['articlcommenteid' => articlcommenteid, 'voterid' => voterid]
    */
    public function cancelVoteArticleComment($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "DELETE FROM RVIXarticlecommentvotes WHERE articlecommentid = ? AND voterid = ?";
        $params = [$votedata['articlecommentid'], $votedata['voterid']];
        $db->execute($sql, $params);
    }

    /**
    * Cancel vote on answercomment
    *
    * @param array $votedata - ['answercommenteid' => answercommenteid, 'voterid' => voterid]
    */
    public function cancelVoteAnswerComment($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "DELETE FROM RVIXanswercommentvotes WHERE answercommentid = ? AND voterid = ?";
        $params = [$votedata['answercommentid'], $votedata['voterid']];
        $db->execute($sql, $params);
    }

    /**
    * Cancel vote on answer
    *
    * @param array $votedata - ['answerid' => answerid, 'voterid' => voterid]
    */
    public function cancelVoteAnswer($votedata)
    {
        $db = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql = "DELETE FROM RVIXanswervotes WHERE answerid = ? AND voterid = ?";
        $params = [$votedata['answerid'], $votedata['voterid']];
        $db->execute($sql, $params);
    }

    /**
    * Get votes on this article
    *
    * @param int $articleid - id of the current article
    *
    * @return object $res - resulttable of all votes on article with id = articleid
    */
    public function getArticlevotes($articleid)
    {
        $db = $this->di->get("db");
        $db->connect();

        //----------------------------------

        $sql = "SELECT * FROM RVIXarticlevotes WHERE articleid = ?";
        $param = [$articleid];
        $res = $db->executeFetchAll($sql, $param);

        return $res;
    }

    /**
    * Get votes on this article
    *
    * @param int $articleid - id of the current article
    *
    * @return object $res - resulttable of all votes on answers on article with id = articleid
    */
    public function getAnswervotes($articleid)
    {
        $db = $this->di->get("db");
        $db->connect();

        //----------------------------------

        $sql = "SELECT * FROM RVIXanswervotes WHERE articleid = ?";
        $param = [$articleid];
        $res = $db->executeFetchAll($sql, $param);

        return $res;
    }

    /**
    * Check if user is author to article with id = $articleid.
    *
    * @param int $articleid - id of the current article
    *
    * @return boolean $ownarticle - true if owner, false otherwise
    */
    public function ownArticle($articleid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXarticle WHERE id = ? AND user = ?";
        $param  = [$articleid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------


        $ownarticle = false;
        if (!empty($res)) {
            $ownarticle = true;
        }

        return $ownarticle;
    }

    /**
    * Check if user is author to articlecomment with id = $articlecommentid.
    *
    * @param int $articlecommentid - id of the current articlecomment
    *
    * @return boolean $ownarticlecomment - true if owner, false otherwise
    */
    public function ownArticleComment($articlecommentid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXarticlecomment WHERE id = ? AND user = ?";
        $param  = [$articlecommentid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------


        $ownarticlecomment = false;
        if (!empty($res)) {
            $ownarticlecomment = true;
        }

        return $ownarticlecomment;
    }

    /**
    * Check if user is author to answer with id = $answerid.
    *
    * @param int $answerid - id of the current answer
    *
    * @return boolean $ownanswer - true if owner, false otherwise
    */
    public function ownAnswer($answerid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXanswer WHERE id = ? AND user = ?";
        $param  = [$answerid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------


        $ownanswer = false;
        if (!empty($res)) {
            $ownanswer = true;
        }

        return $ownanswer;
    }

    /**
    * Check if user is author to answercomment with id = $answercommentid.
    *
    * @param int $answercommentid - id of the current answercomment
    *
    * @return boolean $ownanswercomment - true if owner, false otherwise
    */
    public function ownAnswerComment($answercommentid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXanswercomment WHERE id = ? AND user = ?";
        $param  = [$answercommentid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------


        $ownanswercomment = false;
        if (!empty($res)) {
            $ownanswercomment = true;
        }

        return $ownanswercomment;
    }
// '&aring;&auml;&ouml; &Aring;&Auml;&Ouml;
    /**
    * Check if user has voted on article with id = articleid
    *
    * @param int $articleid - id of the current article
    *
    * @return boolean $hasVoted - true if voted, false otherwise
    */
    public function userHasVotedOnArticle($articleid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXarticlevotes WHERE articleid = ? AND voterid = ?";
        $param  = [$articleid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $hasVoted = false;
        if (count($res) > 0) {
            $hasVoted = true;
        }
        return $hasVoted;
    }

    /**
    * Check if user has voted on articlecommment with id = articlecommentid
    *
    * @param int $articlecommentid - id of the current articlecomment
    *
    * @return boolean $hasVoted - true if voted, false otherwise
    */
    public function userHasVotedOnArticlecomment($articlecommentid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXarticlecommentvotes WHERE articlecommentid = ? AND voterid = ?";
        $param  = [$articlecommentid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $hasVoted = false;
        if (count($res) > 0) {
            $hasVoted = true;
        }
        return $hasVoted;
    }

    /**
    * Check if user has voted on answer with id = answerid
    *
    * @param int $answerid - id of the current answer
    *
    * @return boolean $hasVoted - true if voted, false otherwise
    */
    public function userHasVotedOnAnswer($answerid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXanswervotes WHERE answerid = ? AND voterid = ?";
        $param  = [$answerid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $hasVoted = false;
        if (count($res) > 0) {
            $hasVoted = true;
        }
        return $hasVoted;
    }

    /**
    * Check if user has voted on answercommment with id = answercommentid
    *
    * @param int $answercommentid - id of the current answercomment
    *
    * @return boolean $hasVoted - true if voted, false otherwise
    */
    public function userHasVotedOnAnswerComment($answercommentid)
    {
        $session    = $this->di->get("session");
        $db         = $this->di->get("db");

        //----------------------------------

        $userid     = $session->get("userid");

        $db->connect();
        $sql    = "SELECT * FROM RVIXanswercommentvotes WHERE answercommentid = ? AND voterid = ?";
        $param  = [$answercommentid, $userid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $hasVoted = false;
        if (count($res) > 0) {
            $hasVoted = true;
        }
        return $hasVoted;
    }

    /**
    * Sum up and return total vote on article with id = $articleid
    *
    * @param int $articleid - id of the current article
    *
    * @return int $sumvotes - sum of article votes
    */
    public function getArticleVoteSum($articleid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT SUM(vote) AS votesum FROM RVIXarticlevotes WHERE articleid = ?";
        $param  = [$articleid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $votesum = $res[0]->votesum;

        return $votesum;
    }

    /**
    * Sum up and return total vote on articlecomment with id = $articlecommentid
    *
    * @param int $articlecommentid - id of the current article
    *
    * @return int $sumvotes - sum of article votes
    */
    public function getArticleCommentVoteSum($articlecommentid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT SUM(vote) AS votesum FROM RVIXarticlecommentvotes WHERE articlecommentid = ?";
        $param  = [$articlecommentid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $votesum = $res[0]->votesum;

        return $votesum;
    }

    /**
    * Sum up and return total vote on answer with id = $answerid
    *
    * @param int $answerid - id of the current answer
    *
    * @return int $sumvotes - sum of answer votes
    */
    public function getAnswerVoteSum($answerid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT SUM(vote) AS votesum FROM RVIXanswervotes WHERE answerid = ?";
        $param  = [$answerid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $votesum = $res[0]->votesum;

        return $votesum;
    }

    /**
    * Count number of answercomments on answer with id = answerid
    *
    * @param int $answerid - id of the current answer
    *
    * @return int $numbcomments - number of answercomments
    */
    public function getTotNumbOfAnswerComments($answerid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT count(*) AS numbcomments FROM RVIXanswercomment WHERE commentto = ?";
        $param  = [$answerid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $numbcomments = $res[0]->numbcomments;

        return $numbcomments;
    }

    /**
    * Count number of articlecomments on article with id = articleid
    *
    * @param int $articleid - id of the current article
    *
    * @return int $numbcomments - number of articlecomments
    */
    public function getTotNumbOfArticleComments($articleid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT count(*) AS numbcomments FROM RVIXarticlecomment WHERE commentto = ?";
        $param  = [$articleid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $numbcomments = $res[0]->numbcomments;

        return $numbcomments;
    }

    /**
    * Sum up and return total vote on articlecomment with id = $articlecommentid
    *
    * @param int $articlecommentid - id of the current article
    *
    * @return int $sumvotes - sum of article votes
    */
    public function getAnswerCommentVoteSum($answercommentid)
    {
        $db         = $this->di->get("db");

        //----------------------------------

        $db->connect();
        $sql    = "SELECT SUM(vote) AS votesum FROM RVIXanswercommentvotes WHERE answercommentid = ?";
        $param  = [$answercommentid];
        $res    = $db->executeFetchAll($sql, $param);

        //----------------------------------

        $votesum = $res[0]->votesum;

        return $votesum;
    }

    public function utf8Filter($text)
    {
        $text = preg_replace(
            ['/&aring;{1}/', '/&auml;{1}/', '/&ouml;{1}/', '/&Aring;{1}/', '/&Auml;{1}/', '/&Ouml;{1}/'],
            ['å', 'ä', 'ö', 'Å', 'Ä', 'Ö'],
            $text
        );

        return $text;
    }
}
