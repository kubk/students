DROP TABLE IF EXISTS students;
DROP TYPE IF EXISTS genderEnum;

CREATE TYPE genderEnum AS ENUM ('f', 'm');
CREATE TABLE students (
  id      SERIAL PRIMARY KEY,
  name    VARCHAR(25)  NOT NULL,
  surname VARCHAR(30)  NOT NULL,
  email   VARCHAR(60)  NOT NULL,
  gender genderEnum NOT NULL,
  "group" VARCHAR(5)   NOT NULL,
  rating  INT          NOT NULL,
  token   VARCHAR(255) NOT NULL, -- Registration token
  CONSTRAINT email UNIQUE (email),
  CONSTRAINT token UNIQUE (token),
  CONSTRAINT rating_constraint CHECK (rating <= 200 AND rating > 0)
);
