<?php

class RecetteAliment
{
    public ?int $id;
    public int $recette_id;
    public int $aliment_id;
    public ?string $quantite;

    public function __construct(
        ?int $id,
        int $recette_id,
        int $aliment_id,
        ?string $quantite = null
    ) {
        $this->id = $id;
        $this->recette_id = $recette_id;
        $this->aliment_id = $aliment_id;
        $this->quantite = $quantite;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            (int) ($data['recette_id'] ?? 0),
            (int) ($data['aliment_id'] ?? 0),
            $data['quantite'] ?? null
        );
    }
}
