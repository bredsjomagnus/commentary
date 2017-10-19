<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class Tags extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RVIXtags";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $tag;
    public $tagcount;
    public $created;
    public $updated;
    public $deleted;
}
