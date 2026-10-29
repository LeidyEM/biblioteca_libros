<?php

namespace App\controllers;

use App\Models\Libro as LibroModel;
use Illuminate\Database\QueryException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Libro
{
    public function __construct(private ContainerInterface $container){
    }

    public function read(Request $request, Response $response, array $args): Response {
        $eloquent = $this->container->get('eloquent');

        if (isset($args['id'])) {
            $libro = LibroModel::query()
                ->select(['id','isbn','titulo','autor','anio_publicacion','ejemplares_disponibles','fecha_registro'])
                ->find($args['id']);

            if ($libro === null) {
                return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
            }

            return $this->json($response, ['data' => $libro], 200);
        }

        $libros = LibroModel::query()
            ->select(['id','isbn','titulo','autor','anio_publicacion','ejemplares_disponibles','fecha_registro'])
            ->get()
            ->toArray();

        return $this->json($response, ['data' => $libros], 200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        $errors = [];
        $isbn = trim((string) ($data['isbn'] ?? ''));
        $titulo = trim((string) ($data['titulo'] ?? ''));
        $autor = trim((string) ($data['autor'] ?? ''));
        $anioPublicacion = filter_var($data['anio_publicacion'] ?? null, FILTER_VALIDATE_INT);
        $ejemplaresDisponibles = filter_var($data['ejemplares_disponibles'] ?? null, FILTER_VALIDATE_INT);
        
        if ($isbn === '' || mb_strlen($isbn) > 30) {
            $errors['isbn'] = 'Es obligatorio y debe tener máximo 30 caracteres.';
        }
        if ($titulo === '' || mb_strlen($titulo) > 150) {
            $errors['titulo'] = 'Es obligatorio y debe tener máximo 150 caracteres.';
        }
        if ($autor === '' || mb_strlen($autor) > 100) {
            $errors['autor'] = 'Es obligatorio y debe tener máximo 100 caracteres.';
        }
        if ($anioPublicacion === false || $anioPublicacion < 1 || $anioPublicacion > 2147483647) {
            $errors['anio_publicacion'] = 'Debe ser un entero positivo.';
        }
        if ($ejemplaresDisponibles === false || $ejemplaresDisponibles < 0 || $ejemplaresDisponibles > 2147483647) {
            $errors['ejemplares_disponibles'] = 'Debe ser un entero igual o mayor que cero.';
        }

        if ($errors !== []) {
            return $this->json($response, ['errors' => $errors], 422);
        }

        $eloquent = $this->container->get('eloquent');

        try {
            $libro = LibroModel::create([
                'isbn' => $isbn,
                'titulo' => $titulo,
                'autor' => $autor,
                'anio_publicacion' => $anioPublicacion,
                'ejemplares_disponibles' => $ejemplaresDisponibles,
            ]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return $this->json($response, ['error' => 'El ISBN ya está registrado.'], 409);
            }

            throw $exception;
        }

        return $this->json($response, ['data' => $libro->refresh()->toArray()], 201);
    }

    public function update(Request $request, Response $response, array $args): Response{
        $errors = [];
        $eloquent = $this->container->get('eloquent');
        if (isset($args['id'])) {
            $libro = LibroModel::find($args['id']);
            if ($libro === null) {
                return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
            }

            $data = $request->getParsedBody();
            $data = is_array($data) ? $data : [];

            if (isset($data['isbn'])) {
                $isbn = trim((string) $data['isbn']);
                if ($isbn === '' || mb_strlen($isbn) > 30) {
                    return $this->json($response, ['error' => 'ISBN inválido.'], 422);
                }
                $libro->isbn = $isbn;
            }

            if (isset($data['titulo'])) {
                $titulo = trim((string) $data['titulo']);
                if ($titulo === '' || mb_strlen($titulo) > 150) {
                    return $this->json($response, ['error' => 'Título inválido.'], 422);
                }
                $libro->titulo = $titulo;
            }

            if (isset($data['autor'])) {
                $autor = trim((string) $data['autor']);
                if ($autor === '' || mb_strlen($autor) > 100) {
                    return $this->json($response, ['error' => 'Autor inválido.'], 422);
                }
                $libro->autor = $autor;
            }

            if (isset($data['anio_publicacion'])) {
                $anioPublicacion = filter_var($data['anio_publicacion'], FILTER_VALIDATE_INT);

                if ($anioPublicacion === false || $anioPublicacion < 1 || $anioPublicacion > 2147483647) {
                    $errors['anio_publicacion'] = 'Debe ser un entero positivo.';
                } else {
                    $libro->anio_publicacion = $anioPublicacion;
                }
            }

            if (isset($data['ejemplares_disponibles'])) {
                $ejemplaresDisponibles = filter_var($data['ejemplares_disponibles'], FILTER_VALIDATE_INT);

                if ($ejemplaresDisponibles === false || $ejemplaresDisponibles < 0 || $ejemplaresDisponibles > 2147483647) {
                    $errors['ejemplares_disponibles'] = 'Debe ser un entero igual o mayor que cero.';
                } else {
                    $libro->ejemplares_disponibles = $ejemplaresDisponibles;
                }
            }

            if (!empty($errors)) {
                return $this->json($response, ['errors' => $errors], 422);
            }

            $libro->save();

            return $this->json($response, ['data' => $libro->toArray()], 200);
        }

        return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $eloquent = $this->container->get('eloquent');
        if (isset($args['id'])) {
            $libro = LibroModel::find($args['id']);

            if ($libro === null) {
                return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
            }

            $libro->delete();

            return $this->json($response, ['message' => 'Libro eliminado exitosamente.'], 200);
        }

        return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
    }

    private function json(Response $response, array $payload, int $status): Response {
        $response->getBody()->write(json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ));

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }
}
