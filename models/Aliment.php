<?php

class Aliment
{
    public ?int $id;
    public string $nom;
    public int $calories;
    public float $proteines;
    public float $glucides;
    public float $lipides;
    public ?string $image;

    public function __construct(
        ?int $id,
        string $nom,
        int $calories = 0,
        float $proteines = 0.0,
        float $glucides = 0.0,
        float $lipides = 0.0,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->calories = $calories;
        $this->proteines = $proteines;
        $this->glucides = $glucides;
        $this->lipides = $lipides;
        $this->image = $image;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nom'] ?? '',
            (int) ($data['calories'] ?? 0),
            (float) ($data['proteines'] ?? 0.0),
            (float) ($data['glucides'] ?? 0.0),
            (float) ($data['lipides'] ?? 0.0),
            $data['image'] ?? null
        );
    }
}
