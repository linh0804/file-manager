<?php
namespace app;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setHideProgress(true)
    ->setUsingCache(false)
    ->setRules([
        '@PSR12' => true,
        'elseif' => false,
        'array_indentation' => true,
        'method_chaining_indentation' => true,
        'visibility_required' => false,
        'no_unneeded_import_alias' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
    ])
    ->setIndent("    ") // 4 space
;