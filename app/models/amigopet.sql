//Criar tabela SQL usuario_adotante

CREATE TABLE usuario_adotante (
    id_usuario BIGINT(20) NOT NULL AUTO_INCREMENT,
    nome_completo VARCHAR(80) COLLATE utf8mb4_general_ci NOT NULL,
    data_nascimento DATETIME NOT NULL,
    cpf VARCHAR(11) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
    telefone VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
    senha VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    status_cadastro TINYINT(1) NOT NULL,
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

//Criar tabela usuario_ong

CREATE TABLE usuario_ong (
    id_ong BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_fantasia VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tipo_animal (
    id_tipo BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL
);

INSERT INTO tipo_animal (descricao) VALUES ('Cachorro'), ('Gato');

CREATE TABLE animal (
    id_animal INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_ong BIGINT(20) NOT NULL,
    id_tipo BIGINT(20) NOT NULL,
    raca VARCHAR(80) NOT NULL,
    peso FLOAT(3,2) NOT NULL,
    idade INT NOT NULL,
    porte VARCHAR(7) NOT NULL,
    sexo INT(1) NOT NULL,
    descricao VARCHAR(255),
    imagem VARCHAR(255)
);

ALTER TABLE animal
    ADD CONSTRAINT fk_ong FOREIGN KEY (id_ong) REFERENCES usuario_ong(id_ong) ON DELETE CASCADE,
    ADD CONSTRAINT fk_tipo FOREIGN KEY (id_tipo) REFERENCES tipo_animal(id_tipo) ON DELETE CASCADE;