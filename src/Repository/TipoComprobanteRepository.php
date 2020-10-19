<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class TipoComprobanteRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function tiposComprobantesIva($condicionIva)
    {
      $em = $this->getEntityManager();
      $qb = $em->createQueryBuilder();
      $qb->select('tc')
          ->from('App:TipoComprobante', 'tc');
      if(empty($condicionIva)){
        /** usuario no inscrito en la AFIP*/
        $qb->where('tc.afipId = 99');
      }
      else{
      /** '1' -> responsable inscripto */
        ($condicionIva == 1 ) ? $qb->where('tc.afipId BETWEEN 1 AND 9') : $qb->where('tc.afipId > 9');
      }
      return $qb->getQuery()->getArrayResult();
    }
}
