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
        $people = $personMapper->all();
        $this->view->assign('people', $people);
        $this->view->show();
    }
}
