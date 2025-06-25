<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    protected $guarded = ['id'];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'domestic_related_documents' => 'array',
            'international_quality_certification' => 'array',
            'international_safety_certification' => 'array',
            'principal_checklist' => 'array',
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'principalId';
    }
}
