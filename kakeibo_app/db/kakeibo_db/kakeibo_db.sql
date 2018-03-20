DROP DATABASE IF EXISTS kakeibo_db;
CREATE DATABASE kakeibo_db;
USE kakeibo_db;
CREATE TABLE budget_table(id int AUTO_INCREMENT NOT NULL PRIMARY KEY, update_at timestamp, user_id varchar(100), budget_name varchar(100));