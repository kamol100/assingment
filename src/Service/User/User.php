<?php
/**
 * Created by PhpStorm.
 * User: kamol
 * Date: 7/30/2022
 * Time: 6:30 PM
 */

namespace Service\User;

class User
{
    private $id;

    private $type;

    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }
}
