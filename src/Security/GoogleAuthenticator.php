<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\AuthIdentity;
use App\Entity\Enum\AuthProvider;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * @psalm-suppress UnusedClass Instantiated by Symfony's security firewall via
 *     custom_authenticators/entry_point in config/packages/security.yaml, not
 *     referenced directly from application code.
 */
class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
    ) {
    }

    #[\Override]
    public function supports(Request $request): ?bool
    {
        return 'connect_google_check' === $request->attributes->get('_route');
    }

    #[\Override]
    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client): User {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                return $this->resolveUser($googleUser);
            }),
        );
    }

    private function resolveUser(GoogleUser $googleUser): User
    {
        $providerUserId = (string) $googleUser->getId();

        $authIdentityRepository = $this->entityManager->getRepository(AuthIdentity::class);

        $existingIdentity = $authIdentityRepository->findOneBy([
            'provider' => AuthProvider::Google,
            'providerUserId' => $providerUserId,
        ]);

        if (null !== $existingIdentity) {
            $existingIdentity->setLastLoginAt();
            $this->entityManager->flush();

            return $existingIdentity->getUser();
        }

        $user = new User($googleUser->getName(), $this->deriveInitials($googleUser->getName()));
        $authIdentity = new AuthIdentity($user, AuthProvider::Google);
        $authIdentity->setProviderUserId($providerUserId);
        $authIdentity->setEmail($googleUser->getEmail());
        $authIdentity->setLastLoginAt();

        $this->entityManager->persist($user);
        $this->entityManager->persist($authIdentity);
        $this->entityManager->flush();

        return $user;
    }

    private function deriveInitials(string $name): string
    {
        $words = array_values(array_filter(explode(' ', trim($name))));

        if ([] === $words) {
            return '?';
        }

        if (1 === count($words)) {
            return mb_strtoupper(mb_substr($words[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[count($words) - 1], 0, 1));
    }

    #[\Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_me'));
    }

    #[\Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            Response::HTTP_FORBIDDEN,
        );
    }

    #[\Override]
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
