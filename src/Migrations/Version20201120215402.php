<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201120205626 extends AbstractMigration
{

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO condicion_iva ( afip_id, descripcion) VALUES ( '4','IVA Sujeto Exento'),( '5','Consumidor Final'),( '6','Responsable Monotributo')");
        $this->addSql("INSERT INTO condicion_venta ( descripcion) VALUES ( 'Contado'),( 'Tarjeta de Débito'),( 'Tarjeta de Crédito'),( 'Cuenta Corriente'),( 'Cheque'),( 'Ticket'),( 'Otra')");
        $this->addSql("INSERT INTO estado (descripcion) VALUES ( 'PENDIENTE'),( 'REALIZADO'),( 'PAGADO'),( 'CANCELADO'),( 'PENDIENTE DE PAGO')");
        $this->addSql("INSERT INTO tipo_alicuota (afip_id, valor, descripcion) VALUES ( '1','0','No Gravado'),( '2','0','Exento'),( '3','0','0%'),( '9','0.025','2,50%'),( '8','0.05','5%'),( '4','0.105','10,50%'),( '5','0.21','21%'),( '6','0.27','27%')");
        $this->addSql("INSERT INTO tipo_comprobante (afip_id, descripcion, codigo) VALUES ( '1','Factura','A'),( '2','Nota de Débito','A'),( '3','Nota de Crédito','A'),( '4','Recibos','A'),( '6','Factura','B'),( '7','Nota de Débito','B'),( '8','Nota de Crédito','B'),( '9','Recibos','B'),( '11','Factura','C'),( '12','Nota de Débito','C'),( '13','Nota de Crédito','C'),( '15','Recibo','C'),( '99','Recibo','X')");
        $this->addSql("INSERT INTO tipo_documento ( afip_id, descripcion) VALUES ( '80','CUIT'),( '87','CDI'),( '91','CI Extranjera'),( '94','Pasaporte'),( '96','DNI'),( '99','Otro')");
        $this->addSql("INSERT INTO tipo_preventa (descripcion) VALUES ( 'Comprobante'),( 'Pedido')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
