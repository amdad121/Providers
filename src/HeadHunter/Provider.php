<?php

namespace SocialiteProviders\HeadHunter;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'HEADHUNTER';

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['user_agent'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://hh.ru/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://hh.ru/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.hh.ru/me', [
            RequestOptions::HEADERS => [
                'User-Agent'    => $this->getConfig('user_agent'),
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['email'],
            'name'     => trim($user['last_name'].' '.$user['first_name']),
            'email'    => $user['email'],
            'avatar'   => null,
        ]);
    }
}
