<?php

namespace Maaa16\Commentary;

use \Anax\Database\ActiveRecordModel;

/**
 * A database driven model.
 */
class Articlecomment extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "RVIXarticlecomment";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $commentto;
    public $user;
    public $data;
    public $created;
    public $updated;
    public $deleted;
}
