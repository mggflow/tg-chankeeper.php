<?php

namespace MGGFLOW\Telegram\ChannelKeeper;

use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ApiGate;
use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelActivityPeriodsMap;
use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelMessagesMap;
use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelsTransmitter;

class PublishActivity
{
    protected ChannelsTransmitter $channelsTransmitter;
    protected int $channelsCount;
    protected ChannelMessagesMap $channelMessagesMap;
    protected ApiGate $apiGate;
    protected ChannelActivityPeriodsMap $channelActivityPeriodsMap;
    protected array $channelsNames;
    protected array $result;
    protected string $currentChannel;
    protected ?object $lastMessage;
    protected int $channelActivityPeriod;
    protected string $activityMessage;
    protected ?object $published;

    public function __construct(
        ChannelsTransmitter       $channelsTransmitter, int $channelsCount,
        ChannelMessagesMap        $channelMessagesMap,
        ApiGate                   $apiGate,
        ChannelActivityPeriodsMap $channelActivityPeriodsMap
    )
    {
        $this->channelsTransmitter = $channelsTransmitter;
        $this->channelsCount = $channelsCount;
        $this->channelMessagesMap = $channelMessagesMap;
        $this->apiGate = $apiGate;
        $this->channelActivityPeriodsMap = $channelActivityPeriodsMap;
    }

    public function publish(): array
    {
        $this->resetFields();
        $this->takeChannels();
        $this->serveChannels();

        return $this->getResult();
    }

    public function getResult(): array
    {
        return $this->result;
    }

    protected function resetFields()
    {
        $this->result = [];
        $this->channelsNames = [];
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
        $this->getChannelActivityPeriod();

        if ($this->channelActive()) {
            $this->addEmptyResult();
            return;
        }

        $this->getChannelMessage();
        $this->publishMessage();
        $this->addPublishResult();
    }

    protected function getLastMessage()
    {
        $this->lastMessage = $this->apiGate->getChannelLastMessage($this->currentChannel);
    }

    protected function getChannelActivityPeriod()
    {
        $this->channelActivityPeriod = $this->channelActivityPeriodsMap->getChannelActivityPeriod($this->currentChannel);
    }

    protected function channelActive(): bool
    {
        return time() <= ($this->channelActivityPeriod + $this->lastMessage->date);
    }

    protected function addEmptyResult()
    {
        $this->result[$this->currentChannel] = true;
    }

    protected function getChannelMessage()
    {
        $this->activityMessage = $this->channelMessagesMap->getMessage($this->currentChannel);
    }

    protected function publishMessage()
    {
        $this->published = $this->apiGate->publishChannelMessage($this->currentChannel, $this->activityMessage);
    }

    protected function addPublishResult()
    {
        $this->result[$this->currentChannel] = $this->published;
    }
}