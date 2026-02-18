<?php

namespace Jenishev\Laravel\Contacts\Enums;

use Jenishev\Laravel\Support\Enums\Concerns\HasChoices;
use Jenishev\Laravel\Support\Enums\Concerns\HasValues;

/**
 * Defines available contact method types for the system.
 */
enum ContactTypeEnum: string
{
    use HasChoices {
        HasChoices::label as traitLabel;
    }
    use HasValues;

    case Phone = 'phone';
    case Email = 'email';
    case Mobile = 'mobile';
    case Whatsapp = 'whatsapp';
    case Telegram = 'telegram';
    case Website = 'website';
    case Other = 'other';

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        if ($this->value === 'whatsapp') {
            return __('WhatsApp');
        }

        return $this->traitLabel();
    }
}
