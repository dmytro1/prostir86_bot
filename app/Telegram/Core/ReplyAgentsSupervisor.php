<?php

namespace App\Telegram\Core;

use App\Telegram\ReplyAgents\AbstractReplyAgent;
use App\Telegram\ReplyAgents\DefaultReplyAgent;
use App\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class ReplyAgentsSupervisor
{
    const FORCE_AGENT = 'force_agent';

    /** @var AbstractReplyAgent[] */
    protected $agents = [];

    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;

        $agents = config('telegram.replyAgents', []);
        $this->addAgents($agents);
    }

    public function addAgent($agent)
    {
        /** @var AbstractReplyAgent $agent */
        $agent = $this->resolveAgent($agent);

        $this->agents[$agent->getName()] = $agent;
    }

    public function addAgents($agents)
    {
        foreach ($agents as $agent) {
            $this->addAgent($agent);
        }
    }

    public function removeAgent($name)
    {
        unset($this->agents[$name]);
    }

    public function removeAgents($names)
    {
        foreach ($names as $name) {
            $this->removeAgent($name);
        }
    }

    protected function resolveAgent($agent)
    {
        if ($agent instanceof AbstractReplyAgent) {
            return $agent;
        } elseif (is_string($agent) && class_exists($agent)) {
            $agentObj = new $agent($this->telegram);
            if ($agentObj instanceof AbstractReplyAgent) {
                return $agentObj;
            }
        }

        throw new \Exception("Could not resolve agent.");
    }

    public function handle(Update $update)
    {
        $chat_id = $update->message->chat->id;

        $userState = User::where(['chat_id' => $chat_id])->value('state');
        if ($userState && isset($this->agents[$agentName = $userState])) {
            $agent = $this->agents[$agentName];
            $agent->setUpdate($update);
            $agent->handle();

            return false;
        } else {
            //foreach ($this->agents as $agent) {
            //    print_r($agent->getName() . ' Agent not found');
            //    $agent->setUpdate($update);
            //    if (false === $agent->handle()) {
            //        break;
            //    }
            //}
            $default = new DefaultReplyAgent($this->telegram);
            $default->setUpdate($update);
            $default->handle();
        }
    }
}
