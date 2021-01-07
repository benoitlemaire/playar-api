@component('mail::message')
    <p>Bonjour {{$freelance->name}} vous venez de créer un compte sur Playar et nous vous en remerciant ! Nos équipe vont anayliser votre profile au plus vite afin d'assurer la qualité de nos freelances auprès des entreprises. Vous pouvez d'ores et déjà consulter la liste des offres mais tant que votre compte se sera pas validé vous ne pourrez pas postuler à celles-ci :</p>
    @component('mail::button', ['url' => 'http://127.0.0.1:8000/offers/create'])
        Voir les offres
    @endcomponent
    <p class="fallback-link">Si vous ne parvenez pas à cliquer sur le bouton, copiez collez ce lien dans dans votre navigateur : {{ 'http://127.0.0.1:8000/offers/create' }} </p>
@endcomponent


