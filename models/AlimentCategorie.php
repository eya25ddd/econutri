<?php

class AlimentCategorie
{
    private int $aliment_id;
    private int $categorie_id;

    public function __construct(
        int $aliment_id = 0,
        int $categorie_id = 0
    ) {
        $this->aliment_id = $aliment_id;
        $this->categorie_id = $categorie_id;
    }

    /* ─── GETTERS ─────────────────────────────── */
    public function getAlimentId(): int
    {
        return $this->aliment_id;
    }

    public function getCategorieId(): int
    {
        return $this->categorie_id;
    }

    /* ─── SETTERS ─────────────────────────────── */
    public function setAlimentId(int $aliment_id): void
    {
        $this->aliment_id = $aliment_id;
    }

    public function setCategorieId(int $categorie_id): void
    {
        $this->categorie_id = $categorie_id;
    }

    /* ─── MAGIC GETTERS (for backward compatibility) ── */
    public function __get(string $name)
    {
        return match($name) {
            'aliment_id' => $this->aliment_id,
            'categorie_id' => $this->categorie_id,
            default => null
        };
    }

    public function __set(string $name, $value): void
    {
        match($name) {
            'aliment_id' => $this->aliment_id = $value,
            'categorie_id' => $this->categorie_id = $value,
            default => null
        };
    }

    /**
     * Create an AlimentCategorie instance from an associative array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            aliment_id: (int) ($data['aliment_id'] ?? 0),
            categorie_id: (int) ($data['categorie_id'] ?? 0)
        );
    }

    /**
     * Convert the AlimentCategorie instance to an associative array
     */
    public function toArray(): array
    {
        return [
            'aliment_id' => $this->aliment_id,
            'categorie_id' => $this->categorie_id,
        ];
    }
}
