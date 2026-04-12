<?php

class Recette
{
    public ?int $id;
    public string $nom;
    public string $description;
    public ?string $image;
    public int $temps_preparation;
    public string $difficulte;
    public ?string $date_creation;

    public function __construct(
        ?int $id,
        string $nom,
        string $description,
        ?string $image = null,
        int $temps_preparation = 0,
        string $difficulte = 'facile',
        ?string $date_creation = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->image = $image;
        $this->temps_preparation = $temps_preparation;
        $this->difficulte = $difficulte;
        $this->date_creation = $date_creation;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['nom'] ?? '',
            $data['description'] ?? '',
            $data['image'] ?? null,
            (int) ($data['temps_preparation'] ?? 0),
            $data['difficulte'] ?? 'facile',
            $data['date_creation'] ?? null
        );
    }
}
