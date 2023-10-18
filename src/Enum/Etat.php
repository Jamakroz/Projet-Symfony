<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Etat extends Enum
{
    private const CREATION =  'Créée';
    private const OPEN =  'Ouverte';
    private const INPROGRESS =  'Activité en cours';
    private const CLOSED =  'Cloturée';
    private const PAST =  'Passée';
    private const CANCELED =  'Annulée';

}