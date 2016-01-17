<?php

namespace sndsgd\http\form;

use \sndsgd\Field;
use \sndsgd\field\IntegerField;
use \sndsgd\field\StringField;
use \sndsgd\field\rule\ClosureRule;
use \sndsgd\field\rule\MaxValueCountRule;
use \sndsgd\field\rule\MinValueRule;
use \sndsgd\field\rule\MaxValueRule;
use \sndsgd\model\field\rule\SortablePropertyRule;


class FilterForm extends \sndsgd\http\Form
{
    /**
     * Any properties that are used for sorting results but are NOT in the
     * model are included here
     *
     * @var array<string,string>
     */
    protected $sortableProperties = [];

    /**
     * @return array<string,string>
     */
    public function getSortableProperties()
    {
        return $this->sortableProperties;
    }

    /**
     * {@inheritdoc}
     */
    public function registerFields()
    {
        $controller = $this->controller;
        $this->addFields([
            (new IntegerField("pagination-page"))
                ->setDescription("The result page number")
                ->setExportHandler(Field::EXPORT_SKIP)
                ->setDefault(1)
                ->addRules([
                    new MinValueRule(1),
                    new MaxValueRule(PHP_INT_MAX),
                    new MaxValueCountRule(1)
                ]),
            (new IntegerField("pagination-per-page"))
                ->setDescription("The number of entities to display per page")
                ->setExportHandler(Field::EXPORT_SKIP)
                ->setDefault(25)
                ->addRules([
                    new MinValueRule(1),
                    new MaxValueRule(1000),
                    new MaxValueCountRule(1)
                ]),
            (new StringField("sort-column"))
                ->setDescription("The property to sort results by")
                ->setExportHandler(Field::EXPORT_SKIP)
                ->addRules([
                    (new SortablePropertyRule)
                        ->setModel($controller::MODEL)
                        ->setAdditionalProperties($this->sortableProperties)
                ]),
            (new StringField("sort-direction"))
                ->setDescription("The property to sort results by")
                ->setExportHandler(Field::EXPORT_SKIP)
                ->addData("short-hint", "ASC|DESC")
                ->setDefault("ASC")
                ->addRules([
                    new ClosureRule(function($value, $index, $field, $coll) {
                        $val = strtoupper($value);
                        if ($val !== "ASC" && $val !== "DESC") {
                            $this->message = "unknown sort direction '$value'";
                            return false;
                        }
                        return true;
                    }),
                    new ClosureRule(function($value, $index, $field, $coll) {
                        if (count($coll->getField("sort-column")) !== count($field)) {
                            $this->message = "column and direction parameter mismatch";
                            return false;
                        }
                        return true;
                    }),
                ]),
            (new StringField("sort-by"))
                ->setDescription("A combo of the sort property and direction")
                ->setExportHandler(Field::EXPORT_SKIP)
                ->addData("short-hint", "property[:DESC]")
                ->addRules([
                    new ClosureRule(function($value, $index, $field, $coll) {
                        $parts = explode("|", $value);
                        list($property, $direction) = array_pad($parts, 2, "EQ");
                    })
                ])
        ]);
    }
}
