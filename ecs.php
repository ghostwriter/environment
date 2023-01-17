<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\GroupImportFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__ . '/vendor/ghostwriter/coding-standard/ecs.php');

    $ecsConfig->sets([SetList::STRICT]);

    $ecsConfig->paths([__DIR__ . '/rector.php', __DIR__ . '/ecs.php', __DIR__ . '/src', __DIR__ . '/tests']);
    $ecsConfig->skip([
        '*/tests/Fixture/*',
        '*/vendor/*',
        GroupImportFixer::class,
        BinaryOperatorSpacesFixer::class,
        GeneralPhpdocAnnotationRemoveFixer::class,
        PhpdocLineSpanFixer::class,
        PhpdocTrimFixer::class,
    ]);
};
