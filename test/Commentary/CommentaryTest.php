<?php
namespace Maaa16\Commentary;

/**
 * Test cases for class Textfilter.
 */
class CommentaryTest extends \PHPUnit_Framework_TestCase
{
    // /**
    //  * Test case Commentary
    //  */
    public function testGetArticle()
    {
        // DI
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        // Commentary
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        $artFact = new \Maaa16\Commentary\ArticleFactory();
        $artFact->setDI($di);



        // Get comments before change
        $res = $artFact->getArticle(1);


        // Kollar att differensen mellan antalet är 1
        $this->assertEquals($res['article']->title, 'testarticletitle');
    }

    public function testSlugify()
    {
        $di         = new \Anax\DI\DIFactoryConfig("di.php");
        $artfact    = $di->get("articleFactory");

        //-------------------------------------------------------

        $inputslug   = "åäö åäö";
        // $inputslugtwo   = "åäö åäö"
        // $outputslugone  = "aao aao";
        // $outputslugtwo  = "aao aao2";

        $outputslugone = $artfact->slugify($inputslug);
        // $outputslugtwo = $artfact->slugify($inputslug);

        $this->assertEquals($outputslugone, "aaoaao");
        // $this->assertEquals($outputslugtwo, "aaoaao2");
    }

    public function testSlugifyUTF8()
    {
        $di         = new \Anax\DI\DIFactoryConfig("di.php");
        $artfact    = $di->get("articleFactory");

        //-------------------------------------------------------

        $inputslug   = "åäö åäö- ";
        // $inputslugtwo   = "åäö åäö"
        // $outputslugone  = "aao aao";
        // $outputslugtwo  = "aao aao2";

        $outputslugone = $artfact->slugifytagnameUTF8($inputslug);
        // $outputslugtwo = $artfact->slugify($inputslug);

        $this->assertEquals($outputslugone, "åäöåäö");
        // $this->assertEquals($outputslugtwo, "aaoaao2");
    }

    public function testTagExists()
    {
        $di         = new \Anax\DI\DIFactoryConfig("di.php");
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        //-------------------------------------------------------

        $tag = "töst";
        // $inputslugtwo   = "åäö åäö"
        // $outputslugone  = "aao aao";
        // $outputslugtwo  = "aao aao2";

        // $comm->tagExists($tag);
        // $outputslugtwo = $artfact->slugify($inputslug);
        $true = $comm->tagExists($tag);
        $this->assertTrue($true);
        // $this->assertEquals($outputslugtwo, "aaoaao2");
    }

    // public function testAddToExistingTag()
    // {
    //     $di         = new \Anax\DI\DIFactoryConfig("di.php");
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     //-------------------------------------------------------
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $sql = "SELECT tagcount FROM RVIXtags WHERE tag = 'töst'";
    //     $resbefore = $db->executeFetchAll($sql);
    //
    //     $tag = "töst";
    //     $comm->addToExistingTag($tag);
    //
    //     $sql = "SELECT tagcount FROM RVIXtags WHERE tag = 'töst'";
    //     $resafter = $db->executeFetchAll($sql);
    //
    //     $diff = intval($resafter[0]->tagcount) - intval($resbefore[0]->tagcount);
    //
    //     $this->assertEquals($diff, 1);
    //     // $this->assertEquals($outputslugtwo, "aaoaao2");
    // }

    // public function testEditCommentLoad()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     // Get comments before change
    //     $res = $comm->editCommentLoad(1);
    //     $numberrows = count($res);
    //
    //     // Kollar att differensen mellan antalet är 1
    //     $this->assertEquals($numberrows, 1);
    // }
    //
    // public function testEditCommentSave()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     // Get comments before change
    //     $res = $comm->editCommentSave(1, 'ny testkommentar');
    //
    //     $sql = "SELECT * FROM ramverk1comments WHERE id = 1";
    //     $res = $db->executeFetchAll($sql);
    //
    //     $testcomment = $res[0]->comm;
    //     // Kollar att differensen mellan antalet är 1
    //     $this->assertEquals($testcomment, 'ny testkommentar');
    // }
    //
    // public function testAddLike()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $comm->addLike(1, 2);
    //
    //     $sql = "SELECT * FROM ramverk1comments WHERE id = 2";
    //
    //     $res = $db->executeFetchAll($sql);
    //     $likedby = $res[0]->likes;
    //     $likedby = explode(",", $likedby);
    //     $likedvalid = in_array('1', $likedby);
    //
    //     $this->assertEquals($likedvalid, true);
    // }
    //
    // public function testGetLikersUsernames()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $usernames = $comm->getLikersUsernames([1]);
    //     $usernamesarray = explode(", ", $usernames);
    //
    //     $likeuservalid = in_array('testuser', $usernamesarray);
    //
    //     $this->assertEquals($likeuservalid, true);
    // }
    //
    // public function testDeleteComment()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $sql = "SELECT COUNT(*) AS numbrows, id FROM ramverk1comments";
    //     $res = $db->executeFetchAll($sql);
    //     $numbrows = $res[0]->numbrows;
    //     $lastid = $res[0]->id;
    //
    //     $comm->deleteComment($lastid);
    //
    //     $sql = "SELECT COUNT(*) AS numbrows FROM ramverk1comments";
    //     $res = $db->executeFetchAll($sql);
    //     $newnumberrows = $res[0]->numbrows;
    //
    //     $diffnumber = intval($numbrows) - intval($newnumberrows);
    //
    //     $this->assertEquals($diffnumber, 1);
    // }
    //
    // public function testGetComments()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $res = $comm->getComments(1);
    //
    //     $username = $res[0]->username;
    //
    //     $this->assertEquals($username, 'user');
    // }
    //
    // public function testAssemble()
    // {
    //     $di  = new \Anax\DI\DIFactoryConfig("di.php");
    //
    //     // Commentary
    //     $comm = new \Maaa16\Commentary\Commentary();
    //     $comm->setDI($di);
    //
    //     $commAssembler = new \Maaa16\Commentary\CommAssembler();
    //     $commAssembler->setDI($di);
    //
    //     $db = new \Anax\Database\DatabaseQueryBuilder();
    //     $db->configure("databaseconfig.php");
    //     $db->connect();
    //
    //     $comments = $comm->getComment();
    //
    //     $table = $commAssembler->assemble($comments);
    //
    //     $this->assertContains("<table class='commenttable'>", $table);
    // }
    //
    public function testGetForm()
    {
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        $commAssembler = new \Maaa16\Commentary\CommAssembler();
        $commAssembler->setDI($di);

        $form = $commAssembler->getForm(1, 'path');

        $this->assertContains("</form>", $form);
    }
}
