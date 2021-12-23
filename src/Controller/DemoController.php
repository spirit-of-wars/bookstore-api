<?php

namespace App\Controller;

use App\Entity\Product;
use App\Enum\UserRolesEnum;
use App\Mif;
use App\Request;
use App\Service\Serializer\EntitySerializer;
use Symfony\Component\Routing\Annotation\Route;
use App\Interfaces\Auth\AuthenticationOAuth2Interface;
use App\Interfaces\Auth\AuthorizationRbacInterface;
use App\Util\Authentication\OAuth2AndRbacControllerTrait;
use App\Util\Authorization\RbacControllerTrait;

/**
 * Class DemoController
 * @package App\Controller
 *
 * @Route("/demo", name="demo_")
 */
class DemoController extends BaseController implements AuthenticationOAuth2Interface, AuthorizationRbacInterface
{
    use RbacControllerTrait;
    use OAuth2AndRbacControllerTrait;

    /**
     * @return string
     */
    public static function getValidationFormsGroupName() : string
    {
        return 'Demo';
    }

    /**
     * @return array
     */
    public static function getActionsWithoutValidation() : array
    {
        return [
            'tempAuthRedirectGet',
            'tempAuthRedirectPost',
        ];
    }

    /**
     * @return array|string[]
     */
    public static function getPermissions() : array
    {
        return [
            'auth' => [UserRolesEnum::CLIENT],
        ];
    }

    /**
     * @Route("/test", name="demo_test", methods={"GET"})
     */
    public function test()
    {
        return $this->prepareResponse('Hello');
    }

    /**
     * @Route("/test-date", name="demo_test_date", methods={"POST"})
     */
    public function testDate(Request $request, EntitySerializer $serializer)
    {
        $date = $request->get('date');
        $product = new Product();
        $product->setStartSaleDate($date);

        return $this->prepareResponse([
            'test_product_with_your_date' => $serializer->serialize($product),
        ]);
    }

    /**
     * @Route("/auth", name="demo_auth", methods={"GET"})
     */
    public function auth()
    {
        $user = Mif::getUserManager()->getUser();
        $name = $user->getFirstname() . ' ' . $user->getSurname();

        return $this->prepareResponse("Hello, user $name!");
    }

    /**
     * @Route("/sum", name="demo_sum", methods={"GET"})
     */
    public function sum(Request $request)
    {
        $a = $request->get('a');
        $b = $request->get('b');

        return $this->prepareResponse($a + $b);
    }

    /**
     * @Route("/calc", name="demo_calc", methods={"POST"})
     */
    public function calc(Request $request)
    {
        $a = $request->get('a');
        $b = $request->get('b');
        $operation = $request->get('operation');

        switch ($operation) {
            case '+': $result = $a + $b; break;
            case '-': $result = $a - $b; break;
            case '*': $result = $a * $b; break;
            case '/': $result = $a / $b; break;
            default: $result = $a + $b;
        }

        return $this->prepareResponse($result);
    }

    /**
     * @Route("/temp-auth-redirect-get", name="temp_auth_get", methods={"GET"})
     */
    public function tempAuthRedirectGet(Request $request)
    {
        return $this->prepareResponse($request->get('code'));
    }

    /**
     * @Route("/temp-auth-redirect-post", name="temp_auth_post", methods={"POST"})
     */
    public function tempAuthRedirectPost(Request $request)
    {
        return $this->prepareResponse($request->get('code'));
    }
}
