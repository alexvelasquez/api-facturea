<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201120215344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE usuario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categoria (categoria_id SERIAL NOT NULL, negocio_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(categoria_id))');
        $this->addSql('CREATE INDEX negocio_id ON categoria (negocio_id)');
        $this->addSql('CREATE TABLE cliente (cliente_id SERIAL NOT NULL, localidad_id INT DEFAULT NULL, tipo_documento_id INT DEFAULT NULL, condicion_iva_id INT DEFAULT NULL, negocio_id INT DEFAULT NULL, razon_social VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, direccion VARCHAR(255) DEFAULT NULL, telefono VARCHAR(255) DEFAULT NULL, documento VARCHAR(255) DEFAULT NULL, f_creacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_modificacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_hasta TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(cliente_id))');
        $this->addSql('CREATE INDEX IDX_F41C9B257D879E4F ON cliente (negocio_id)');
        $this->addSql('CREATE INDEX localidad_id ON cliente (localidad_id)');
        $this->addSql('CREATE INDEX tipo_documento_id ON cliente (tipo_documento_id)');
        $this->addSql('CREATE INDEX condicion_iva_id ON cliente (condicion_iva_id)');
        $this->addSql('CREATE TABLE comprobante_preventa (comprobante_preventa_id SERIAL NOT NULL, estado_id INT NOT NULL, tipo_comprobante_id INT DEFAULT NULL, condicion_venta_id INT DEFAULT NULL, preventa_id INT NOT NULL, numero INT DEFAULT NULL, punto_venta INT DEFAULT NULL, vigente VARCHAR(1) NOT NULL, PRIMARY KEY(comprobante_preventa_id))');
        $this->addSql('CREATE INDEX estado_id ON comprobante_preventa (estado_id)');
        $this->addSql('CREATE INDEX tipo_comprobante_id ON comprobante_preventa (tipo_comprobante_id)');
        $this->addSql('CREATE INDEX preventa_id ON comprobante_preventa (preventa_id)');
        $this->addSql('CREATE INDEX condicion_venta_id ON comprobante_preventa (condicion_venta_id)');
        $this->addSql('CREATE TABLE condicion_iva (condicion_iva_id SERIAL NOT NULL, afip_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(condicion_iva_id))');
        $this->addSql('CREATE TABLE condicion_venta (condicion_venta_id SERIAL NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(condicion_venta_id))');
        $this->addSql('CREATE TABLE estado (estado_id SERIAL NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(estado_id))');
        $this->addSql('CREATE TABLE localidad (localidad_id SERIAL NOT NULL, provincia_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(localidad_id))');
        $this->addSql('CREATE INDEX provincia_id ON localidad (provincia_id)');
        $this->addSql('CREATE TABLE marca (marca_id SERIAL NOT NULL, negocio_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(marca_id))');
        $this->addSql('CREATE INDEX IDX_70A01137D879E4F ON marca (negocio_id)');
        $this->addSql('CREATE TABLE movimiento (movimiento_id SERIAL NOT NULL, cliente_id INT NOT NULL, monto_pagado DOUBLE PRECISION NOT NULL, observacion VARCHAR(255) DEFAULT NULL, f_creacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(movimiento_id))');
        $this->addSql('CREATE INDEX IDX_C8FF107ADE734E51 ON movimiento (cliente_id)');
        $this->addSql('CREATE INDEX movimiento_id ON movimiento (movimiento_id)');
        $this->addSql('CREATE TABLE negocio (negocio_id SERIAL NOT NULL, localidad_id INT DEFAULT NULL, condicion_iva_id INT DEFAULT NULL, razon_social VARCHAR(255) DEFAULT NULL, direccion VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telefono VARCHAR(255) DEFAULT NULL, cuit_cuil VARCHAR(255) DEFAULT NULL, iibb VARCHAR(255) DEFAULT NULL, inicio_actividad DATE DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, punto_vta INT DEFAULT NULL, PRIMARY KEY(negocio_id))');
        $this->addSql('CREATE INDEX IDX_7528E37967707C89 ON negocio (localidad_id)');
        $this->addSql('CREATE INDEX IDX_7528E379E262B53E ON negocio (condicion_iva_id)');
        $this->addSql('CREATE TABLE notificacion (notificacion_id SERIAL NOT NULL, user_id INT DEFAULT NULL, titulo VARCHAR(255) NOT NULL, mensaje TEXT NOT NULL, redireccion VARCHAR(255) DEFAULT NULL, leido VARCHAR(1) NOT NULL, PRIMARY KEY(notificacion_id))');
        $this->addSql('CREATE INDEX id ON notificacion (user_id)');
        $this->addSql('CREATE TABLE preventa (preventa_id SERIAL NOT NULL, cliente_id INT NOT NULL, tipo_preventa_id INT DEFAULT NULL, fecha TIMESTAMP(0) WITH TIME ZONE NOT NULL, monto_debido DOUBLE PRECISION NOT NULL, f_creacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_modificacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_hasta TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(preventa_id))');
        $this->addSql('CREATE INDEX cliente_id ON preventa (cliente_id)');
        $this->addSql('CREATE INDEX tipo_preventa_id ON preventa (tipo_preventa_id)');
        $this->addSql('CREATE TABLE producto (producto_id SERIAL NOT NULL, marca_id INT DEFAULT NULL, categoria_id INT DEFAULT NULL, negocio_id INT NOT NULL, codigo INT NOT NULL, descripcion VARCHAR(255) NOT NULL, stock INT NOT NULL, precio_compra DOUBLE PRECISION NOT NULL, aumento INT NOT NULL, f_creacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_modificacion TIMESTAMP(0) WITH TIME ZONE NOT NULL, f_hasta TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(producto_id))');
        $this->addSql('CREATE INDEX IDX_A7BB06157D879E4F ON producto (negocio_id)');
        $this->addSql('CREATE INDEX marca_id ON producto (marca_id)');
        $this->addSql('CREATE INDEX categoria_id ON producto (categoria_id)');
        $this->addSql('CREATE TABLE producto_preventa (producto_preventa_id SERIAL NOT NULL, tipo_alicuota_id INT DEFAULT NULL, producto_id INT NOT NULL, preventa_id INT NOT NULL, cantidad INT NOT NULL, subtotal DOUBLE PRECISION NOT NULL, subtotal_sin_iva DOUBLE PRECISION NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, bonificacion DOUBLE PRECISION NOT NULL, monto_bonif DOUBLE PRECISION NOT NULL, monto_iva DOUBLE PRECISION NOT NULL, PRIMARY KEY(producto_preventa_id))');
        $this->addSql('CREATE INDEX IDX_8BEA798FE66A2D65 ON producto_preventa (preventa_id)');
        $this->addSql('CREATE INDEX producto_id ON producto_preventa (producto_id)');
        $this->addSql('CREATE INDEX tipo_alicuota_id ON producto_preventa (tipo_alicuota_id)');
        $this->addSql('CREATE TABLE provincia (provincia_id SERIAL NOT NULL, geo_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(provincia_id))');
        $this->addSql('CREATE TABLE tipo_alicuota (tipo_alicuota_id SERIAL NOT NULL, afip_id INT NOT NULL, valor DOUBLE PRECISION DEFAULT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(tipo_alicuota_id))');
        $this->addSql('CREATE TABLE tipo_comprobante (tipo_comprobante_id SERIAL NOT NULL, afip_id INT NOT NULL, codigo VARCHAR(1) NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(tipo_comprobante_id))');
        $this->addSql('CREATE TABLE tipo_concepto (tipo_concepto_id SERIAL NOT NULL, afip_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(tipo_concepto_id))');
        $this->addSql('CREATE TABLE tipo_documento (tipo_documento_id SERIAL NOT NULL, afip_id INT NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(tipo_documento_id))');
        $this->addSql('CREATE TABLE tipo_preventa (tipo_preventa_id SERIAL NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(tipo_preventa_id))');
        $this->addSql('CREATE TABLE usuario (id INT NOT NULL, negocio_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, factura_electronica VARCHAR(1) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2265B05D7D879E4F ON usuario (negocio_id)');
        $this->addSql('COMMENT ON COLUMN usuario.roles IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE categoria ADD CONSTRAINT FK_4E10122D7D879E4F FOREIGN KEY (negocio_id) REFERENCES negocio (negocio_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B2567707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (localidad_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25F6939175 FOREIGN KEY (tipo_documento_id) REFERENCES tipo_documento (tipo_documento_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B25E262B53E FOREIGN KEY (condicion_iva_id) REFERENCES condicion_iva (condicion_iva_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cliente ADD CONSTRAINT FK_F41C9B257D879E4F FOREIGN KEY (negocio_id) REFERENCES negocio (negocio_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comprobante_preventa ADD CONSTRAINT FK_F4FFB3FC9F5A440B FOREIGN KEY (estado_id) REFERENCES estado (estado_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comprobante_preventa ADD CONSTRAINT FK_F4FFB3FCA9B5E49A FOREIGN KEY (tipo_comprobante_id) REFERENCES tipo_comprobante (tipo_comprobante_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comprobante_preventa ADD CONSTRAINT FK_F4FFB3FC1C97F2C6 FOREIGN KEY (condicion_venta_id) REFERENCES condicion_venta (condicion_venta_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comprobante_preventa ADD CONSTRAINT FK_F4FFB3FCE66A2D65 FOREIGN KEY (preventa_id) REFERENCES preventa (preventa_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE localidad ADD CONSTRAINT FK_4F68E0104E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (provincia_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE marca ADD CONSTRAINT FK_70A01137D879E4F FOREIGN KEY (negocio_id) REFERENCES negocio (negocio_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE movimiento ADD CONSTRAINT FK_C8FF107ADE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (cliente_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negocio ADD CONSTRAINT FK_7528E37967707C89 FOREIGN KEY (localidad_id) REFERENCES localidad (localidad_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE negocio ADD CONSTRAINT FK_7528E379E262B53E FOREIGN KEY (condicion_iva_id) REFERENCES condicion_iva (condicion_iva_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notificacion ADD CONSTRAINT FK_729A19ECA76ED395 FOREIGN KEY (user_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preventa ADD CONSTRAINT FK_A379085ADE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (cliente_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preventa ADD CONSTRAINT FK_A379085AD34E039D FOREIGN KEY (tipo_preventa_id) REFERENCES tipo_preventa (tipo_preventa_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB061581EF0041 FOREIGN KEY (marca_id) REFERENCES marca (marca_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06153397707A FOREIGN KEY (categoria_id) REFERENCES categoria (categoria_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB06157D879E4F FOREIGN KEY (negocio_id) REFERENCES negocio (negocio_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto_preventa ADD CONSTRAINT FK_8BEA798FFBBC2AFD FOREIGN KEY (tipo_alicuota_id) REFERENCES tipo_alicuota (tipo_alicuota_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto_preventa ADD CONSTRAINT FK_8BEA798F7645698E FOREIGN KEY (producto_id) REFERENCES producto (producto_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto_preventa ADD CONSTRAINT FK_8BEA798FE66A2D65 FOREIGN KEY (preventa_id) REFERENCES preventa (preventa_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D7D879E4F FOREIGN KEY (negocio_id) REFERENCES negocio (negocio_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE producto DROP CONSTRAINT FK_A7BB06153397707A');
        $this->addSql('ALTER TABLE movimiento DROP CONSTRAINT FK_C8FF107ADE734E51');
        $this->addSql('ALTER TABLE preventa DROP CONSTRAINT FK_A379085ADE734E51');
        $this->addSql('ALTER TABLE cliente DROP CONSTRAINT FK_F41C9B25E262B53E');
        $this->addSql('ALTER TABLE negocio DROP CONSTRAINT FK_7528E379E262B53E');
        $this->addSql('ALTER TABLE comprobante_preventa DROP CONSTRAINT FK_F4FFB3FC1C97F2C6');
        $this->addSql('ALTER TABLE comprobante_preventa DROP CONSTRAINT FK_F4FFB3FC9F5A440B');
        $this->addSql('ALTER TABLE cliente DROP CONSTRAINT FK_F41C9B2567707C89');
        $this->addSql('ALTER TABLE negocio DROP CONSTRAINT FK_7528E37967707C89');
        $this->addSql('ALTER TABLE producto DROP CONSTRAINT FK_A7BB061581EF0041');
        $this->addSql('ALTER TABLE categoria DROP CONSTRAINT FK_4E10122D7D879E4F');
        $this->addSql('ALTER TABLE cliente DROP CONSTRAINT FK_F41C9B257D879E4F');
        $this->addSql('ALTER TABLE marca DROP CONSTRAINT FK_70A01137D879E4F');
        $this->addSql('ALTER TABLE producto DROP CONSTRAINT FK_A7BB06157D879E4F');
        $this->addSql('ALTER TABLE usuario DROP CONSTRAINT FK_2265B05D7D879E4F');
        $this->addSql('ALTER TABLE comprobante_preventa DROP CONSTRAINT FK_F4FFB3FCE66A2D65');
        $this->addSql('ALTER TABLE producto_preventa DROP CONSTRAINT FK_8BEA798FE66A2D65');
        $this->addSql('ALTER TABLE producto_preventa DROP CONSTRAINT FK_8BEA798F7645698E');
        $this->addSql('ALTER TABLE localidad DROP CONSTRAINT FK_4F68E0104E7121AF');
        $this->addSql('ALTER TABLE producto_preventa DROP CONSTRAINT FK_8BEA798FFBBC2AFD');
        $this->addSql('ALTER TABLE comprobante_preventa DROP CONSTRAINT FK_F4FFB3FCA9B5E49A');
        $this->addSql('ALTER TABLE cliente DROP CONSTRAINT FK_F41C9B25F6939175');
        $this->addSql('ALTER TABLE preventa DROP CONSTRAINT FK_A379085AD34E039D');
        $this->addSql('ALTER TABLE notificacion DROP CONSTRAINT FK_729A19ECA76ED395');
        $this->addSql('DROP SEQUENCE usuario_id_seq CASCADE');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE comprobante_preventa');
        $this->addSql('DROP TABLE condicion_iva');
        $this->addSql('DROP TABLE condicion_venta');
        $this->addSql('DROP TABLE estado');
        $this->addSql('DROP TABLE localidad');
        $this->addSql('DROP TABLE marca');
        $this->addSql('DROP TABLE movimiento');
        $this->addSql('DROP TABLE negocio');
        $this->addSql('DROP TABLE notificacion');
        $this->addSql('DROP TABLE preventa');
        $this->addSql('DROP TABLE producto');
        $this->addSql('DROP TABLE producto_preventa');
        $this->addSql('DROP TABLE provincia');
        $this->addSql('DROP TABLE tipo_alicuota');
        $this->addSql('DROP TABLE tipo_comprobante');
        $this->addSql('DROP TABLE tipo_concepto');
        $this->addSql('DROP TABLE tipo_documento');
        $this->addSql('DROP TABLE tipo_preventa');
        $this->addSql('DROP TABLE usuario');
    }
}
