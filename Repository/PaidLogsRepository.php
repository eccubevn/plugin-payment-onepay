<?php
namespace Plugin\Onepay\Repository;

use Eccube\Entity\Order;
use Plugin\Onepay\Entity\PaidLogs;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Eccube\Repository\AbstractRepository;
use Symfony\Component\HttpFoundation\Request;

class PaidLogsRepository extends AbstractRepository
{
    /**
     * PaidLogsRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaidLogs::class);
    }

    /**
     * Get current config
     *
     * @param int $id
     * @return object
     */
    public function get($id = 1)
    {
        return $this->find($id);
    }

    /**
     * @param $Order
     * @param Request $request
     */
    public function saveLogs($Order, Request $request)
    {
        $PaidLog = new PaidLogs();
        $PaidLog->setOrder($Order);
        $PaidLog->setPaidInformation(json_encode($request->query->all()));
        $PaidLog->setCreatedAt(new \DateTime());
        $this->getEntityManager()->persist($PaidLog);
    }
}
