<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Php73\Rector\ConstFetch\SensitiveConstantNameRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([
        __DIR__
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        SensitiveConstantNameRector::class,
        ReturnNeverTypeRector::class,
        ClosureToArrowFunctionRector::class,
    ])
    ->withImportNames(removeUnusedImports: true)
   ->withSets([
       SetList::PHP_81
   ]);
