<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Exceptions;

class FailedToGetLastMessage extends \Exception
{
    protected $message = 'Failed to get last message in channel.';
}