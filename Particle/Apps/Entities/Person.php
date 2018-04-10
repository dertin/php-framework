<?php

namespace Particle\Apps\Entities;

class Person extends \Spot\Entity
{
    protected static $table = 'person';
    // protected static $mapper = 'Entities\Mapper\person';

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
            'books' => $mapper->HasMany($entity, 'Particle\Apps\Entities\Books', 'BookId'),
        ];
    }

    public static function events(EventEmitter $eventEmitter)
    {
        $eventEmitter->on('beforeSave', function (Entity $entity, Mapper $mapper) {
            if ($entity->UserCountry == 0) {
                throw new \Exception("Edad debe ser mayor a 0");
                return false;
            }
        });
    }
}
