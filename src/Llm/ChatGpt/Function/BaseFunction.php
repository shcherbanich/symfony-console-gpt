<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

abstract class BaseFunction implements FunctionInterface
{
    final public function asArray(): array
    {
        $data = ['name' => $this->getName()];
        if ($description = $this->getDescription()) {
            $data['description'] = $description;
        }
        if ($arguments = $this->getArguments()) {
            $requiredArguments = [];
            $preparedArguments = [];
            foreach ($arguments as $argument) {
                $preparedArguments[$argument->getName()] = [
                    'type' => count($argument->getTypes()) === 1 ? $argument->getTypes()[0] : $argument->getTypes(),
                    'description' => $argument->getDescription()
                ];
                if ($argument->isRequired()) {
                    $requiredArguments[] = $argument->getName();
                }
            }
            $parameters = [
                'type' => 'object',
                'additionalProperties' => false,
                'properties' => $preparedArguments,
            ];
            if ($requiredArguments) {
                $parameters['required'] = $requiredArguments;
            }
            $data['parameters'] = $parameters;
        } else {
            $data['parameters'] = [
                'type' => 'object',
                'properties' => [
                    'run' => [
                        'type' => 'boolean'
                    ]
                ]
            ];
        }
        return $data;
    }
}
