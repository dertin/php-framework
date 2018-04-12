<?php

namespace Particle\Apps\Controllers;

use Particle\Core;

class indexController extends Core\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->show();
    }

    public function testORM()
    {
        $personMapper = $this->spot->mapper('Particle\Apps\Entities\Person');
        $personMapper->migrate();

        $bookMapper = $this->spot->mapper('Particle\Apps\Entities\Books');
        $bookMapper->migrate();

        $sBirthday = '1995-02-24';
        $date = new \DateTime($sBirthday);

        /* Relations (In this case de save the relation )*/
        $person = $personMapper->create(['PersonName' => 'Teo Muj Jr',
                                        'PersonMail' => 'mateomu18@gmail.com',
                                        'PersonBirthday' => $date,
                                        'PersonCountry' => 'Uruguay']);
        $newBook = $bookMapper->build(['BookTitle' => 'The Book 3',
                                       'BookAuthor' => 'Jon Doe',
                                       'BookDatePublished' => $date,
                                       'BookEdition' => 1,
                                       'PersonId' => $person->PersonId,
                                      ]);
        $newBook->relation('person', $person);
        $bookMapper->save($newBook);
        $person->relation('books', $newBook);
        $personMapper->save($person, ['relations' => true]);
        $bookTitle = $person->books->BookTitle;


        return array('PersonId' => (int)$person->PersonId, 'BookTitle'=> $bookTitle);

        // $booksP = $person->books;

        /* Create enetity (Person) */
        // $person = $personMapper->first();
        // $newBook = $bookMapper->create([
        //             'PersonId' => $person->PersonaId,
        //             'BookTitle' => 'Harry Potter',
        //             'BookAuthor' => 'Guillermo Cespedes',
        //             'BookDatePublished' => $date,
        //             'BookEdition' => 1,
        // ]);

        /* Delete && Update */
        // $bookDelete = $bookMapper->first();
        // $bookDelete->BookTitle = 'Title Change';
        // $this->view->assign('TitleUpd', $bookDelete->BookTitle);
        // $bookMapper->update($bookDelete);
        // $result = $bookMapper->delete($bookDelete);
        // if (!$result) {
        //     return false;
        // } elseif (is_numeric($result)) {
        //     $resultD = 'Delete success';
        // }

        /* Events (if personId <= 0 exception)*/
        // $person = $personMapper->create(['PersonId' => 0,
        //                                 'PersonName' => 'Teo Muj Jr',
        //                                 'PersonMail' => 'mateomu18@gmail.com',
        //                                 'PersonBirthday' => $date,
        //                                 'PersonCountry' => 'Uruguay']);

        /* Custom Mapper */
        // $owners1stEditionBooks = $personMapper->libros1eraEdicion();

        /* Retrive all */
        // $people = $personMapper->all();
        // $books = $bookMapper->all();
    }
}
