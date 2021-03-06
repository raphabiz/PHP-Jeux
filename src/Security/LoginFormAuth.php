<?php

namespace App\Security;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuth extends AbstractFormLoginAuthenticator
{
use TargetPathTrait;

private $entityManager;
private $router;
private $csrfTokenManager;
private $passwordEncoder;

public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
{
$this->entityManager = $entityManager;
$this->router = $router;
$this->csrfTokenManager = $csrfTokenManager;
$this->passwordEncoder = $passwordEncoder;
}

public function supports(Request $request)
{
return 'security_login' === $request->attributes->get('_route')
&& $request->isMethod('POST');
}

public function getCredentials(Request $request)
{
$credentials = [
'email' => $request->request->get('email'),
'password' => $request->request->get('password'),
'csrf_token' => $request->request->get('_csrf_token'),
];
$request->getSession()->set(
Security::LAST_USERNAME,
$credentials['email']
);

return $credentials;
}

public function getUser($credentials, UserProviderInterface $userProvider)
{
$token = new CsrfToken('authenticate', $credentials['csrf_token']);
if (!$this->csrfTokenManager->isTokenValid($token)) {
throw new InvalidCsrfTokenException();
}

$user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $credentials['email']]);
$user2 = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $credentials['email']]);

if (!$user && !$user2 ) {
// fail authentication with a custom error
throw new CustomUserMessageAuthenticationException('Votre mail ou pseudo est incorrect');
}
if($user2!=null){
    $user=$user2;
}
return $user;
}

public function checkCredentials($credentials, UserInterface $user)
{
return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
}

public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
{
if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
return new RedirectResponse($targetPath);
}

return new RedirectResponse($this->router->generate('home'));
}

protected function getLoginUrl()
{
return $this->router->generate('security_login');
}
}