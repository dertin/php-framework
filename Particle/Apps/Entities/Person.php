<?php

namespace Particle\Apps\Entities;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

class Person extends \Spot\Entity
{
    protected static $table = 'person';
    protected static $mapper = 'Mapper\Person';

    public static function fields()
    {
        return [
            'PersonId'      => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'PersonMail'    => ['type' => 'string', 'required' => true],
            'PersonName'    => ['type' => 'string', 'required' => true],
            'PersonBirthday'=> ['type' => 'datetime','required' => true],
            'PersonCountry' => ['type' => 'string', 'required' => true],
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'books' => $mapper->hasMany($entity, 'Particle\Apps\Entities\Books', 'BookId'),
        ];
    }
}
