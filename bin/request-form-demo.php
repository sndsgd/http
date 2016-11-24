<?php

use \sndsgd\form\field;
use \sndsgd\form\rule;

require __DIR__."/../vendor/autoload.php";

$form = (new \sndsgd\Form())
    ->addFields(
        (new field\StringField("caption"))
            ->addRules(
                new rule\RequiredRule(),
                new rule\MaxLengthRule(255)
            ),
        (new field\UploadedFileField("image"))
            ->addRules(
                new rule\RequiredRule(),
                new rule\UploadedFileTypeRule("image/jpeg", "image/png")
            )
    );

$environment = new \sndsgd\Environment($_SERVER);
$request = new \sndsgd\http\Request($environment);
$validator = new \sndsgd\form\Validator($form);
try {
    $parameters = $validator->validate($request->getBodyParameters());
    $message = "Success";
} catch (\sndsgd\form\ValidationException $ex) {
    $message = "Validation Error";
    $errors = $ex->getErrors();
}

echo json_encode([
    "message" => $message,
    "parameters" => $parameters ?? null,
    "errors" => $errors ?? [],
], \sndsgd\Json::HUMAN);
