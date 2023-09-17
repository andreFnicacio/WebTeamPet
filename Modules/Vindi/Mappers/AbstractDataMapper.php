<?php

namespace Modules\Vindi\Mappers;

use Illuminate\Support\Arr;

abstract class AbstractDataMapper
{
    protected $mapper;
    protected $imutables = [];
    protected $unsetAfterParseToRequest = [];

    public function toRequest(array $request)
    {
        $data = [];
        foreach ($this->mapper as $key => $value) {
            $data[$value] = $request[$key];
        }

        return array_merge(Arr::except($data, $this->unsetAfterParseToRequest), $this->imutables);
    }

    public function fromResponse(array $response)
    {
        $data = [];
        foreach ($this->mapper as $key => $value) {
            if (count($response) === 1) {
                foreach ($response as $k => $v) {
                    $data[$key] = $response[$k][$value];
                }
            } else {
                $data[$key] = $response[$value];
            }
        }

        return $data;
    }
}