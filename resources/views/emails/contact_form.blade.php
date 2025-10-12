@extends('emails.base')
@section('body-content')
 <p><strong>Nom :</strong> {{ $content['nom'] ?? '' }}</p>
    <p><strong>Email :</strong> {{ $content['email'] ?? '' }}</p>
    <p><strong>Téléphone :</strong> {{ $content['telephone'] ?? '' }}</p>
    <p><strong>Sujet :</strong> {{ $content['sujet'] ?? '' }}</p>
    <hr>
    <p><strong>Message :</strong></p>
    <p>{!! nl2br(e($content['message'] ?? '')) !!}</p>

    <p class="text-center mt-3">
        Le Ministère du Travail et de la Fonction Publique vous remercie.
    </p>
@endsection
