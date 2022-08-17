<?php

declare(strict_types=1);

namespace Rector\Doctrine\NodeManipulator;

use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFalseNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprTrueNode;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;

final class DoctrineItemDefaultValueManipulator
{
    public function clearDoctrineAnnotationTagValueNode(
        PhpDocInfo $phpDocInfo,
        DoctrineAnnotationTagValueNode $doctrineAnnotationTagValueNode,
        string $item,
        string|bool|int $defaultValue
    ): bool {
        if (! $this->hasItemWithDefaultValue($doctrineAnnotationTagValueNode, $item, $defaultValue)) {
            return false;
        }

        $doctrineAnnotationTagValueNode->removeValue($item);

        $phpDocInfo->markAsChanged();
        return true;
    }

    private function hasItemWithDefaultValue(
        DoctrineAnnotationTagValueNode $doctrineAnnotationTagValueNode,
        string $itemKey,
        string|bool|int $defaultValue
    ): bool {
        $currentValue = $doctrineAnnotationTagValueNode->getValueWithoutQuotes($itemKey);
        if ($currentValue === null) {
            return false;
        }

        if ($defaultValue === false) {
            return $currentValue instanceof ConstExprFalseNode;
        }

        if ($defaultValue === true) {
            return $currentValue instanceof ConstExprTrueNode;
        }

        if (is_int($defaultValue) && $currentValue instanceof ConstExprIntegerNode) {
            $currentValue = (int) $currentValue->value;
        }

        return $currentValue === $defaultValue;
    }
}
