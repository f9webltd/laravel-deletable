<?php

declare(strict_types=1);

namespace F9Web\LaravelDeletable\Traits;

use F9Web\LaravelDeletable\Exceptions\NoneDeletableModel;

use function config;
use function get_class;
use function sprintf;
use function trans;

trait RestrictsDeletion
{
    /** @var null|string|\Illuminate\Contracts\Translation\Translator|array */
    protected $notDeletableMessage = null;

    /**
     * @return bool|null
     * @throws \F9Web\LaravelDeletable\Exceptions\NoneDeletableModel
     */
    public function delete()
    {
        if ($this->isDeletable()) {
            return parent::delete();
        }

        throw new NoneDeletableModel(
            $this->getNoneDeletableMessage()
        );
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getNoneDeletableMessage(): ?string
    {
        return $this->notDeletableMessage ?? $this->getFallbackMessage();
    }

    /**
     * If a default message is omitted from the config, use a default
     *
     * @return string
     */
    public function getFallbackMessage(): string
    {
        if ($message = config('f9web-laravel-deletable.messages.default')) {
            return $message;
        }

        return sprintf(
            'Restricted deletion: %s - %s is not deletable',
            get_class($this),
            $this->getKey()
        );
    }

    /**
     * Set a custom deletion restriction message and stop deletion
     *
     * @param  string  $message
     * @return bool
     */
    public function denyDeletionReason(?string $message = null): bool
    {
        $this->notDeletableMessage = $message;

        return false;
    }

    /**
     * Set a custom deletion restriction message for core models
     *
     * @return bool
     */
    public function isCoreEntity(): bool
    {
        $this->notDeletableMessage = trans(
            'f9web-laravel-deletable::messages.core',
            [
                'model' => get_class($this),
                'id'    => $this->getKey(),
            ]
        );

        return false;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    abstract public function getKey();
}
