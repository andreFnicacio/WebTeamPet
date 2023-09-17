<?php

namespace Modules\Vindi\Mappers;

class PhoneDataMapper extends AbstractDataMapper implements DataMapperInterface
{
    const PHONE_TYPE_CELLPHONE = 'mobile';
    const PHONE_TYPE_TELEPHONE = 'landline';
    const COUNTRY_CODE = "55";

    public function toRequest(array $request)
    {
        if (empty($request)) {
            return $request;
        }

        $data = [];
        if (!empty($request["telefone"])) {
            $data[] = $this->buildPhoneNode(self::PHONE_TYPE_TELEPHONE, $request['telefone']);
        }

        if (!empty($request["celular"])) {
            $data[] = $this->buildPhoneNode(self::PHONE_TYPE_CELLPHONE, $request['celular']);
        }

        return $data;
    }

    private function buildPhoneNode($type, $number)
    {
        return [
            "phone_type" => $type,
            "number" => self::COUNTRY_CODE . $number
        ];
    }

}