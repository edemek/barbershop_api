<?php
/*
 * File name: BookingAPIController.php
 * Last modified: 2024.04.10 at 13:21:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2024
 */

namespace App\Http\Controllers\API;


use App\Criteria\Bookings\BookingsOfUserCriteria;
use App\Criteria\Coupons\ValidCriteria;
use App\Events\BookingChangedEvent;
use App\Events\BookingStatusChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Notifications\NewBooking;
use App\Repositories\AddressRepository;
use App\Repositories\BookingRepository;
use App\Repositories\BookingStatusRepository;
use App\Repositories\CouponRepository;
use App\Repositories\EServiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\SalonRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class BookingController
 * @package App\Http\Controllers\API
 */
class BookingAPIController extends Controller
{
    /** @var  BookingRepository */
    private BookingRepository $bookingRepository;

    /**
     * @var BookingStatusRepository
     */
    private BookingStatusRepository $bookingStatusRepository;
  
    /**
     * @var AddressRepository
     */
    private AddressRepository $addressRepository;
    /**
     * @var EServiceRepository
     */
    private EServiceRepository $eServiceRepository;
    /**
     * @var SalonRepository
     */
    private SalonRepository $salonRepository;
   
  


    public function __construct(BookingRepository $bookingRepo,
        BookingStatusRepository  $bookingStatusRepo, 
        AddressRepository $addressRepository, 
        EServiceRepository $eServiceRepository, 
        SalonRepository $salonRepository)
    {
        // parent::__construct();
        $this->bookingRepository = $bookingRepo;
        $this->bookingStatusRepository = $bookingStatusRepo;
        $this->addressRepository = $addressRepository;
        $this->eServiceRepository = $eServiceRepository;
        $this->salonRepository = $salonRepository;
    }

    /**
     * Display a listing of the Booking.
     * GET|HEAD /bookings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->bookingRepository->queryCriteria(function (\Illuminate\Database\Eloquent\Builder $query) use ($request) {
                // Exemple de filtrage : par ID d'utilisateur
                if ($request->has('user_id')) {
                    $query->where('user_id', $request->input('user_id'));
                }
                return $query ;
            });
            // $this->bookingRepository->pushCriteria(new RequestCriteria($request));
            // $this->bookingRepository->pushCriteria(new BookingsOfUserCriteria(auth()->id()));
            // $this->bookingRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
        $bookings = $this->bookingRepository->get();
        $this->filterCollection($request, $bookings);
        return $this->sendResponse($bookings->toArray(), 'Bookings retrieved successfully');
    }

    /**
     * Display the specified Booking.
     * GET|HEAD /bookings/{id}
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $this->bookingRepository->queryCriteria(function (\Illuminate\Database\Eloquent\Builder $query) use ($request) {});
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
        $booking = $this->bookingRepository->findWithoutFail($id);
        if (empty($booking)) {
            return $this->sendError('Booking not found');
        }
        $this->filterModel($request, $booking);
        return $this->sendResponse($booking->toArray(), 'Booking retrieved successfully');


    }

    /**
     * Store a newly created Booking in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    /** @noinspection PhpUndefinedFieldInspection */
    public function store(Request $request): JsonResponse
    {
        try {
            $input = $request->all();
            $salon = $this->salonRepository->find($input['salon_id']);
            if (isset($input['address'])) {
                $this->validate($request, [
                    'address.address' => Address::$rules['address'],
                    'address.longitude' => Address::$rules['longitude'],
                    'address.latitude' => Address::$rules['latitude'],
                ]);
                // $address = $this->addressRepository->updateOrCreate(['address' => $input['address']['address']], $input['address']);
                // if (empty($address)) {
                //     return $this->sendError(__('lang.not_found', ['operator', __('lang.address')]));
                // } else {
                    $input['address'] = '$address';
                // }
            } else {
                $input['address'] = $salon->address;
            }
            if (isset($input['e_services'])) {
                $input['e_services'] = $this->eServiceRepository->findWhereIn('id', $input['e_services']);
                // coupon code 
            }

            // $taxes = $salon->taxes;
            // $input['salon'] = $salon;
            // $input['taxes'] = $taxes;

            // if (isset($input['options'])) {
            //     $input['options'] = $this->optionRepository->findWhereIn('id', $input['options']);
            // }
            $input['booking_status_id'] = $this->bookingStatusRepository->find(1)->id;

            $booking = $this->bookingRepository->create($input);
            Notification::send($salon->users, new NewBooking($booking));

        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->errors()));
        } catch (ValidatorException|ModelNotFoundException|Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($booking->toArray(), __('lang.saved_successfully', ['operator' => __('lang.booking')]));
    }

    /**
     * Update the specified Booking in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $oldBooking = $this->bookingRepository->findWithoutFail($id);
        if (empty($oldBooking)) {
            return $this->sendError('Booking not found');
        }
        $input = $request->all();
        try {
            if (isset($input['cancel']) && $input['cancel'] == '1') {
                $input['payment_status_id'] = 3;
                $input['booking_status_id'] = 7;
            }
            $booking = $this->bookingRepository->update($input, $id);
            if (isset($input['payment_status_id'])) {
                // $this->paymentRepository->update(
                //     ['payment_status_id' => $input['payment_status_id']],
                //     $booking->payment_id
                // );
                // event(new BookingChangedEvent($booking));
            }
            if (isset($input['booking_status_id']) && $input['booking_status_id'] != $oldBooking->booking_status_id) {
                event(new BookingStatusChangedEvent($booking));
            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($booking->toArray(), __('lang.saved_successfully', ['operator' => __('lang.booking')]));
    }

}