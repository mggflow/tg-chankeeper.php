<?php

namespace MGGFLOW\Telegram\ChannelKeeper;

use MGGFLOW\ExceptionManager\Interfaces\UniException;
use MGGFLOW\ExceptionManager\ManageException;
use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ApiGate;

class PublishActivity
{
    protected ApiGate $apiGate;

    protected string $currentChannel;
    protected ?object $lastMessage;
    protected int $channelActivityPeriod;
    protected string $activityMessage;
    protected ?object $published;

    public function __construct(
        ApiGate $apiGate
    )
    {
        $this->apiGate = $apiGate;
    }

    /**
     * Осуществить активность в канале опубликовав сообщение.
     * @param string $channelName
     * @param int $activityPeriod
     * @param string $message
     * @return bool|object|null
     * @throws UniException
     */
    public function publish(string $channelName, int $activityPeriod, string $message)
    {
        $this->setCurrentChannel($channelName);
        $this->setChannelActivityPeriod($activityPeriod);
        $this->setActivityMessage($message);
        $this->getLastMessage();

        if ($this->channelActive()) {
            return true;
        }

        $this->publishMessage();

        return $this->getResult();
    }

    public function getResult(): ?object
    {
        return $this->published;
    }

    protected function setCurrentChannel(string $name)
    {
        $this->currentChannel = $name;
    }

    protected function setChannelActivityPeriod(int $channelActivityPeriod)
    {
        $this->channelActivityPeriod = $channelActivityPeriod;
    }

    protected function setActivityMessage(string $message)
    {
        $this->activityMessage = $message;
    }

    /**
     * @throws UniException
     */
    protected function getLastMessage()
    {
        $this->lastMessage = $this->apiGate->getLastMessage($this->createChannelPeer());
        if (empty($this->lastMessage)){
            throw ManageException::build()
                ->log()->info()->b()
                ->desc()->failed(null,'to Get Last Message')
                ->context($this->currentChannel, 'currentChannel')->b()
                ->fill();
        }
    }

    protected function channelActive(): bool
    {
        return time() <= ($this->channelActivityPeriod + $this->lastMessage->date);
    }

    /**
     * @throws UniException
     */
    protected function publishMessage()
    {
        $this->published = $this->apiGate->sendMessage($this->createChannelPeer(), $this->activityMessage);
        if (empty($this->published)) {
            throw ManageException::build()
                ->log()->info()->b()
                ->desc()->failed(null,'to Publish Activity Message')
                ->context($this->currentChannel, 'currentChannel')
                ->context($this->channelActivityPeriod, 'channelActivityPeriod')
                ->context($this->activityMessage, 'activityMessage')
                ->context($this->lastMessage, 'lastMessage')->b()
                ->fill();
        }
    }

    protected function createChannelPeer(): string
    {
        return '@' . $this->currentChannel;
    }
}