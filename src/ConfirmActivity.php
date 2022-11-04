<?php

namespace MGGFLOW\Telegram\ChannelKeeper;

use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ApiGate;
use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelsTransmitter;

class ConfirmActivity
{
    protected ChannelsTransmitter $channelsTransmitter;
    protected int $channelsCount;
    protected ApiGate $apiGate;
    protected array $channelsNames;
    protected array $result;
    protected string $currentChannel;
    protected ?object $lastMessage;
    protected ?object $reacted;

    public function __construct(
        ChannelsTransmitter $channelsTransmitter, int $channelsCount,
        ApiGate             $apiGate
    )
    {
        $this->channelsTransmitter = $channelsTransmitter;
        $this->channelsCount = $channelsCount;
        $this->apiGate = $apiGate;
    }

    public function confirm(): array
    {
        $this->takeChannels();
        $this->serveChannels();

        return $this->getResult();
    }

    public function getResult(): array
    {
        return $this->result;
    }

    protected function takeChannels()
    {
        $this->channelsNames = $this->channelsTransmitter->getFreshNames($this->channelsCount);
    }

    protected function serveChannels()
    {
        foreach ($this->channelsNames as $channelName) {
            $this->setCurrentChannel($channelName);
            $this->serveChannel();
        }
    }

    protected function setCurrentChannel(string $name)
    {
        $this->currentChannel = $name;
    }

    protected function serveChannel()
    {
        $this->getLastMessage();
        $this->reactToMessage();
        $this->addResult();
    }

    protected function getLastMessage()
    {
        $this->lastMessage = $this->apiGate->getChannelLastMessage($this->currentChannel);
    }

    protected function reactToMessage()
    {
        $this->reacted = $this->apiGate->reactToMessage($this->lastMessage);
    }

    protected function addResult()
    {
        $this->result[$this->currentChannel] = $this->reacted;
    }
}