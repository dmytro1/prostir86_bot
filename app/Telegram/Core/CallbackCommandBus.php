<?php

namespace App\Telegram\Core;

use App\Telegram\CallbackCommands\CallbackCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class CallbackCommandBus
{
    protected $commands = [];

    /** @var Api */
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;

        $commands = config('telegram.callbackCommands', []);
        $this->addCommands($commands);
    }

    public function addCommands($commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    public function addCommand($command)
    {
        $command = $this->resolveCommand($command);

        $this->commands[$command->getName()] = $command;
    }

    /**
     * @param $command
     *
     * @return CallbackCommand
     * @throws \Exception
     */
    protected function resolveCommand($command)
    {
        if ($command instanceof CallbackCommand) {
            return $command;
        } elseif (is_string($command)) {
            try {
                $command = app()->make($command, [$this->telegram]);
                if ($command instanceof CallbackCommand) {
                    return $command;
                }
            } catch (\Throwable $e) {
                throw new \Exception("Could not instantiate command {$command}");
            }
        }

        throw new \Exception("Command {$command} is not an instance of CallbackCommand");
    }

    public function removeCommand($name)
    {
        unset($this->commands[$name]);
    }

    public function removeCommands($names)
    {
        foreach ($names as $name) {
            $this->removeCommand($name);
        }
    }

    public function handle($data, Update $update)
    {
        if (preg_match('~^([a-z]+):(.*)~is', $data, $matches)) {
            $callbackCommandName = $matches[1];
            $paramsRaw = $matches[2];
            $params = explode(",", $paramsRaw);

            if (isset($this->commands[$callbackCommandName])) {
                $this->execute(get_class($this->commands[$callbackCommandName]), $update, $params);
            }
        }
    }

    protected function execute($commandClass, $update, $parameters)
    {
        /** @var CallbackCommand $command */
        $command = app()->make($commandClass, [$this->telegram]);
        $command->setUpdate($update)->setParameters($parameters);

        $command->handle();

        return false;
    }
}
