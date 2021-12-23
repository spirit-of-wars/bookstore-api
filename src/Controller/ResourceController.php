<?php

namespace App\Controller;

use App\Enum\UserRolesEnum;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Request;
use App\Service\File\FileService;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SourceController
 * @package App\Controller
 * @Route("/resource", name="resource_")
 */
class ResourceController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Resource';
    }

    /**
     * @return array
     */
    public static function getActionsWithoutValidation() : array
    {
        //TODO: убрать после доработки сборщика валидационных форм и сваггера
        return ['create', 'update'];
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'create' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'update' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
            'delete' => [UserRolesEnum::SUPER_ADMIN, UserRolesEnum::ADMIN],
        ];
    }

    /**
     * @Route("/", name="create_resource", methods={"POST"})
     *
     * @param FileService $fileService
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(FileService $fileService, Request $request) : JsonResponse
    {
        $files = $fileService->uploadFileForEntityFromRequest($request);

        if (!$files) {
            return $this->prepareErrorResponse('Files upload error');
        }

        return $this->prepareResponse($files);
    }

    /**
     * @Route("/update/{id}", requirements={"id": "[1-9]\d*"}, name="update_resource", methods={"POST"})
     *
     * @param FileService $fileService
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(FileService $fileService, Request $request) : JsonResponse
    {
        $files = $fileService->uploadFileForEntityFromRequest($request);

        if (!$files) {
            return $this->prepareErrorResponse('Files upload error');
        }

        return $this->prepareResponse($files);
    }

    /**
     * @Route("/{id}", requirements={"id": "[1-9]\d*"}, name="delete_resource", methods={"DELETE"})
     * @param $id
     * @param FileService $service
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete($id, FileService $service)
    {
        if ($service->deleteResource($id)) {
            return $this->prepareResponse(['id' => $id]);
        }

        return $this->prepareErrorResponse('An error occurred while deleting resource with id = ' . $id);
    }
}
