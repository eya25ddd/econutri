<?php

class RecetteCategorie
{
    private int $recette_id;
    private int $categorie_id;

    public function __construct(
        int $recette_id = 0,
        int $categorie_id = 0
    ) {
        $this->recette_id = $recette_id;
        $this->categorie_id = $categorie_id;
    }

    /* ─── GETTERS ─────────────────────────────── */
    public function getRecetteId(): int
    {
        return $this->recette_id;
    }

    public function getCategorieId(): int
    {
        return $this->categorie_id;
    }

    /* ─── SETTERS ─────────────────────────────── */
    public function setRecetteId(int $recette_id): void
    {
        $this->recette_id = $recette_id;
    }

    public function setCategorieId(int $categorie_id): void
    {
        $this->categorie_id = $categorie_id;
    }

    /* ─── MAGIC GETTERS (for backward compatibility) ── */
    public function __get(string $name)
    {
        return match($name) {
            'recette_id' => $this->recette_id,
            'categorie_id' => $this->categorie_id,
            default => null
        };
    }

    public function __set(string $name, $value): void
    {
        match($name) {
            'recette_id' => $this->recette_id = $value,
            'categorie_id' => $this->categorie_id = $value,
            default => null
        };
    }

    /**
     * Create a RecetteCategorie instance from an associative array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            recette_id: (int) ($data['recette_id'] ?? 0),
            categorie_id: (int) ($data['categorie_id'] ?? 0)
        );
    }

    /**
     * Convert the RecetteCategorie instance to an associative array
     */
    public function toArray(): array
    {
        return [
            'recette_id' => $this->recette_id,
            'categorie_id' => $this->categorie_id,
        ];
    }
}
