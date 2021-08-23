<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver;

use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\ArgumentResolverException;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\ArgumentNotFoundException;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\WrongTypeArgumentException;

class ArgumentResolver
{
    private array $predefined = [];
    /**
     * @var array|ArgumentResolverInterface[]
     */
    private array $resolvers = [];

    public function predefine(string $className, object $argument): void
    {
        $this->predefined[$className] = $argument;
    }

    public function addResolver(ArgumentResolverInterface $resolver): void
    {
        if (!in_array($resolver, $this->resolvers)) {
            $this->resolvers[] = $resolver;
        }
    }

    /**
     * @throws ArgumentResolverException
     */
    public function getArguments(object $handler, string $method, array $messageData): array
    {
        $refMethod = new \ReflectionMethod($handler::class, $method);
        $methodArgs = $refMethod->getParameters();
        $arguments = [];

        foreach ($methodArgs as $methodArg) {
            $argType = $methodArg->getType();
            $argTypeName = $argType->getName();

            if ($argType->isBuiltin()) {
                $argName = $methodArg->getName();

                if (!isset($messageData[$argName])) {
                    throw new ArgumentNotFoundException('Argument ' . $argName . ' not found in message data');
                }

                $arguments[] = match ($argTypeName) {
                    'string' => $this->getStringArg($messageData, $argName),
                    'int' => $this->getIntArg($messageData, $argName),
                    'array' => $this->getArrayArg($messageData, $argName),
                    'float' => $this->getFloatArg($messageData, $argName),
                    'bool' => $this->getBoolArg($messageData, $argName)
                };
            } elseif ($this->isPredefined($argTypeName)) {
                $arguments[] = $this->predefined[$argTypeName];
            } elseif (!is_null($argument = $this->getArgFromResolvers($messageData, $argTypeName))) {
                $arguments[] = $argument;
            } else {
                throw new ArgumentNotFoundException('Argument of type '
                    . $argTypeName . ' cannot be resolved');
            }
        }

        return $arguments;
    }

    /**
     * @throws WrongTypeArgumentException
     */
    private function getStringArg(array $messageData, string $argName): string
    {
        if (!is_string($messageData[$argName])) {
            throw new WrongTypeArgumentException('Argument ' . $argName . ' has wrong type. ' .
                'Expected: string, actual: ' . gettype($messageData[$argName]));
        }

        return $messageData[$argName];
    }

    /**
     * @throws WrongTypeArgumentException
     */
    private function getIntArg(array $messageData, string $argName): int
    {
        if (!is_int($messageData[$argName])) {
            throw new WrongTypeArgumentException('Argument ' . $argName . ' has wrong type. ' .
                'Expected: int, actual: ' . gettype($messageData[$argName]));
        }

        return $messageData[$argName];
    }

    /**
     * @throws WrongTypeArgumentException
     */
    private function getArrayArg(array $messageData, string $argName): array
    {
        if (!is_array($messageData[$argName])) {
            throw new WrongTypeArgumentException('Argument ' . $argName . ' has wrong type. ' .
                'Expected: array, actual: ' . gettype($messageData[$argName]));
        }

        return $messageData[$argName];
    }

    /**
     * @throws WrongTypeArgumentException
     */
    private function getFloatArg(array $messageData, string $argName): float
    {
        if (!is_float($messageData[$argName])) {
            throw new WrongTypeArgumentException('Argument ' . $argName . ' has wrong type. ' .
                'Expected: float, actual: ' . gettype($messageData[$argName]));
        }

        return $messageData[$argName];
    }

    /**
     * @throws WrongTypeArgumentException
     */
    private function getBoolArg(array $messageData, string $argName): bool
    {
        if (!is_bool($messageData[$argName])) {
            throw new WrongTypeArgumentException('Argument ' . $argName . ' has wrong type. ' .
                'Expected: bool, actual: ' . gettype($messageData[$argName]));
        }

        return $messageData[$argName];
    }

    private function isPredefined(string $argTypeName): bool
    {
        return isset($this->predefined[$argTypeName]);
    }

    /**
     * @throws ArgumentResolverException
     */
    private function getArgFromResolvers(array $messageData, string $argTypeName): ?object
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($argTypeName)) {
                return $resolver->resolve($messageData, $argTypeName);
            }
        }

        return null;
    }
}
