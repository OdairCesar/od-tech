<?php

namespace App\Exceptions;

use RuntimeException;

final class AiGenerationException extends RuntimeException
{
    public static function invalidResponseShape(): self
    {
        return new self('A resposta da IA não contém os campos esperados.');
    }

    public static function invalidImageResponse(): self
    {
        return new self('A resposta de geração de imagem da IA não contém uma imagem válida.');
    }
}
