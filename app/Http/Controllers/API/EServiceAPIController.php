<?php
/*
 * File name: EServiceAPIController.php
 * Last modified: 2025.02.01 at 11:22:50
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEServiceRequest;
use App\Http\Requests\UpdateEServiceRequest;
use App\Repositories\EServiceRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class EServiceController
 * @package App\Http\Controllers\API
 */
class EServiceAPIController extends Controller
{
    /** @var  eServiceRepository */
    protected EServiceRepository $eServiceRepository;
    /** @var UserRepository */
    private UserRepository $userRepository;
    /**
     * @var UploadRepository
     */
    private UploadRepository $uploadRepository;

    public function __construct(EServiceRepository $eServiceRepository, UserRepository $userRepository, UploadRepository $uploadRepository)
    {
        parent::__construct();
        $this->eServiceRepository = $eServiceRepository;
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
    }


    /**
     * Store a newly created EService in storage.
     *
     * @param CreateEServiceRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateEServiceRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $eService = $this->eServiceRepository->create($input);
            // if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
            //     foreach ($input['image'] as $fileUuid) {
            //         $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
            //         $mediaItem = $cacheUpload->getMedia('image')->first();
            //         $mediaItem->copy($eService, 'image');
            //     }
            // }
        }catch (ValidationException $e) {
            // Return a custom JSON error response for validation errors
            return $this->sendError($e->validator->errors(),422); // HTTP status code 422 Unprocessable Entity
        }  catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($eService->toArray(), __('lang.saved_successfully', ['operator' => __('lang.e_service')]));
    }

    /**
     * Update the specified EService in storage.
     *
     * @param int $id
     * @param UpdateEServiceRequest $request
     *
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function update(int $id, UpdateEServiceRequest $request): JsonResponse
    {
        $eService = $this->eServiceRepository->findWithoutFail($id);

        if (empty($eService)) {
            return $this->sendError('E Service not found');
        }
        try {
            $input = $request->all();
            $input['categories'] = $input['categories'] ?? [];
            $eService = $this->eServiceRepository->update($id , $input);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                if ($eService->hasMedia('image')) {
                    $eService->getMedia('image')->each->delete();
                }
                // foreach ($input['image'] as $fileUuid) {
                //     $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                //     $mediaItem = $cacheUpload->getMedia('image')->first();
                //     $mediaItem->copy($eService, 'image');
                // }
            }
        }catch (ValidationException $e) {
            // Return a custom JSON error response for validation errors
            return $this->sendError($e->validator->errors(),422); // HTTP status code 422 Unprocessable Entity
        }  catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($eService->toArray(), __('lang.updated_successfully', ['operator' => __('lang.e_service')]));
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
        // $this->eServiceRepository->pushCriteria(new EServicesOfUserCriteria(auth()->id()));
        $eService = $this->eServiceRepository->findWithoutFail($id);

        if (empty($eService)) {
            return $this->sendError('EService not found');
        }

        $eService = $this->eServiceRepository->delete($id);

        return $this->sendResponse($eService, __('lang.deleted_successfully', ['operator' => __('lang.e_service')]));

    }

}
