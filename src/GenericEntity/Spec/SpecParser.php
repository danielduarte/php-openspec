<?php

namespace GenericEntity\Spec;


class SpecParser
{
    public function parse($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if index 'type' exists
        $type = $specData['type'];

        switch ($type) {
            case 'string': {
                return $this->parseString($specData);
                break;
            }
            case 'boolean': {
                return $this->parseBoolean($specData);
                break;
            }
            case 'object': {
                return $this->parseObject($specData);
                break;
            }
            case 'array': {
                return $this->parseArray($specData);
                break;
            }
            case 'mixed': {
                return $this->parseMixed($specData);
                break;
            }
            // @todo check if $type has not matched with any valid type name
        }
    }

    protected function parseString($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if there are not unknown fields
        return new StringSpec();
    }

    protected function parseBoolean($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if there are not unknown fields
        return new BooleanSpec();
    }

    protected function parseObject($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if required fields are defined
        $fields = $specData['fields'];
        $extensible = $specData['extensible'];
        // @todo check if there are not unknown fields

        $fieldSpecs = [];
        foreach ($fields as $fieldKey => $fieldData) {
            $fieldSpecs[] = $this->parse($fieldData);
        }

        return new ObjectSpec($fieldSpecs, $extensible);
    }

    protected function parseArray($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if required fields are defined
        $itemsType = $specData['items'];
        // @todo check if there are not unknown fields

        $itemsSpec = $this->parse($itemsType);

        return new ArraySpec($itemsSpec);
    }

    protected function parseMixed($specData)
    {
        if (!is_array($specData)) {
            // @todo error array expected. return
        }

        // @todo check if required fields are defined
        $options = $specData['options'];
        // @todo check if there are not unknown fields

        $optionSpecs = [];
        foreach ($options as $option) {
            $optionSpecs[] = $this->parse($option);
        }

        return new MixedSpec($optionSpecs);
    }
}