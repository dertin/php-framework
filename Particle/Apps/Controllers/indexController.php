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
        $personMapper->migrate();

        $bookMapper = $this->spot->mapper('Particle\Apps\Entities\Books');
        $bookMapper->migrate();

        $date = new \DateTime();
        $newPerson = $personMapper->build([
                   'PersonMail' => 'mateomu18@gmail.com',
                   'PersonName' => 'Mateo Mujica',
                   'PersonBirthday' => $date,
                   'PersonCountry' => 'Uruguay'
                  ]);
        $result = $personMapper->insert($newPerson);
        if (!$result) {
            echo "No insert";
            return false;
        }
        $this->view->assign('PersonName', $personMapper->first(['PersonName' => 'Mateo Mujica'])->PersonName);
        $this->view->show();
    }
}
