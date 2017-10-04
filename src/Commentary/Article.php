<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class Article extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RV1content";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $path;
    public $slug;
    public $title;
    public $data;
    public $type;
    public $filter;
    public $status;
    public $published;
    public $created;
    public $updated;
    public $deleted;
}
