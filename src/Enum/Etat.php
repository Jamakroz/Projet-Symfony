<?php

namespace App\Enum;

use MyCLabs\Enum\Enum;

class Etat extends Enum
{
    public const CREATION =  'Créée';
    public const OPEN =  'Ouverte';
    public const INPROGRESS =  'Activité en cours';
    public const CLOSED =  'Cloturée';
    public const PAST =  'Passée';
    public const CANCELED =  'Annulée';
    public const ALL = 'Choisir une option';

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


//namespace App\Enum;
//
//use MyCLabs\Enum\Enum;
//
//enum Etat :string
//{
//    case CREATION = 'Créée';
//    case OPEN = 'Ouverte';
//    case INPROGRESS = 'Activité en cours';
//    case  CLOSED = 'Cloturée';
//    case  PAST = 'Passée';
//    case  CANCELED = 'Annulée';
//    case  ALL = 'Choisir une option';
//
//    public static function toArray(): array
//    {
//        return [
//            'Choisir une option' => self::ALL,
//            'Créée' => self::CREATION,
//            'Ouverte' => self::OPEN,
//            'Activité en cours' => self::INPROGRESS,
//            'Cloturée' => self::CLOSED,
//            'Passée' => self::PAST,
//            'Annulée' => self::CANCELED,
//        ];
//    }
//}