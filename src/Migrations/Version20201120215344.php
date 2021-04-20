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
        $fechaActual = (new \DateTime())->format('Y-m-d H:m:s');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO condicion_iva ( afip_id, descripcion) VALUES ('1','IVA Responsable Inscripto'),('4','IVA Sujeto Exento'),( '5','Consumidor Final'),('6','Responsable Monotributo')");
        $this->addSql("INSERT INTO condicion_venta (condicion_venta_id,descripcion) VALUES ('15','Contado'),('16','Tarjeta de Débito'),('17','Tarjeta de Crédito'),( '18','Cuenta Corriente'),('19','Cheque'),('20','Ticket'),('21','Otra')");
        $this->addSql("INSERT INTO estado (codigo, descripcion) VALUES ('PENDIENTE', 'Pendiente'), ('REALIZADO', 'Realizado'), ('PENDIENTEPAGO', 'Pendiente de Pago'), ('PENDIENTECOMPROBANTE', 'Pendiente de Comprobante'), ('PAGADO', 'Pagado')");
        $this->addSql("INSERT INTO tipo_alicuota (afip_id, valor, descripcion) VALUES ( '1','0','No Gravado'),( '2','0','Exento'),( '3','0','0%'),( '9','0.025','2,50%'),( '8','0.05','5%'),( '4','0.105','10,50%'),( '5','0.21','21%'),( '6','0.27','27%')");
        $this->addSql("INSERT INTO tipo_comprobante (afip_id, descripcion, codigo) VALUES ( '1','Factura','A'),( '2','Nota de Débito','A'),( '3','Nota de Crédito','A'),( '4','Recibos','A'),( '6','Factura','B'),( '7','Nota de Débito','B'),( '8','Nota de Crédito','B'),( '9','Recibos','B'),( '11','Factura','C'),( '12','Nota de Débito','C'),( '13','Nota de Crédito','C'),( '15','Recibo','C'),( '99','Recibo','R')");
        $this->addSql("INSERT INTO tipo_documento ( afip_id, descripcion) VALUES ( '80','CUIT'),( '87','CDI'),( '91','CI Extranjera'),( '94','Pasaporte'),( '96','DNI'),( '99','Otro')");
        $this->addSql("INSERT INTO tipo_venta (codigo,descripcion) VALUES ('COMPROBANTE','Comprobante'),('PEDIDO','Pedido')");
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO tipo_concepto ( afip_id, descripcion) VALUES ('1','Producto'),( '2','Servicios'),( '3','Productos y Servicios')");
        $this->addSql("INSERT INTO usuario (name, last_name, email, username, password, roles, created_at, updated_at) VALUES ('facturea', 'Gestion', 'info.facturea@gmail.com', 'facturea.gestion', 'Eti36Ru/pWG6WfoIPiDFUBxUuyvgMA4L8+LLuGbGyqV9ATuT9brCWPchBqX5vFTF+DgntacecW+sSGD+GZts2A==', '[\"ROLE_ADMIN\"]', '{$fechaActual}', '{$fechaActual}')");
    
    
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
