<?php
namespace Hq\DaemonsBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class DaemonSessionRepository extends EntityRepository {

    public function startSession($status) {}

}