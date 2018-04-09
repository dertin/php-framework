<?php

// namespace Entity;
//
// use Spot\EntityInterface as Entity;
// use Spot\MapperInterface as Mapper;
// use Spot\EventEmitter;

class Person extends Core\Model
{
    protected static $table = 'person';
    protected static $mapper = 'Entities\Mapper\person';

    public static function fields()
    {
        return [
            'UserId'       => ['type' => 'integer', 'autoincrement' => true, 'primary' => true],
            'USerMail'   => ['type' => 'string', 'required' => true],
            'UserName' => ['type' => 'string', 'required' => true],
            'UserBirthday'=> ['type' => 'datetime','required' => true],
            'UserCountry' => ['type' => 'string', 'required' => true],
        ];
    }

    public static function relations(Mapper $mapper, Entity $entity)
    {
        return [
            'books' => $mapper->HasMany($entity, 'Entity\books', 'BookId'),
        ];
    }

}
