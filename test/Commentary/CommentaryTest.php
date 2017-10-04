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
}
