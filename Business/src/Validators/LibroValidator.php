<?php

declare(strict_types=1);

namespace App\Validators;

use Respect\Validation\ValidatorBuilder as v;

final class LibroValidator
{
    private function reglas(): array {
        return [
            'isbn' => [
                'regla' => v::stringType()
                    ->notBlank()
                    ->length(v::between(1, 30)),
                'mensaje' => 'El ISBN es obligatorio y debe tener entre 1 y 30 caracteres.',
            ],
            'titulo' => [
                'regla' => v::stringType()
                    ->notBlank()
                    ->length(v::between(1, 150)),
                'mensaje' => 'El título es obligatorio y debe tener entre 1 y 150 caracteres.',
            ],
            'autor' => [
                'regla' => v::stringType()
                    ->notBlank()
                    ->length(v::between(1, 100)),
                'mensaje' => 'El autor es obligatorio y debe tener entre 1 y 100 caracteres.',
            ],
            'anio_publicacion' => [
                'regla' => v::intType()->between(1, 2147483647),
                'mensaje' => 'El año de publicación debe ser un entero positivo.',
            ],
            'ejemplares_disponibles' => [
                'regla' => v::intType()->between(0, 2147483647),
                'mensaje' => 'Los ejemplares disponibles deben ser un entero igual o mayor que cero.',
            ],
        ];
    }

    public function validate(array $data): array {
        $errores = [];

        foreach ($this->reglas() as $campo => $config) {
            $valor = $data[$campo] ?? null;
            $regla = $config['regla'];
            $mensaje = $config['mensaje'];

            if ($regla->validate($valor)->hasFailed()) {
                $errores[$campo] = $mensaje;
            }
        }

        return $errores;
    }
}
