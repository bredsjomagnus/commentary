<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class ArticleCommentVotes extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RVIXarticlecommentvotes";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $articleid;
    public $articlecommentid;
    public $authorid;
    public $voterid;
    public $vote;
    public $created;
    public $updated;
    public $deleted;
}
