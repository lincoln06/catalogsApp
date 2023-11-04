CREATE TABLE catalog
(
    id         INT AUTO_INCREMENT NOT NULL,
    system_id  INT          NOT NULL,
    name       VARCHAR(255) NOT NULL,
    date_added DATE         NOT NULL,
    pdf_file   VARCHAR(255) NOT NULL,
    INDEX      IDX_1B2C3247D0952FA5 (system_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE user
(
    id       INT AUTO_INCREMENT NOT NULL,
    email    VARCHAR(180) NOT NULL,
    roles    LONGTEXT     NOT NULL COMMENT '(DC2Type:json)',
    password VARCHAR(255) NOT NULL,
    UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE report
(
    id          INT AUTO_INCREMENT NOT NULL,
    category_id INT          NOT NULL,
    report_from VARCHAR(60)  NOT NULL,
    topic       VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    INDEX       IDX_C42F778412469DE2 (category_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE report_category
(
    id   INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(30)  NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE register_request
(
    id          INT AUTO_INCREMENT NOT NULL,
    email       VARCHAR(255) NOT NULL,
    hash        VARCHAR(255) NOT NULL,
    is_accepted TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE system
(
    id   INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE messenger_messages
(
    id           BIGINT AUTO_INCREMENT NOT NULL,
    body         LONGTEXT     NOT NULL,
    headers      LONGTEXT     NOT NULL,
    queue_name   VARCHAR(190) NOT NULL,
    created_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    available_at DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX        IDX_75EA56E0FB7336F0 (queue_name),
    INDEX        IDX_75EA56E0E3BD61CE (available_at),
    INDEX        IDX_75EA56E016BA31DB (delivered_at),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE catalog
    ADD CONSTRAINT FK_1B2C3247D0952FA5 FOREIGN KEY (system_id) REFERENCES system (id);
ALTER TABLE report
    ADD CONSTRAINT FK_C42F778412469DE2 FOREIGN KEY (category_id) REFERENCES report_category (id);
