@component('mail::message')
    <p>Bonjour {{$freelance->name}} votre compte vient d'être vérifié bien joué !</p>
    @component('mail::button', ['url' => 'http://127.0.0.1:8000/offers/create'])
        Voir les offres
    @endcomponent
    <p class="fallback-link">Si vous ne parvenez pas à cliquer sur le bouton, copiez collez ce lien dans dans votre navigateur : {{ 'http://127.0.0.1:8000/offers/create' }} </p>
@endcomponent


