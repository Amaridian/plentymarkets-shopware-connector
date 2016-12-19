<?php

namespace ShopwareAdapter\QueryBus\QueryHandler\PaymentStatus;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use PlentyConnector\Connector\QueryBus\Query\PaymentStatus\FetchAllPaymentStatusesQuery;
use PlentyConnector\Connector\QueryBus\Query\QueryInterface;
use PlentyConnector\Connector\QueryBus\QueryHandler\QueryHandlerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Order\Status;
use ShopwareAdapter\ResponseParser\ResponseParserInterface;
use ShopwareAdapter\ShopwareAdapter;

/**
 * Class FetchAllPaymentStatusesHandler
 */
class FetchAllPaymentStatusesHandler implements QueryHandlerInterface
{
    /**
     * @var ModelRepository
     */
    private $repository;

    /**
     * @var ResponseParserInterface
     */
    private $responseParser;

    /**
     * FetchAllPaymentStatusesHandler constructor.
     *
     * @param EntityManagerInterface $entityManager ,
     * @param ResponseParserInterface $responseParser
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ResponseParserInterface $responseParser
    ) {
        $this->repository = $entityManager->getRepository(Status::class);
        $this->responseParser = $responseParser;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(QueryInterface $event)
    {
        return $event instanceof FetchAllPaymentStatusesQuery &&
            $event->getAdapterName() === ShopwareAdapter::getName();
    }

    /**
     * @return Query
     */
    private function createPaymentStatusQuery()
    {
        $queryBuilder = $this->repository->createQueryBuilder('status');
        $queryBuilder->select([
            'status.id as id',
            'status.name as name'
        ]);
        $queryBuilder->where('status.group = :group');
        $queryBuilder->setParameter('group', Status::GROUP_PAYMENT);

        $query = $queryBuilder->getQuery();
        $query->execute();

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QueryInterface $event)
    {
        $query = $this->createPaymentStatusQuery();

        $paymentStatuses = array_map(function ($status) {
            return $this->responseParser->parse($status);
        }, $query->getArrayResult());

        return array_filter($paymentStatuses);
    }
}
