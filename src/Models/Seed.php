<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

use function Garden\BSON_array_to_array;

class Seed extends DBRecord
{
    public \DateTimeImmutable $added;
    public string $type;
    public string $common_name;
    public string $variety;
    public int $days_to_maturity;
    public int $days_to_germination;
    public bool $is_heirloom;
    public string $sun;
    public array $season;
    public array $characteristics;
    public bool $is_hybrid;
    public string $source;
    public string $link;
    public string $notes;
    public bool $on_wishlist;
    public array $tags;

    public function display_string(): string
    {
        return "{$this->common_name} - {$this->variety}";
    }

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->added = new \DateTimeImmutable($record['added']);
        $this->type = $record['type'];
        $this->common_name = $record['common_name'];
        $this->variety = $record['variety'];
        $this->days_to_maturity = $record['days_to_maturity'];
        $this->days_to_germination = $record['days_to_germination'];
        $this->is_heirloom = $record['is_heirloom'];
        $this->sun = $record['sun'];
        $this->season = BSON_array_to_array($record['season']);
        $this->characteristics = BSON_array_to_array($record['characteristics']);
        $this->is_hybrid = $record['is_hybrid'];
        $this->source = $record['source'];
        $this->link = $record['link'];
        $this->notes = $record['notes'];
        $this->on_wishlist = $record['on_wishlist'];
        $this->tags = BSON_array_to_array($record['custom_tags']);
    }

    public function to_array(): array
    {
        sort($this->tags, \SORT_NATURAL | \SORT_FLAG_CASE);

        return [
            'added' => $this->added->format('Y-m-d'),
            'type' => $this->type,
            'common_name' => $this->common_name,
            'variety' => $this->variety,
            'days_to_maturity' => $this->days_to_maturity,
            'days_to_germination' => $this->days_to_germination,
            'is_heirloom' => $this->is_heirloom,
            'sun' => $this->sun,
            'season' => $this->season,
            'characteristics' => $this->characteristics,
            'is_hybrid' => $this->is_hybrid,
            'source' => $this->source,
            'link' => $this->link,
            'notes' => $this->notes,
            'on_wishlist' => $this->on_wishlist,
            'custom_tags' => array_map(
                fn($value): string => strtolower($value),
                $this->tags
            ),
        ];
    }
}
