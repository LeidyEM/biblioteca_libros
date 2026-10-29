# CRUD de biblioteca_libros

API REST de libros con Slim en la capa de Negocio y Eloquent en la capa de Datos.
La base de datos se inicializa desde `db_dump/biblioteca.sql`.

## Iniciar

Desde esta carpeta, ejecutar:

```sh
docker compose up --build
```

La API de Negocio queda disponible en `http://localhost:9090/api/libros`.
Compose define únicamente los servicios `lemnegocio`, `lemdatos` y `lemdb`;
la capa de Datos solo es accesible desde la red interna. MySQL conserva sus
datos en el volumen `lem_mysql_data`.

## Endpoints

| Método | Ruta | Resultado |
|---|---|---|
| GET | `/api/libros` | Lista libros |
| GET | `/api/libros/{id}` | Obtiene un libro |
| POST | `/api/libros` | Crea un libro |
| PUT | `/api/libros/{id}` | Actualiza un libro |
| DELETE | `/api/libros/{id}` | Elimina un libro |

El cuerpo de POST y PUT debe incluir `isbn`, `titulo`, `autor`,
`anio_publicacion` y `ejemplares_disponibles`.
