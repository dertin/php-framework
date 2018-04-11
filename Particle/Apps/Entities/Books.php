<?php

namespace Particle\Apps\Entities;

use Spot\EntityInterface as Entity;
use Spot\MapperInterface as Mapper;

class Books extends \Spot\Entity
{
    protected static $table = 'books';

    public static function fields()
    {
        return [
            'BookId'       => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'PersonId' => ['type' => 'integer', 'required' => true],
            'BookTitle'   => ['type' => 'string', 'required' => true],
            'BookAuthor' => ['type' => 'string', 'required' => true],
            'BookDatePublished'=> ['type' => 'datetime','required' => true],
            'BookEdition' => ['type' => 'integer', 'required' => true],
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'person' => $mapper->belongsTo($entity, 'Particle\Apps\Entities\Person', 'PersonId'),
        ];
    }

    public static function events(EventEmitter $eventEmitter)
    {
        $eventEmitter->on('beforeSave', function (Entity $entity, Mapper $mapper) {
            if ($entity->PersonId <= 0) {
                throw new \Exception("El id de la persona debe relacionarse con las de
                                        la tabla persona");
                return false;
            }
        });
    }
}
