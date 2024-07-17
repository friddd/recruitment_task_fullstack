<?php
namespace App\Entity;

use JsonSerializable;

class Currency implements JsonSerializable
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string|null
     */
    private $name;

    public function __construct(string $code, ?string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
        ];
    }
}