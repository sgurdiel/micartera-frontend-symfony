<?php

namespace Xver\MiCartera\Frontend\Symfony\Stock\Interface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Xver\MiCartera\Domain\Account\Domain\AccountPersistenceInterface;
use Xver\MiCartera\Domain\Stock\Application\Query\Portfolio\PortfolioQuery;
use Xver\MiCartera\Domain\Stock\Domain\StockPersistenceInterface;
use Xver\MiCartera\Domain\Stock\Domain\Transaction\TransactionPersistenceInterface;

#[Route('/{_locale<%app.locales%>}/stockportfolio')]
final class StockPortfolioController extends AbstractController
{
    #[Route('', name: 'stockportfolio_index', methods: ['GET'])]
    public function index(
        Request $request,
        AccountPersistenceInterface $accountPersistence,
        StockPersistenceInterface $stockPersistence,
        TransactionPersistenceInterface $transactionPersistence
    ): Response {
        /** @psalm-suppress PossiblyNullReference */
        $userIdentifier = $this->getUser()->getUserIdentifier();
        $query = new PortfolioQuery($stockPersistence, $accountPersistence, $transactionPersistence);
        $portfolioDTO = $query->getPortfolio(
            $userIdentifier,
            10,
            (int) $request->query->get('page', 0)
        );

        return $this->render('stock/portfolio/index.html.twig', ['portfolio' => $portfolioDTO]);
    }
}
