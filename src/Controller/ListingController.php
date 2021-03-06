<?php


namespace App\Controller;

use App\Entity\Listing;
use App\Service\ListingService;
use App\Service\ResponseErrorDecoratorService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ListingController extends Controller
{
    /**
     * Creates new listing by passed JSON data
     *
     * @Route("/api/listings", methods={"POST"})
     * @param Request $request
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createListing(
        Request $request,
        ListingService $listingService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data)) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $listingService->createListing($data);

        if ($result instanceof Listing) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'section_id' => $result->getSection()->getId(),
                    'title' => $result->getTitle(),
                    'zip_code' => $result->getZipCode(),
                    'city_id' => $result->getCity()->getId(),
                    'description' => $result->getDescription(),
                    'publication_date' => $result->getPublicationDate()->format("Y-m-d H:i:s"),
                    'expiration_date' => $result->getExpirationDate()->format("Y-m-d H:i:s"),
                    'user_id' => $result->getUser()->getEmail(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/listings/{id}", methods={"GET"})
     * @param Listing $listing Symfony will find listing entity by {id} and will assign it to $listing
     * @return JsonResponse Data array which contains information about listing
     */
    public function getListing(Listing $listing)
    {
        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'id' => $listing->getId(),
                'section_id' => $listing->getSection()->getId(),
                'title' => $listing->getTitle(),
                'zip_code' => $listing->getZipCode(),
                'city_id' => $listing->getCity()->getId(),
                'description' => $listing->getDescription(),
                'publication_date' => $listing->getPublicationDate()->format("Y-m-d H:i:s"),
                'expiration_date' => $listing->getExpirationDate()->format("Y-m-d H:i:s"),
                'user_id' => $listing->getUser()->getEmail(),
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * Get listings filtered by (optional params given in a query string)
     *
     * Here is usage example:
     *
     * url: http://localhost:8000/api/listings?section_id=1&city_id=1&days_back=30&excluded_user_id=1
     *   (where
     *   - section_id is id of a category you want to filter by
     *   - city_id is id of a city to filter by
     *   - days_back is used to get listings published up to 30 days ago
     *   - excluded_user_id if listing belongs to given excluded_user_id, it will be filtered out
     *    * all filter keys are optional (you can use none, one or all of them if needed)
     *   )
     *
     * @Route("/api/listings", methods={"GET"})
     * @param Request $request
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse List of listings
     */
    public function getListings(
        Request $request,
        ListingService $listingService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $filter = $request->query->all();
        $result = $listingService->getListings($filter);

        if (is_array($result)) {
            $listingsArr = [];
            foreach ($result as $listing) {
                $listingsArr[] = [
                    'id' => $listing->getId(),
                    'section_id' => $listing->getSection()->getId(),
                    'title' => $listing->getTitle(),
                    'zip_code' => $listing->getZipCode(),
                    'city_id' => $listing->getCity()->getId(),
                    'description' => $listing->getDescription(),
                    'publication_date' => $listing->getPublicationDate()->format("Y-m-d H:i:s"),
                    'expiration_date' => $listing->getExpirationDate()->format("Y-m-d H:i:s"),
                    'user_id' => $listing->getUser()->getEmail(),
                ];
            }

            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'listings' => $listingsArr
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * Update listing by passed JSON data
     *
     * @Route("/api/listings/{id}", methods={"PUT"})
     * @param Listing $listing
     * @param Request $request
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function updateListing(
        Listing $listing,
        Request $request,
        ListingService $listingService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data)) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $listingService->updateListing($listing, $data);

        if ($result instanceof Listing) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'section_id' => $result->getSection()->getId(),
                    'title' => $result->getTitle(),
                    'zip_code' => $result->getZipCode(),
                    'city_id' => $result->getCity()->getId(),
                    'description' => $result->getDescription(),
                    'publication_date' => $result->getPublicationDate()->format("Y-m-d H:i:s"),
                    'expiration_date' => $result->getExpirationDate()->format("Y-m-d H:i:s"),
                    'user_id' => $result->getUser()->getEmail(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/listings/{id}", methods={"DELETE"})
     * @param Listing $listing
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteListing(
        Listing $listing,
        ListingService $listingService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $listingService->deleteListing($listing);
        if ($result === true) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $data = null;
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }
}