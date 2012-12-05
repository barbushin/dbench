DROP SCHEMA public CASCADE;
CREATE SCHEMA public; 

CREATE SEQUENCE user_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE TABLE users
(
  id bigint NOT NULL DEFAULT nextval('user_id_seq'::regclass),
  "login" character varying(255) NOT NULL,
  "password" character varying(32) NOT NULL,
  is_active boolean NOT NULL DEFAULT false,
  CONSTRAINT users_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
CREATE INDEX users_login ON users ("login");


CREATE SEQUENCE photo_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE TABLE photos
(
  id bigint NOT NULL DEFAULT nextval('photo_id_seq'::regclass),
  "user_id" bigint NOT NULL, 
  "file" character varying(255) NOT NULL,
  "name" character varying(32) NOT NULL,
  CONSTRAINT photos_pkey PRIMARY KEY (id),
  CONSTRAINT photos_user_id FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
CREATE INDEX photos_user_id ON photos ("user_id");
CREATE INDEX photos_files ON photos ("file");


CREATE SEQUENCE comments_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
CREATE TABLE photos_comments
(
  id bigint NOT NULL DEFAULT nextval('comments_id_seq'::regclass),
  "photo_id" bigint NOT NULL, 
  "user_id" bigint NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(), 
  "text" text NOT NULL,
  CONSTRAINT comments_pkey PRIMARY KEY (id),
  CONSTRAINT comments_user_id FOREIGN KEY (user_id)
      REFERENCES users (id) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT comments_photo_id FOREIGN KEY (photo_id)
      REFERENCES photos (id) MATCH FULL
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
CREATE INDEX comments_user_id ON photos_comments ("user_id");
CREATE INDEX comments_photo_id ON photos_comments ("photo_id")