<?php

namespace Particle\Apps\Entities;

class Person extends Spot\Entity
{
    protected static $table = 'person';
    protected static $mapper = 'Entities\Mapper\person';

    public static function fields()
    {
        return [
            'UserId'      => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'UserMail'    => ['type' => 'string', 'required' => true],
            'UserName'    => ['type' => 'string', 'required' => true],
            'UserBirthday'=> ['type' => 'datetime','required' => true],
            'UserCountry' => ['type' => 'string', 'required' => true],
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'books' => $mapper->HasMany($entity, 'Entity\Books', 'BookId'),
        ];
    }
}
