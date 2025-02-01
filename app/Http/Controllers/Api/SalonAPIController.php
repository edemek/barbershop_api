<?php
/*
 * File name: SalonAPIController.php
 * Last modified: 2024.04.18 at 17:22:50
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */


 namespace App\Http\Controllers\API;
 
 
use App\Criteria\Salons\NearCriteria;
use App\Criteria\Salons\SalonsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSalonRequest;
use App\Http\Requests\UpdateSalonRequest;
use App\Models\Salon;
use App\Repositories\SalonRepository;
use App\Repositories\UploadRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

 /**
  * Class SalonController
  * @package App\Http\Controllers\API
  */
 class SalonAPIController extends Controller
 {
        /** @var  SalonRepository */
        private SalonRepository $salonRepository;

        /** @var UploadRepository */
        //  private UploadRepository $uploadRepository;

        public function __construct(
            SalonRepository $salonRepo,
            //  , UploadRepository $uploadRepository
        )
        {
            $this->salonRepository = $salonRepo;
            //  $this->uploadRepository = $uploadRepository;
        }

        /**
         * Display a listing of the Salon.
        * GET|HEAD /salons
        *
        * @param Request $request
        * @return JsonResponse
        */
        public function index(Request $request): JsonResponse
        {
            try {
                $this->salonRepository->pushCriteria(new NearCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $salons = $this->salonRepository->get();
            $this->filterCollection($request, $salons);

            return $this->sendResponse($salons->toArray(),  __('lang.saved_successfully', ['operator' => __('lang.address')]));
        }

        /**
         * Display the specified Salon.
        * GET|HEAD /salons/{id}
        *
        * @param int $id
        * @param Request $request
        * @return JsonResponse
        */
        public function show(int $id, Request $request): JsonResponse
        {
            try {
                $this->salonRepository->pushCriteria(new RequestCriteria($request));
            //  $this->salonRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $salon = $this->salonRepository->findWithoutFail($id);
            if (empty($salon)) {
                return $this->sendError('Salon not found');
            }
            $this->filterModel($request, $salon);
            $array = $this->orderAvailabilityHours($salon);
            return $this->sendResponse($array, 'Salon retrieved successfully');
        }

        private function orderAvailabilityHours($salon)
        {
            $array = $salon->toArray();
            if (isset($array['availability_hours'])) {
                $availabilityHours = $array['availability_hours'];
                $availabilityHours = collect($availabilityHours);
                $availabilityHours = $availabilityHours->sortBy(function ($item, $key) {
                    return Carbon::createFromIsoFormat('dddd', $item['day'])->dayOfWeek;
                });
                $array['availability_hours'] = array_values($availabilityHours->toArray());
            }
            return $array;
        }

        /**
         * Store a newly created EService in storage.
        *
        * @param CreateSalonRequest $request
        *
        * @return JsonResponse
        */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(Salon::$rules);

            $input = $request->all();
            // if (auth()->user()->hasAnyRole(['salon owner', 'customer'])) {
                    $input['users'] = 1;
                    $input['owner_id'] = 1;
                //  $input['users'] = [auth()->id()];
                //  $input['accepted'] = 0;
                //  $input['featured'] = 0;
                //  $input['available'] = 1;
            // }
            $salon = $this->salonRepository->create($input);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    //  $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    //  $mediaItem = $cacheUpload->getMedia('image')->first();
                    //  $mediaItem->copy($salon, 'image');
                }
            }
        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->errors()));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($salon->toArray(), __('lang.saved_successfully', ['operator' => __('lang.salon')]));
    }

    /**
     * Update the specified EService in storage.
    *
    * @param int $id
    * @param UpdateSalonRequest $request
    *
    * @return JsonResponse
    * @throws RepositoryException
    */
    public function update(int $id, Request $request): JsonResponse
    {
        $request->validate(Salon::$rules);
        //  $this->salonRepository->pushCriteria(new SalonsOfUserCriteria(auth()->id()));
        $salon = $this->salonRepository->findWithoutFail($id);
 
        if (empty($salon)) {
             return $this->sendError('Salon not found');
        }
        try {
            $input = $request->all();
            $salon = $this->salonRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                if ($salon->hasMedia('image')) {
                    $salon->getMedia('image')->each->delete();
                }
                foreach ($input['image'] as $fileUuid) {
                    // $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    // $mediaItem = $cacheUpload->getMedia('image')->first();
                    // $mediaItem->copy($salon, 'image');
                }
            }
        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->errors()));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($salon->toArray(), __('lang.updated_successfully', ['operator' => __('lang.salon')]));
     }
 
     /**
      * Remove the specified EService from storage.
      *
      * @param int $id
      *
      * @return JsonResponse
      * @throws RepositoryException
      */
     public function destroy(int $id): JsonResponse
     {
        //  $this->salonRepository->pushCriteria(new SalonsOfUserCriteria(auth()->id()));
         $salon = $this->salonRepository->findWithoutFail($id);
         if (empty($salon)) {
             return $this->sendError('Salon not found');
         }
         $this->salonRepository->delete($id);
         return $this->sendResponse($salon, __('lang.deleted_successfully', ['operator' => __('lang.salon')]));
 
     }

}