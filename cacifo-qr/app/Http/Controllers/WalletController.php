<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCard;

class WalletController extends Controller
{
    public function editCard(Request $request, int $id)
    {
        $user = Auth::user();
        $card = UserCard::where('user_id', $user->id)->findOrFail($id);

        $card->update([
            'card_holder'  => $request->card_holder,
            'last_four'    => $request->last_four ?: $card->last_four,
            'mbway_phone'  => $request->mbway_phone ?: $card->mbway_phone,
            'paypal_email' => $request->paypal_email ?: $card->paypal_email,
        ]);

        return back()->with('success', 'Cartão atualizado com sucesso!');
    }

    public function deleteCard(int $id)
    {
        $user = Auth::user();
        $card = UserCard::where('user_id', $user->id)->findOrFail($id);
        $wasDefault = $card->is_default;
        $card->delete();

        // Se era o predefinido, define o próximo como predefinido
        if ($wasDefault) {
            $next = UserCard::where('user_id', $user->id)->first();
            if ($next) $next->update(['is_default' => true]);
        }

        return back()->with('success', 'Cartão eliminado com sucesso!');
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Obtém todos os cartões guardados do utilizador
        $cards = $user->cards()->orderBy('is_default', 'desc')->get();

        return view('wallet.index', compact('user', 'cards'));
    }

    public function addCard(Request $request)
    {
        $request->validate([
            'card_holder' => ['required', 'string', 'max:255'],
            'card_number' => ['required_unless:brand,mbway,paypal', 'nullable', 'digits:16'],
            'brand'       => ['required', 'in:visa,mastercard,mbway,paypal'],
            'mbway_phone' => ['required_if:brand,mbway', 'nullable', 'digits:9'],
            'paypal_email' => ['required_if:brand,paypal', 'nullable', 'email'],
        ]);

        $user = Auth::user();

        // Se for o primeiro cartão adicionado, fica logo marcado como predefinido
        $hasCards = UserCard::where('user_id', $user->id)->exists();

        UserCard::create([
            'user_id'      => $user->id,
            'brand'        => $request->brand,
            'last_four'    => $request->card_number ? substr($request->card_number, -4) : null,
            'card_holder'  => $request->card_holder,
            'mbway_phone'  => $request->mbway_phone,
            'paypal_email' => $request->paypal_email,
            'is_default'   => !$hasCards,
        ]);

        return back()->with('success', 'Cartão adicionado com sucesso!');
    }

    public function setDefaultCard(int $id)
    {
        $user = Auth::user();

        // Remove a predefinição de todos os cartões do utilizador
        UserCard::where('user_id', $user->id)->update(['is_default' => false]);

        // Define o cartão escolhido como o novo predefinido
        $card = UserCard::where('user_id', $user->id)->findOrFail($id);
        $card->update(['is_default' => true]);

        return back()->with('success', 'Cartão predefinido alterado com sucesso.');
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.001'],
            'method' => ['required', 'in:mbway,paypal,visa,mastercard,saved_card'],
            'saved_card_id' => ['required_if:method,saved_card', 'nullable', 'exists:user_cards,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Simulação do carregamento (incrementa na Wallet)
        $user->increment('wallet_balance', $request->amount);

        return back()->with('success', "Carregamento de " . number_format($request->amount, 2) . "€ concluído com sucesso!");
    }
}
