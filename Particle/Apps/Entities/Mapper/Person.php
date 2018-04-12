<?php

namespace Particle\Apps\Entities;

use Spot\Mapper;

class Persona extends Mapper
{
    public function libros1eraEdicion()
    {
        return $this->query('SELECT * FROM person INNER JOIN book ON book.PersonId = Person.personId
                              WHERE book.BookEdition = 1');
    }
}
