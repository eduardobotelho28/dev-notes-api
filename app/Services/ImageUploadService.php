<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageUploadService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_SIZE_KB = 2048; // 2MB
    private const DISK = 'public';
    private const DIRECTORY = 'concepts';

    public function store(UploadedFile $file): string
    {
        $this->validate($file);

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid() . '.' . $extension;

        $path = $file->storeAs(self::DIRECTORY, $filename, self::DISK);

        if (!$path) {
            throw new RuntimeException('Falha ao salvar imagem.');
        }

        return $path;
    }

    public function delete(string $path): void
    {
        Storage::disk(self::DISK)->delete($path);
    }

    private function validate(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new RuntimeException('Arquivo inválido ou corrompido.');
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw new RuntimeException('Formato de imagem não permitido.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException('Extensão de arquivo não permitida.');
        }

        if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
            throw new RuntimeException('Imagem excede o tamanho máximo de ' . self::MAX_SIZE_KB . 'KB.');
        }
    }
}