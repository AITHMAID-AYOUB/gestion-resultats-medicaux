<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Patient</title>

<style>
body {
  margin:0;
  font-family:'Segoe UI';
  background:linear-gradient(to right,#667eea,#764ba2);
}
.container {
  max-width:800px;
  margin:100px auto;
  background:white;
  padding:30px;
  border-radius:20px;
}
.logout {
  position:absolute;
  top:20px;
  right:20px;
  color:red;
  text-decoration:none;
}
</style>
</head>

<body>

<a href="{{ route('logout') }}" class="logout">Déconnexion</a>

<div class="container">

<h2>Bienvenue {{ session('patient_nom') }} 👋</h2>

@if($resultats->isEmpty())
<p>Aucun résultat.</p>
@else

@foreach($resultats as $res)
<div style="margin-bottom:20px;">
<p><b>Date:</b> {{ $res->date_resultat }}</p>
<p><b>Commentaire:</b> {{ $res->commentaire ?? 'Aucun' }}</p>

<a href="{{ asset($res->fichier_pdf) }}" target="_blank">Voir PDF</a>
</div>
@endforeach

@endif

</div>

</body>
</html>