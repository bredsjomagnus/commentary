<?php

namespace Maaa16\Commentary;

use \Anax\DI\InjectionAwareInterface;
use \Anax\DI\InjectionAwareTrait;

/**
 * Commentary
 */
class Commentary implements InjectionAwareInterface
{
    use InjectionAwareTrait;



    /**
     * @var array $session inject a reference to the session.
     */
    // private $session;



    /**
     * @var string $key to use when storing in session.
     */
    // const KEY = "commentary";



    /**
     * Inject dependencies.
     *
     * @param array $dependency key/value array with dependencies.
     *
     * @return self
     */
    // public function inject($dependency)
    // {
    //     $this->session = $dependency["session"];
    //     return $this;
    // }



    /**
     * Fill the session with default data that are read from files.
     *
     * @return self
     */
    // public function init()
    // {
    //     $files = $this->config["dataset"];
    //     $dataset = [];
    //     foreach ($files as $file) {
    //         $content = file_get_contents($file);
    //         $key = pathinfo($file, PATHINFO_FILENAME);
    //         $dataset[$key] = json_decode($content, true);
    //     }
    //
    //     $this->session->set(self::KEY, $dataset);
    //     return $this;
    // }



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
}
