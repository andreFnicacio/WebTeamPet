<?php


namespace App\Helpers\API\Superlogica\V2\Transformers;


abstract class Transformable
{
    public function toArray($hideNull = true): array
    {
        $reflection = new \ReflectionClass($this);
        $array = [];
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property) {
            $propertyName = $property->getName();
            $getterMethod = "get" . ucfirst($propertyName);
            $hasGetter = $reflection->hasMethod($getterMethod);

            $value = null;
            if($hasGetter) {
                $value = $this->$getterMethod();
            } else {
                $value = $this->$propertyName;
            }

            if(!is_null($value)) {
                $array[$propertyName] = $value;
            } else {
                if(!$hideNull) {
                    $array[$propertyName] = $value;
                }
            }
        }

        return $array;
    }

    protected function numberOnly($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}