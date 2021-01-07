@component('mail::message')
    <p>Bonjour {{$company->name}} vous venez de créer un compte chez Playar.io et nous vous en remerciant ! Vous pouvez d'ores et déjà publier votre première offre ici :</p>
    @component('mail::button', ['url' => 'http://127.0.0.1:8000/offers/create'])
        Poster une offre
    @endcomponent
    <p class="fallback-link">Si vous ne parvenez pas à cliquer sur le bouton, copiez collez ce lien dans dans votre navigateur : {{ 'http://127.0.0.1:8000/offers/create' }} </p>
@endcomponent


