<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class TipoComprobanteRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function tiposComprobantesIva($comprobantes)
    {
      $em = $this->getEntityManager();
      $qb = $em->createQueryBuilder();
      $whereCondicion = '(';
      foreach ($comprobantes as $key=>$value) {
        $whereCondicion .= $value;
        if($key < count($comprobantes) -1){
          $whereCondicion .= ',';
        }
      }
      $whereCondicion .= ')';

      $qb->select('tc')
          ->from('App:TipoComprobante', 'tc');
        /** usuario no inscrito en la AFIP*/
      $qb->where($whereCondicion);
      return $qb->getQuery()->getArrayResult();
    }
}
