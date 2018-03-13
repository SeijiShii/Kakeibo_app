DROP DATABASE IF EXISTS user_db;
CREATE DATABASE user_db;
USE user_db;
CREATE TABLE user_table (id int AUTO_INCREMENT NOT NULL PRIMARY KEY, update_at timestamp, user_id varchar(100), user_name varchar(100), password varchar(100), fasebook_id varchar(100), google_id varchar(100), twitter_id varchar(100), line_id varchar(100));
