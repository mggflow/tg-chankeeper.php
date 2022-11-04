<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Exceptions;

class FailedToPublishMessage extends \Exception
{
    protected $message = 'Failed to publish the message to the Channel.';
}