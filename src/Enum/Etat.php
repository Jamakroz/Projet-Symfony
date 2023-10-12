<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Etat extends Enum
{
    private const CREATION =  'En création';
    private const OPEN =  'Ouvert';
    private const INPROGRESS =  'En cours';
    private const CLOSED =  'Fermée';

}