<?php

namespace App;


class StringUtil
{
    public static function parseFullName($fullName)
    {
        $names = explode(" ", $fullName);
        if(count($names)>1){
            return $names;
        }else{
            //there is no space
            array_push($names,'');
            return $names;
        }
    }
}