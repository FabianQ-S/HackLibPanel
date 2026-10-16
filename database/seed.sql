-- Libros de Lenguajes
INSERT INTO books (titulo, autor, categoria, nivel, version_tecnologia, formato, stock, precio, isbn, anio_publicacion) VALUES
('The Rust Programming Language', 'Steve Klabnik & Carol Nichols', 'lenguajes', 'intermedio', 'Rust 1.70', 'tapa_blanda', 15, 39.99, '978-1718503106', 2023),
('Programming Ruby 3.3', 'Noel Rappin', 'lenguajes', 'intermedio', 'Ruby 3.3', 'tapa_blanda', 10, 49.95, '978-1680509823', 2024),
('Learn You Some Erlang for Great Good!', 'Fred Hébert', 'lenguajes', 'principiante', 'Erlang/OTP 26', 'ebook', 50, 19.99, '978-1593274351', 2013),
('Python Crash Course', 'Eric Matthes', 'lenguajes', 'principiante', 'Python 3.12', 'tapa_blanda', 25, 35.99, '978-1718502703', 2023),
('Effective Java', 'Joshua Bloch', 'lenguajes', 'avanzado', 'Java 21', 'tapa_dura', 8, 54.99, '978-0134685991', 2018),
('Programming PHP', 'Kevin Tatroe & Peter MacIntyre', 'lenguajes', 'intermedio', 'PHP 8.3', 'tapa_blanda', 12, 44.99, '978-1492054139', 2024),
('The Go Programming Language', 'Alan Donovan & Brian Kernighan', 'lenguajes', 'intermedio', 'Go 1.21', 'tapa_dura', 7, 49.99, '978-0134190440', 2015);

-- Libros de Fundamentos
INSERT INTO books (titulo, autor, categoria, nivel, version_tecnologia, formato, stock, precio, isbn, anio_publicacion) VALUES
('Clean Code', 'Robert C. Martin', 'fundamentos', 'intermedio', 'N/A', 'tapa_blanda', 20, 42.99, '978-0132350884', 2008),
('Design Patterns: Elements of Reusable Object-Oriented Software', 'Gang of Four', 'fundamentos', 'avanzado', 'N/A', 'tapa_dura', 5, 59.99, '978-0201633610', 1994),
('Introduction to Algorithms (CLRS)', 'Thomas Cormen et al.', 'fundamentos', 'avanzado', 'N/A', 'tapa_dura', 6, 89.99, '978-0262046305', 2022),
('The Pragmatic Programmer', 'David Thomas & Andrew Hunt', 'fundamentos', 'intermedio', 'N/A', 'tapa_blanda', 18, 49.99, '978-0135957059', 2019),
('Structure and Interpretation of Computer Programs', 'Harold Abelson & Gerald Jay Sussman', 'fundamentos', 'avanzado', 'N/A', 'ebook', 30, 24.99, '978-0262510875', 1996),
('The Linux Command Line', 'William Shotts', 'fundamentos', 'principiante', 'Linux 6.x', 'tapa_blanda', 14, 29.99, '978-1593279523', 2019),
('Grokking Algorithms', 'Aditya Bhargava', 'fundamentos', 'principiante', 'N/A', 'tapa_blanda', 22, 34.99, '978-1617292231', 2016);

-- Libros de Frameworks & Herramientas
INSERT INTO books (titulo, autor, categoria, nivel, version_tecnologia, formato, stock, precio, isbn, anio_publicacion) VALUES
('Agile Web Development with Rails 8', 'Sam Ruby & Dave Thomas', 'frameworks', 'intermedio', 'Rails 8', 'tapa_blanda', 10, 49.95, '978-1680509878', 2024),
('Programming Phoenix LiveView', 'Bruce Tate & Sophie DeBenedetto', 'frameworks', 'intermedio', 'Phoenix 1.7', 'ebook', 40, 29.99, '978-1680508215', 2023),
('Laravel Up & Running', 'Matt Stauffer', 'frameworks', 'intermedio', 'Laravel 11', 'tapa_blanda', 13, 44.99, '978-1098153267', 2024),
('Docker Deep Dive', 'Nigel Poulton', 'frameworks', 'principiante', 'Docker 25', 'ebook', 35, 22.99, '978-1916585256', 2024),
('Kubernetes in Action', 'Marko Lukša', 'frameworks', 'avanzado', 'K8s 1.29', 'tapa_dura', 4, 69.99, '978-1617297618', 2024),
('Two Scoops of Django', 'Daniel & Audrey Feldroy', 'frameworks', 'intermedio', 'Django 5.0', 'tapa_blanda', 9, 44.95, '978-0692915899', 2024),
('Pro Git', 'Scott Chacon & Ben Straub', 'frameworks', 'principiante', 'Git 2.43', 'ebook', 100, 0.00, '978-1484200773', 2024);
