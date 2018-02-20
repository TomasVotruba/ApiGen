<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source;

class ParameterMethodClass
{
    /**
     * @var string
     */
    private const HERE = 'here';

    /**
     * Send a POST request.
     */
    public function methodWithArgs($url = 1, $data = null, $headers = []): void
    {
    }

    public function methodWithClassParameter(ParameterClass $parameterClass): void
    {
    }

    public function methodWithConstantDefaultValue(string $where = self::HERE, string $when = Time::TODAY): void
    {
    }
}
