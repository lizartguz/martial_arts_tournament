<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlugGeneratorService
{
    /**
     * Ejecuta la operación unique for table del servicio.
     */
    public function uniqueForTable(string $table, string $value, string $column = 'slug', ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'registro';
        $slug = $baseSlug;
        $counter = 2;

        while ($this->exists($table, $column, $slug, $ignoreId)) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Ejecuta la operación unique for model del servicio.
     */
    public function uniqueForModel(Model|string $model, string $value, string $column = 'slug', ?int $ignoreId = null): string
    {
        $table = is_string($model) ? (new $model())->getTable() : $model->getTable();

        return $this->uniqueForTable($table, $value, $column, $ignoreId);
    }

    /**
     * Verifica si el valor ya existe en la tabla indicada.
     */
    private function exists(string $table, string $column, string $slug, ?int $ignoreId): bool
    {
        return DB::table($table)
            ->where($column, $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
