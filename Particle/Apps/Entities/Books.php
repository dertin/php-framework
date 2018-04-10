<?php

// namespace Entity;
//
// use Spot\EntityInterface as Entity;
// use Spot\MapperInterface as Mapper;
// use Spot\EventEmitter;

class Books extends Core\SpotLoad
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
            'BookEdition' => ['type' => 'string', 'required' => true],
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'belogns' => $mapper->BelongsTo($entity, 'Person', 'PersonId'),
        ];
    }

}
