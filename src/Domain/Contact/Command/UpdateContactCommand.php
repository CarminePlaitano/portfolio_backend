<?php

namespace App\Domain\Contact\Command;

class UpdateContactCommand
{
    private int $id;
    private string $type;
    private string $value;
    private ?string $label;

    public function __construct(int $id, string $type, string $value, ?string $label)
    {
        $this->id = $id;
        $this->type = $type;
        $this->value = $value;
        $this->label = $label;
    }

    public function getId(): int
    {
        return $this->id;
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
