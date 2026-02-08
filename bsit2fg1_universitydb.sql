USE bsit2fg1_universitydb;

CREATE TABLE college (
    college_code VARCHAR(10) PRIMARY KEY,
    college_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE program (
    program_code VARCHAR(15) PRIMARY KEY,
    program_name VARCHAR(100) NOT NULL,
    college_code VARCHAR(10),
    FOREIGN KEY (college_code) REFERENCES college(college_code)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE student (
    student_number INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(20),
    last_name VARCHAR(50) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    birthday DATE NOT NULL,
    details TEXT,
    program_code VARCHAR(15),
    FOREIGN KEY (program_code) REFERENCES program(program_code)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE course (
    course_code VARCHAR(10) PRIMARY KEY,
    course_title VARCHAR(100) NOT NULL,
    units INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE grade (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    semester VARCHAR(20) NOT NULL,
    school_year VARCHAR(9) NOT NULL,
    grade DECIMAL(4,2) NOT NULL,
    student_number INT,
    course_code VARCHAR(10),
    FOREIGN KEY (student_number) REFERENCES student(student_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (course_code) REFERENCES course(course_code)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CHECK (grade >= 1.0 AND grade <= 5.0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO college (college_code, college_name) VALUES
('CCS', 'College of Computer Studies'),
('COE', 'College of Engineering'),
('CBA', 'College of Business Administration'),
('CAS', 'College of Arts and Sciences');

INSERT INTO program (program_code, program_name, college_code) VALUES
('BSIT', 'Bachelor of Science in Information Technology', 'CCS'),
('BSCS', 'Bachelor of Science in Computer Science', 'CCS'),
('BSCE', 'Bachelor of Science in Civil Engineering', 'COE'),
('BSEE', 'Bachelor of Science in Electrical Engineering', 'COE'),
('BSBA', 'Bachelor of Science in Business Administration', 'CBA'),
('BSA', 'Bachelor of Science in Accountancy', 'CBA');

INSERT INTO course (course_code, course_title, units) VALUES
('IT101', 'Introduction to Information Technology', 3),
('IT102', 'Computer Programming 1', 3),
('IT103', 'Data Structures and Algorithms', 3),
('IT104', 'Database Management Systems', 3),
('IT105', 'Web Development', 3),
('MATH101', 'College Algebra', 3),
('ENG101', 'English Communication', 3),
('PE101', 'Physical Education 1', 2);

INSERT INTO student (student_number, first_name, middle_name, last_name, gender, birthday, details, program_code) VALUES
(2021001001, 'Juan', 'Cruz', 'Dela Cruz', 'Male', '2003-05-15', 'Dean\'s Lister', 'BSIT'),
(2021001002, 'Maria', 'Santos', 'Reyes', 'Female', '2003-08-22', 'Scholar', 'BSIT'),
(2021002001, 'Pedro', 'Garcia', 'Lopez', 'Male', '2002-11-30', NULL, 'BSCS'),
(2021003001, 'Ana', 'Marie', 'Torres', 'Female', '2003-02-14', 'Athlete', 'BSCE');

INSERT INTO grade (semester, school_year, grade, student_number, course_code) VALUES
('1st Semester', '2023-2024', 1.25, 2021001001, 'IT101'),
('1st Semester', '2023-2024', 1.50, 2021001001, 'IT102'),
('1st Semester', '2023-2024', 1.75, 2021001001, 'MATH101'),
('1st Semester', '2023-2024', 1.00, 2021001002, 'IT101'),
('1st Semester', '2023-2024', 1.25, 2021001002, 'IT102'),
('1st Semester', '2023-2024', 1.50, 2021002001, 'IT101'),
('1st Semester', '2023-2024', 2.00, 2021003001, 'MATH101');

CREATE INDEX idx_program_college ON program(college_code);
CREATE INDEX idx_student_program ON student(program_code);
CREATE INDEX idx_grade_student ON grade(student_number);
CREATE INDEX idx_grade_course ON grade(course_code);