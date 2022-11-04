<?php

namespace MGGFLOW\Telegram\ChannelKeeper;

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

    protected function getLastMessage()
    {
        $this->lastMessage = $this->apiGate->getLastMessage($this->currentChannel);
    }

    protected function channelActive(): bool
    {
        return time() <= ($this->channelActivityPeriod + $this->lastMessage->date);
    }

    protected function publishMessage()
    {
        $this->published = $this->apiGate->sendMessage($this->createChannelPeer(), $this->activityMessage);
    }

    protected function createChannelPeer(): string
    {
        return '@' . $this->currentChannel;
    }
}