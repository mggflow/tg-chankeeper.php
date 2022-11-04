<?php

namespace MGGFLOW\Telegram\ChannelKeeper;

use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ApiGate;

class ConfirmActivity
{

    protected ApiGate $apiGate;

    protected string $currentChannel;
    protected string $emoticon;
    protected ?object $lastMessage;
    protected ?object $reacted;

    public function __construct(
        ApiGate $apiGate
    )
    {
        $this->apiGate = $apiGate;
    }

    /**
     * Подтвердить активность в канале через реакцию на последнее сообщение.
     * @param string $channelName
     * @param string $emoticon
     * @return object|null
     */
    public function confirm(string $channelName, string $emoticon): ?object
    {
        $this->setCurrentChannel($channelName);
        $this->setEmoticon($emoticon);
        $this->getLastMessage();
        $this->reactToMessage();

        return $this->getResult();
    }

    public function getResult(): ?object
    {
        return $this->reacted;
    }

    protected function setCurrentChannel(string $name)
    {
        $this->currentChannel = $name;
    }

    protected function setEmoticon(string $emoticon)
    {
        $this->emoticon = $emoticon;
    }

    protected function getLastMessage()
    {
        $this->lastMessage = $this->apiGate->getLastMessage($this->currentChannel);
    }

    protected function reactToMessage()
    {
        $this->reacted = $this->apiGate->reactToMessage(
            $this->createChannelPeer(),
            $this->lastMessage->id,
            $this->emoticon
        );
    }

    protected function createChannelPeer(): string
    {
        return '@' . $this->currentChannel;
    }
}