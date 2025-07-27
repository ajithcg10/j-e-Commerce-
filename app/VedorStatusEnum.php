<?php

namespace App;

enum VedorStatusEnum : string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public static function label(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Approved->value => 'Approved',
            self::Rejected->value => 'Rejected',
        ];
    }
    public static function colors(): array
    {
        return [
        'gray' =>   self::Pending->value ,
        'success' =>  self::Approved->value ,
        'danger' =>  self::Rejected->value 
        ];
    }
}

