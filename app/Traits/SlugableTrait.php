<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait SlugableTrait
{
    /**
     * Sign on model events.
     */
    public static function bootSlugableTrait(): void
    {
        static::saving(function (Model $model): void {
            if (!$model->{$model->getSlugColumn()}) {
                $model->{$model->getSlugColumn()} = $model->slugify();
            }
        });
    }

    /**
     * Creates the unique slug.
     */
    public function slugify(): string
    {
        // Prepare a text for the slug
        $text = implode($this->getSlugSeparator(), array_map(function ($field) {
            return $this->{$field};
        }, $this->getSlugFields()));

        // Normalize the text
        $slug = Str::slug($text, $this->getSlugSeparator());

        // Find similar slugs
        $similarSlugs = $this->getSimilarSlugs($slug);

        // Check if we haven't used it before
        $finalSlug = $slug;
        $i = 1;

        while ($similarSlugs->contains($this->getSlugColumn(), $finalSlug)) {
            $finalSlug = $slug . $this->getSlugSeparator() . $i++;
        }

        // Set the slug
        $this->{$this->getSlugColumn()} = $finalSlug;

        return $finalSlug;
    }

    /**
     * Find slugs that start with the given value and do not belong to the object.
     * @param string $slug
     * @return Collection
     */
    protected function getSimilarSlugs(string $slug): Collection
    {
        $query = self::select($this->getSlugColumn())->where($this->getSlugColumn(), 'LIKE', $slug . '%')
            ->where($this->primaryKey, '!=', $this->{$this->primaryKey});

        if (in_array(SoftDeletes::class, class_uses($this))) {
            $query->withTrashed();
        }

        return $query->get();
    }

    /**
     * Get the separator of the slug.
     */
    public function getSlugSeparator(): string
    {
        return defined('static::SLUG_SEPARATOR') ? static::SLUG_SEPARATOR : '-';
    }

    /**
     * Get the name of the "slug" column.
     */
    public function getSlugColumn(): string
    {
        return defined('static::SLUG_COLUMN') ? static::SLUG_COLUMN : 'slug';
    }

    /**
     * Get attributes forming the slug.
     */
    public function getSlugFields(): array
    {
        return defined('static::SLUG_BASE') ? [static::SLUG_BASE] : [];
    }
}
