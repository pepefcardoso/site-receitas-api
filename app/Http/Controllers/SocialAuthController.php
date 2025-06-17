<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends BaseController
{
    public function redirectToProvider($provider): JsonResponse
    {
        // Garante que o provedor é suportado para evitar erros.
        if (!in_array($provider, ['google', 'github', 'facebook'])) {
            return response()->json(['error' => 'Provedor não suportado.'], 422);
        }

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $url]);
    }

    public function handleProviderCallback($provider): JsonResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $email = $socialUser->getEmail();

            // Lógica robusta para encontrar ou criar o usuário.
            $user = User::firstOrNew(['email' => $email]);

            if ($user->exists) {
                // Usuário existe. Apenas atualiza o provider se estiver vazio.
                if (empty($user->provider)) {
                    $user->provider = $provider;
                    $user->provider_id = $socialUser->getId();
                    $user->save();
                }
            } else {
                // Novo usuário.
                $user->name = $socialUser->getName() ?? $socialUser->getNickname();
                $user->provider = $provider;
                $user->provider_id = $socialUser->getId();
                $user->email_verified_at = now(); // E-mail de provedor social é considerado verificado.
                $user->password = null; // Senha nula para logins sociais.
                $user->save();
            }

            // Permite múltiplas sessões, se desejado. Caso contrário, use: $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => 'Falha na autenticação com o provedor.'], 500);
        }
    }
}
