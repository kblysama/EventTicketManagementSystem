<?php

namespace App\Http\Requests;

use App\Enums\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Event::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string'],
            'venue' => ['required', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'event_category_id' => ['required', 'exists:event_categories,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'cover' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'organizer_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'etkinlik adı',
            'description' => 'açıklama',
            'venue' => 'mekân',
            'city' => 'şehir',
            'event_category_id' => 'etkinlik türü',
            'starts_at' => 'başlangıç',
            'ends_at' => 'bitiş',
            'status' => 'yayın durumu',
            'cover' => 'kapak görseli',
        ];
    }

    public function messages(): array
    {
        return [
            'cover.uploaded' => 'Kapak görseli yüklenemedi. Dosya 5 MB’dan küçük ve JPG, PNG veya WebP olmalı.',
            'cover.mimes' => 'Kapak görseli JPG, PNG veya WebP olmalı.',
            'cover.max' => 'Kapak görseli en fazla 5 MB olabilir.',
        ];
    }
}
