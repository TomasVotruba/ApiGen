<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\TraitReflectionInterface;

interface ReflectionStorageInterface
{
    /**
     * @param ClassReflectionInterface[] $classReflections
     */
    public function setClassReflections(array $classReflections): void;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array;

    /**
     * @param InterfaceReflectionInterface[] $interfaceReflections
     */
    public function setInterfaceReflections(array $interfaceReflections): void;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array;

    /**
     * @param TraitReflectionInterface[] $traitReflections
     */
    public function setTraitReflections(array $traitReflections): void;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array;

    /**
     * @param FunctionReflectionInterface[] $functionReflections
     */
    public function setFunctionReflections(array $functionReflections): void;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array;
}
