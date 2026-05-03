<?php

namespace Modules\Creditos\Domain\Entities;

class ContextoCreditos
{
    public function __construct(
        private string $nombre,
        private bool $habilitado,
        private string $temaUi,
    ) {
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function habilitado(): bool
    {
        return $this->habilitado;
    }

    public function temaUi(): string
    {
        return $this->temaUi;
    }

    public function aArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'habilitado' => $this->habilitado,
            'temaUi' => $this->temaUi,
        ];
    }
}

