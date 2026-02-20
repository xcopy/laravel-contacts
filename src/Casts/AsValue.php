<?php

namespace Jenishev\Laravel\Contacts\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Jenishev\Laravel\Contacts\Casts\Strategies\NullStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\Strategy;
use Jenishev\Laravel\Contacts\Enums\ContactTypeEnum;
use ValueError;

/**
 * Custom cast for contact values that applies type-specific validation and normalization.
 */
class AsValue implements CastsAttributes
{
    /**
     * Resolve the appropriate strategy based on contact type.
     */
    private function resolveStrategy(ContactTypeEnum|string|null $type): Strategy
    {
        if ($type instanceof ContactTypeEnum) {
            $class = $type->getStrategyClass();
        } else {
            try {
                $class = ContactTypeEnum::from((string) $type)->getStrategyClass();
            } catch (ValueError) {
                $class = NullStrategy::class;
            }
        }

        return new $class;
    }

    /**
     * {@inheritDoc}
     */
    public function get(Model $model, string $key, $value, array $attributes): mixed
    {
        $type = $attributes['type'] ?? '';

        return $this->resolveStrategy($type)->get($value, $attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function set(Model $model, string $key, $value, array $attributes): ?string
    {
        $type = $attributes['type'] ?? '';

        return $this->resolveStrategy($type)->set($value, $attributes);
    }
}
