<?php

return [
    'uploaded' => ':attribute yüklenemedi. Dosya çok büyük olabilir veya geçici olarak alınamadı.',
    'mimes' => ':attribute şu türlerden biri olmalı: :values.',
    'image' => ':attribute bir görsel olmalı.',
    'max' => [
        'file' => ':attribute en fazla :max KB olabilir.',
        'numeric' => ':attribute en fazla :max olabilir.',
        'string' => ':attribute en fazla :max karakter olabilir.',
        'array' => ':attribute en fazla :max öğe içerebilir.',
    ],
    'required' => ':attribute alanı zorunludur.',
    'exists' => 'Seçilen :attribute geçersiz.',
    'after' => ':attribute, :date tarihinden sonra olmalı.',
];
