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
    public function generate(IndexGenerator $indexGenerator, $fields): string
    {
        $fieldsWithPk = array_filter($fields, static function ($attr) use ($indexGenerator) {
            $indexAttr = $indexGenerator->getIndex($attr['field']);
            return isset($indexAttr->type) && 'primary' === $indexAttr->type;
        });

        $pks = array_values(array_map(
            static function ($attr) {
                return $attr['field'];
            },
            $fieldsWithPk
        ));

        return $this->buildArrayCode($pks);
    }

    protected function buildArrayCode(array $pks): string
    {
        if (count($pks) !== 1) {
            return 'null';
        }

        return "'{$pks[0]}'";
    }
}
