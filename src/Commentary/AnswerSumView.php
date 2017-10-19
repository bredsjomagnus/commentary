<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class AnswerSumView extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RVIXanswerSumView";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $userid;
    public $firstname;
    public $surname;
    public $articleid;
    public $title;
    public $numbanswers;
}
