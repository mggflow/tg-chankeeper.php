<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Exceptions;

class FailedToReactToMessage extends \Exception
{
    protected $message = 'Failed to react to the message.';
}