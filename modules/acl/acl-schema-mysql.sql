DROP TABLE IF EXISTS privileges;
CREATE TABLE privileges (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  alias VARCHAR(32) NOT NULL,
  name VARCHAR(64) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 8
AVG_ROW_LENGTH = 2730
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Привилегии ACL';


DROP TABLE IF EXISTS resources;
CREATE TABLE resources (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  alias VARCHAR(64) NOT NULL,
  name VARCHAR(128) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'Ресурсы ACL';


DROP TABLE IF EXISTS roles_users;
CREATE TABLE roles_users (
  user_id INT(10) UNSIGNED NOT NULL,
  role_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, role_id),
  INDEX fk_role_id (role_id),
  CONSTRAINT roles_users_ibfk_1 FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT roles_users_ibfk_2 FOREIGN KEY (role_id)
    REFERENCES roles(id) ON DELETE CASCADE ON UPDATE RESTRICT
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;


DROP TABLE IF EXISTS resources_privileges;
CREATE TABLE resources_privileges (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  resource_id INT(11) UNSIGNED NOT NULL,
  privilege_id INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT FK_resoruces_privileges_privileges_id FOREIGN KEY (privilege_id)
    REFERENCES privileges(id) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT FK_resoruces_privileges_resources_id1 FOREIGN KEY (resource_id)
    REFERENCES resources(id) ON DELETE CASCADE ON UPDATE NO ACTION
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8
COLLATE utf8_general_ci;


DROP TABLE IF EXISTS roles_resources_privileges;
CREATE TABLE roles_resources_privileges (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  resource_privilegy_id INT(10) UNSIGNED NOT NULL,
  role_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT FK_roles_resources_privileges_resources_privileges_id FOREIGN KEY (resource_privilegy_id)
    REFERENCES resources_privileges(id) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT FK_roles_resources_privileges_roles_id FOREIGN KEY (role_id)
    REFERENCES roles(id) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE = INNODB
AUTO_INCREMENT = 2
AVG_ROW_LENGTH = 16384
CHARACTER SET utf8
COLLATE utf8_general_ci;

INSERT INTO privileges VALUES
(1, 'read', 'чтение'),
(3, 'create', 'создание'),
(4, 'update', 'обновление'),
(5, 'delete', 'удаление'),
(6, 'admin', 'полный доступ'),
(7, 'login', 'Логин');