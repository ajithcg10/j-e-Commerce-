<?php

namespace App;

enum ProductVariationsTypesEnum:string
{
    case Select = 'Select';
    case Radio = 'Radio';
    case Image ='Image';

    public static function label():array{
        return [
            self::Select->value => __('Select'),
            self::Radio->value => __('Radio'),
            self::Image->value => __('Image'),

        ];
    }
 }
