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
    private const ALL = 'Choisir une option';

    public static function toArray(): array
    {
        return [
            'Choisir une option' => self::ALL,
            'Créée' => self::CREATION,
            'Ouverte' => self::OPEN,
            'Activité en cours' => self::INPROGRESS,
            'Cloturée' => self::CLOSED,
            'Passée' => self::PAST,
            'Annulée' => self::CANCELED,
        ];
    }
}