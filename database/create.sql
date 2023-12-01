DROP TABLE IF EXISTS students;
CREATE TABLE students(
    id INTEGER UNSIGNED NOT NULL UNIQUE PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    class_code VARCHAR(255) NOT NULL,
    grade INTEGER NOT NULL,
    register VARCHAR(255) NOT NULL,
    CONSTRAINT register_check CHECK (register in ("재학", "휴학", "졸업")),
    CONSTRAINT grade_check CHECK (grade >= 1 AND grade <= 4)
) engine=InnoDB;
