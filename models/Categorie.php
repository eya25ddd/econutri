<?php

class Categorie
{
    private int $id;
    private string $nom;

    public function __construct(
        int $id = 0,
        string $nom = ''
    ) {
        $this->id = $id;
        $this->nom = $nom;
    }

    /* ─── GETTERS ─────────────────────────────── */
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    /* ─── SETTERS ─────────────────────────────── */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /* ─── MAGIC GETTERS (for backward compatibility) ── */
    public function __get(string $name)
    {
        return match($name) {
            'id' => $this->id,
            'nom' => $this->nom,
            default => null
        };
    }

    public function __set(string $name, $value): void
    {
        match($name) {
            'id' => $this->id = $value,
            'nom' => $this->nom = $value,
            default => null
        };
    }

    /**
     * Create a Categorie instance from an associative array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            nom: (string) ($data['nom'] ?? '')
        );
    }

    /**
     * Convert the Categorie instance to an associative array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
        ];
    }
}
