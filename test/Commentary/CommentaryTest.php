<?php
namespace Maaa16\Commentary;

/**
 * Test cases for class Textfilter.
 */
class CommentaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test case Commentary
     */
    public function testCommInsert()
    {
        // DI
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        // Commentary
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        // Get comments before change
        $resbefore = $comm->getComment();
        $numberbefore = count($resbefore);

        // insert one comment
        $commentOn = 1;
        $username = 'user';
        $email = 'user@email.com';
        $comment = 'En kommentar';
        $comm->addComment($commentOn, $username, $email, $comment);

        // Get comments before change
        $resafter = $comm->getComment();
        $numberafter = count($resafter);

        // Differense numberafter - numberbefore
        $diffnumber = intval($numberafter) - intval($numberbefore);

        // Kollar att differensen mellan antalet är 1
        $this->assertEquals($diffnumber, 1);
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

    public function testEditCommentLoad()
    {
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        // Commentary
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        // Get comments before change
        $res = $comm->editCommentLoad(1);
        $numberrows = count($res);

        // Kollar att differensen mellan antalet är 1
        $this->assertEquals($numberrows, 1);
    }

    public function testEditCommentSave()
    {
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        // Commentary
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        $db = new \Anax\Database\DatabaseQueryBuilder();
        $db->configure("databaseconfig.php");
        $db->connect();

        // Get comments before change
        $res = $comm->editCommentSave(1, 'ny testkommentar');

        $sql = "SELECT * FROM ramverk1comments WHERE id = 1";
        $res = $db->executeFetchAll($sql);

        $testcomment = $res[0]->comm;
        // Kollar att differensen mellan antalet är 1
        $this->assertEquals($testcomment, 'ny testkommentar');
    }

    public function testAddLike()
    {
        $di  = new \Anax\DI\DIFactoryConfig("di.php");

        // Commentary
        $comm = new \Maaa16\Commentary\Commentary();
        $comm->setDI($di);

        $db = new \Anax\Database\DatabaseQueryBuilder();
        $db->configure("databaseconfig.php");
        $db->connect();

        $comm->addLike(1, 2);

        $sql = "SELECT * FROM ramverk1comments WHERE id = 2";

        $res = $db->executeFetchAll($sql);
        $likedby = $res[0]->likes;
        $likedby = explode(",", $likedby);
        $likedvalid = in_array('1', $likedby);

        $this->assertEquals($likedvalid, true);
    }
}
