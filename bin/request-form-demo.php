<?php

use \sndsgd\form\field;
use \sndsgd\form\rule;

require __DIR__."/../vendor/autoload.php";

$form = (new \sndsgd\Form())
    ->addFields(
        (new field\ScalarField("caption"))
            ->addRules(
                new rule\IntegerRule(),
                new rule\MaxLengthRule(255)
            ),
        (new field\ScalarField("image"))
            ->addRules(
                new rule\RequiredRule(),
                new rule\UploadedFileTypeRule("image/jpeg", "image/png")
            )
    );

$request = new \sndsgd\http\Request($_SERVER);
$validator = new \sndsgd\form\Validator($form);
try {
    $data = ["payload" => $validator->validate($request->getBodyParameters())];
} catch (\sndsgd\form\ValidationException $ex) {
    $data = ["errors" => $ex->getErrors()];
}

echo json_encode($data, \sndsgd\Json::HUMAN);
