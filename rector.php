<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->sets([
        SetList::PHP_52,
        SetList::PHP_53,
        SetList::PHP_54,
        SetList::PHP_55,
        SetList::PHP_56,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        SetList::PHP_74,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PHP_82,
        SetList::CODE_QUALITY,
    ]);

    $rectorConfig->rule(\Rector\Php82\Rector\Class_\ReadOnlyClassRector::class);
    $rectorConfig->rule(\Rector\Strict\Rector\Ternary\BooleanInTernaryOperatorRuleFixerRector::class);
    $rectorConfig->rule(\Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector::class);
    $rectorConfig->rule(\Rector\Strict\Rector\Ternary\DisallowedShortTernaryRuleFixerRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Switch_\BinarySwitchToIfElseRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\CallUserFuncArrayToVariadicRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\CallUserFuncToMethodCallRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\ConsistentImplodeRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\If_\NullableCompareToNullRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\If_\NullableCompareToNullRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\ClassConst\SplitGroupedClassConstantsRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Property\SplitGroupedPropertiesRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Closure\StaticClosureRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector::class);
    $rectorConfig->rule(\Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\Cast\RecastingRemovalRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\BooleanAnd\RemoveAndTrueRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\BooleanAnd\RemoveAndTrueRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\StmtsAwareInterface\RemoveJustPropertyFetchForAssignRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class);
    $rectorConfig->rule(\Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class);

    // no needed rules
    $rectorConfig->skip([
        \Rector\Php73\Rector\String_\SensitiveHereNowDocRector::class,
        \Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
        \Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector::class,
        \Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector::class,
    ]);
};
