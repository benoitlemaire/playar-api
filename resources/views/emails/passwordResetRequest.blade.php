@component('mail::message')
    <p>Bonjour {{$user->name}} :wave: Vous avez demandé un nouveau mot passe ! Il vous suffit de cliquer sur le bouton pour le changer et en choisir un nouveau :</p>
    @component('mail::button', ['url' => 'http://127.0.0.1:8000/api/auth/password-reset?token='.$token])
        Nouveau mot de passe
    @endcomponent
    <p class="fallback-link">Si vous ne parvenez pas à cliquer sur le bouton, copiez collez ce lien dans dans votre navigateur : {{ 'http://127.0.0.1:8000/api/auth/password-reset?token='.$token }} </p>
@endcomponent
