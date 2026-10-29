CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(30) UNIQUE NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    anio_publicacion INT NOT NULL,
    ejemplares_disponibles INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);