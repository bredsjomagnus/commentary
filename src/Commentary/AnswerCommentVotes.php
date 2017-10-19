<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class AnswerCommentVotes extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RVIXanswercommentvotes";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $articleid;
    public $answerid;
    public $answercommentid;
    public $authorid;
    public $voterid;
    public $vote;
    public $created;
    public $updated;
    public $deleted;
}
