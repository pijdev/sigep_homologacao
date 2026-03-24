<?php

namespace App\Services;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials($user, array $credentials)
    {
        $plain = $credentials['password'];

        if (array_key_exists('is_active', $user->getAttributes()) && ! $user->is_active) {
            return false;
        }
        
        // Verificar se usuário tem acesso à unidade selecionada
        if (isset($credentials['unidade_id'])) {
            if (!$user->temAcessoUnidade($credentials['unidade_id'])) {
                return false;
            }
        }
        
        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['login']) || empty($credentials['password'])) {
            return;
        }

        // Remove a senha das credenciais para não buscar por ela
        $credentials = array_filter($credentials, function ($key) {
            return !Str::contains($key, 'password');
        }, ARRAY_FILTER_USE_KEY);

        // Buscar usuário pelo campo 'login'
        $user = $this->createModel()->where('login', $credentials['login'])->first();

        if (!$user) {
            return null;
        }

        // Armazenar o ID da unidade na sessão para validação posterior
        if (isset($credentials['unidade_id'])) {
            session(['unidade_selecionada' => $credentials['unidade_id']]);
        }

        return $user;
    }
}
