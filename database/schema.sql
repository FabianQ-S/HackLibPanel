CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('master_admin', 'admin', 'user')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS books (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    autor TEXT NOT NULL,
    categoria TEXT NOT NULL CHECK(categoria IN ('lenguajes', 'fundamentos', 'frameworks')),
    nivel TEXT NOT NULL CHECK(nivel IN ('principiante', 'intermedio', 'avanzado')),
    version_tecnologia TEXT DEFAULT '',
    formato TEXT NOT NULL CHECK(formato IN ('tapa_blanda', 'tapa_dura', 'ebook')),
    stock INTEGER NOT NULL DEFAULT 0,
    precio REAL NOT NULL DEFAULT 0.0,
    isbn TEXT UNIQUE,
    anio_publicacion INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rentals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    codigo TEXT NOT NULL UNIQUE,
    estado TEXT NOT NULL DEFAULT 'pendiente' CHECK(estado IN ('pendiente', 'aprobado', 'prestado', 'devuelto', 'cancelado')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

CREATE INDEX IF NOT EXISTS idx_books_categoria ON books(categoria);
CREATE INDEX IF NOT EXISTS idx_books_nivel ON books(nivel);
CREATE INDEX IF NOT EXISTS idx_rentals_estado ON rentals(estado);
CREATE INDEX IF NOT EXISTS idx_rentals_codigo ON rentals(codigo);
