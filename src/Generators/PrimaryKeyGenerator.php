<?php

namespace Corp104\Eloquent\Generator\Generators;

use Xethron\MigrationsGenerator\Generators\IndexGenerator;

class PrimaryKeyGenerator
{
    /**
     * @param IndexGenerator $indexGenerator
     * @param array $fields
     * @return string
     */
    public function generate(IndexGenerator $indexGenerator, $fields)
    {
        $fieldsWithPk = array_filter($fields, function ($attr) use ($indexGenerator) {
            $indexAttr = $indexGenerator->getIndex($attr['field']);
            return isset($indexAttr->type) && 'primary' === $indexAttr->type;
        });

        $pks = array_values(array_map(function ($attr) {
            return $attr['field'];
        }, $fieldsWithPk));

        return $this->buildArrayCode($pks);
    }

    protected function buildArrayCode(array $pks)
    {
        if (count($pks) !== 1) {
            return 'null';
        }

        return "'{$pks[0]}'";
    }
}
