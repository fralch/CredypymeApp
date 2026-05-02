<?php

namespace Modules\Logistica\Domain\Entities;

class LogisticaContext
{
    public function __construct(
        private string $name,
        private bool $enabled,
        private string $uiTheme,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function uiTheme(): string
    {
        return $this->uiTheme;
    }

    /**
     * @return array<string, bool|string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'enabled' => $this->enabled,
            'uiTheme' => $this->uiTheme,
        ];
    }
}
