<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CopyInput extends Component
{
    public function __construct(
        public string $label,
        public string $id,
        public string $name,
        public string $value,
        public string $placeholder,
        public string $tooltip = 'Copiar',
        public ?string $description = null,
    ) {
    }

    public function render(): View
    {
        return view('components.copy-input');
    }
}
