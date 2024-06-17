<?php

namespace App\Controller;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TravelCostController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/calculate-travel-cost', methods: ['POST'])]
    public function calculateTravelCost(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        // Получаем данные из запроса
        $travelStartDate = new DateTime($requestData['travel_start_date']);
        $childrenAges = $requestData['children_ages'];
        $paymentDate = new DateTime($requestData['payment_date']);

        // Расчет стоимости с учетом скидок
        $totalCost = $this->calculateTotalCost($travelStartDate, $childrenAges, $paymentDate);

        $response = [
            'total_cost' => $totalCost,
        ];

        return new JsonResponse($response);
    }

    private function calculateTotalCost(\DateTime $travelStartDate, array $childrenAges, \DateTime $paymentDate): float
    {
        // Базовая стоимость путешествия (здесь можно добавить логику для реального расчета)
        $baseCost = 100000.0;

        // Расчет детских скидок
        $childDiscounts = 0.0;
        foreach ($childrenAges as $age) {
            if ($age < 3) {
                // Дети до 3 лет не платят
                continue;
            } elseif ($age < 6) {
                // Скидка 80% для детей от 3 до 6 лет
                $childDiscounts += $baseCost * 0.8;
            } elseif ($age < 12) {
                // Скидка 30%, но не более 4500 ₽ для детей от 6 до 12 лет
                $childDiscounts += min($baseCost * 0.3, 4500);
            } else {
                // Скидка 10% для детей от 12 лет
                $childDiscounts += $baseCost * 0.1;
            }
        }

        // Расчет скидок за раннее бронирование
        $earlyBookingDiscount = 0.0;
        $monthsDiff = $paymentDate->diff($travelStartDate)->m;

        if ($monthsDiff >= 3 && $monthsDiff < 7) {
            // Скидка 7%, но не более 1500 ₽
            $earlyBookingDiscount = min($baseCost * 0.07, 1500);
        } elseif ($monthsDiff >= 7 && $monthsDiff < 12) {
            // Скидка 5%, но не более 1500 ₽
            $earlyBookingDiscount = min($baseCost * 0.05, 1500);
        } elseif ($monthsDiff >= 12) {
            // Скидка 3%, но не более 1500 ₽
            $earlyBookingDiscount = min($baseCost * 0.03, 1500);
        }

        // Итоговая стоимость с учетом всех скидок
        return $baseCost - $childDiscounts - $earlyBookingDiscount;
    }
}

