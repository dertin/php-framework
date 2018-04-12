<?php

namespace Particle\Apps\Entities\Mapper;

class Persona extends \Spot\Mapper
{
    public function libros1eraEdicion()
    {
        return $this->query('SELECT * FROM person INNER JOIN book ON book.PersonId = person.PersonId
                              WHERE book.BookEdition = 1');
    }
}
