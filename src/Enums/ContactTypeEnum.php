<?php

namespace Jenishev\Laravel\Contacts\Enums;

use Jenishev\Laravel\Contacts\Casts\Strategies\EmailStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\NullStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\PhoneStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\TelegramStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\UrlStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\WhatsAppStrategy;
use Jenishev\Laravel\Contacts\Contracts\ContactValueStrategy;
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
    case Whatsapp = 'whatsapp';
    case Telegram = 'telegram';
    case Url = 'url';
    case Other = 'other';

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::Whatsapp => 'WhatsApp',
            self::Url => 'URL',
            default => $this->traitLabel(),
        };
    }

    /**
     * Get the strategy class for processing this contact type.
     *
     * @return class-string<ContactValueStrategy>
     */
    public function getStrategyClass(): string
    {
        return match ($this) {
            self::Phone => PhoneStrategy::class,
            self::Email => EmailStrategy::class,
            self::Url => UrlStrategy::class,
            self::Whatsapp => WhatsAppStrategy::class,
            self::Telegram => TelegramStrategy::class,
            self::Other => NullStrategy::class,
            // default will throw an exception
        };
    }
}
