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
        $personMapper = $this->spot->mapper('Particle\Apps\Entities\Person');
        // $personMapper->migrate();

        $bookMapper = $this->spot->mapper('Particle\Apps\Entities\Books');
        // $bookMapper->migrate();

        $sBirthday = '1995-02-24';
        $date = new \DateTime($sBirthday);
        $people = $personMapper->all();

        $person = $personMapper->first();
        $newBook = $bookMapper->create([
                    'PersonId' => $person->PersonName,
                    'BookTitle' => 'Harry Potter',
                    'BookAuthor' => 'Guillermo Cespedes',
                    'BookDatePublished' => $date,
                    'BookEdition' => 1,
        ]);

        // $newBook->relation('person', $person);
        // $bookMapper->save($newBook, ['relations' => true]);


        // $autoNew = $autoMapper->build(['marcaAuto' => 'Bugatti',
        //                                'matriculaAuto' => 123456,
        //                               ]);
        // $persona = new \Entity\Persona(['nombrePersona' => 'Teo Muj',
        //                                 'edadPersona' => 25,
        //                                 'fechaNacPersona' => $date,
        //                                 'telefonoPersona' => '099123456']);
        // $autoNew->relation('duenio', $persona);
        // $autoMapper->save($autoNew, ['relations' => true]);

        // $newPerson = $personMapper->build([
        //            'PersonMail' => 'mateomu18@gmail.com',
        //            'PersonName' => 'Mateo Mujica',
        //            'PersonBirthday' => $date,
        //            'PersonCountry' => 'Uruguay'
        //           ]);
        // $result = $personMapper->insert($newPerson);
        // if (!$result) {
        //     echo "No insert";
        //     return false;
        // }
        // $nameP = $personMapper->first(['PersonName' => 'Mateo Mujica'])->PersonName;
        // $this->view->assign('PersonName', $nameP);

        $this->view->assign('people', $people);
        $this->view->show();
    }
}
