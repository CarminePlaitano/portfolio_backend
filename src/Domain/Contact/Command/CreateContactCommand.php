<?php

namespace App\Domain\Contact\Command;

class CreateContactCommand
{
    private string $type;
    private string $value;
    private ?string $label;

    public function __construct(string $type, string $value, ?string $label)
    {
        $this->type = $type;
        $this->value = $value;
        $this->label = $label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
