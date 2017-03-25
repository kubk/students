DROP TABLE IF EXISTS students;
DROP TYPE IF EXISTS genderEnum;

CREATE TYPE genderEnum AS ENUM ('f', 'm');

CREATE TABLE students (
  id      SERIAL PRIMARY KEY,
  name    VARCHAR(25)  NOT NULL,
  surname VARCHAR(30)  NOT NULL,
  email   VARCHAR(60)  NOT NULL,
  gender  genderEnum   NOT NULL,
  "group" VARCHAR(5)   NOT NULL,
  rating  INT          NOT NULL,
  token   VARCHAR(255) NOT NULL,
  CONSTRAINT email UNIQUE (email),
  CONSTRAINT email CHECK (email ~* '^[^@]+@[^@]+$'),
  CONSTRAINT token UNIQUE (token),
  CONSTRAINT name CHECK (name ~* '^[-а-яёa-zА-ЯЁA-Z\s]{1,20}$'),
  CONSTRAINT surname CHECK (surname ~* '^[-''а-яёa-zА-ЯЁA-Z\s]{1,20}$'),
  CONSTRAINT "group" CHECK ("group" ~* '^[а-яёa-zА-ЯЁA-Z0-9]{2,5}$'),
  CONSTRAINT rating_constraint CHECK (rating <= 200 AND rating >= 0)
);

COMMENT ON COLUMN students.token IS 'Registration token';
