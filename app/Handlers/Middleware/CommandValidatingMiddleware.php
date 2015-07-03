<?php

/*
 * This file is part of StyleCI.
 *
 * (c) Alt Three LTD <support@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StyleCI\StyleCI\Handlers\Middleware;

use Closure;
use Illuminate\Contracts\Validation\Factory;
use ReflectionClass;
use ReflectionProperty;
use Watson\Validating\ValidationException;

/**
 * This is the command validating middleware class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CommandValidatingMiddleware
{
    /**
     * The validation factory instance.
     *
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected $factory;

    /**
     * Create a new run analysis job handler instance.
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     *
     * @return void
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Validate the command before execution.
     *
     * @param object   $command
     * @param \Closure $next
     *
     * @throws \Watson\Validating\ValidationException
     *
     * @return void
     */
    public function handle($command, Closure $next)
    {
        if (property_exists($command, 'rules') && is_array($command->rules)) {
            $this->validate($command);
        }

        return $next($command);
    }

    /**
     * Validate the command.
     *
     * @param object $command
     *
     * @throws \Watson\Validating\ValidationException
     *
     * @return void
     */
    protected function validate($command)
    {
        $messages = property_exists($command, 'validationMessages') ? $command->validationMessages : [];

        $validator = $this->factory->make($this->getData($command), $command->rules, $messages);

        if ($validator->fails()) {
            $exception = new ValidationException(get_class($command).' has failed validation.');
            $exception->setErrors($validator->getMessageBag());
            $exception->setModel($command);

            throw $exception;
        }
    }

    /**
     * Get the data to be validated.
     *
     * @param object $command
     *
     * @return array
     */
    protected function getData($command)
    {
        $data = [];

        foreach ((new ReflectionClass($command))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $value = $property->getValue($command);

            if (in_array($name, ['rules', 'validationMessages'], true) || is_object($value)) {
                continue;
            }

            $data[$name] = $value;
        }

        return $data;
    }
}