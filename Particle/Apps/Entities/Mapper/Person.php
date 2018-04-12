<?php

namespace Particle\Apps\Entities\Mapper;

use Spot\Mapper;

class Person extends Mapper
{
    public function libros1eraEdicion()
    {
        return $this->query('SELECT * FROM person INNER JOIN books ON books.PersonId = person.PersonId
                              WHERE books.BookEdition = 1');
    }
}
