<?php

namespace App;

enum OrderStatusEnum:string
{
   case Draft = 'draft';
   case Paid = 'paid';
   case Shipped = 'shipped';
   case Delivered = 'delivered';
   case Cancelled = 'cancelled';

   public static function getStatusLabel(string $status): string
   {
       return match ($status) {
           self::Draft->value => 'Draft',
           self::Paid->value => 'Paid',
           self::Shipped->value => 'Shipped',
           self::Delivered->value => 'Delivered',
           self::Cancelled->value => 'Cancelled',

       };
   }
}
