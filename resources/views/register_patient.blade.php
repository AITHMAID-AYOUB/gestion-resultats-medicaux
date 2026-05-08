<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Inscription Patient</title>

<style>
body {
  margin:0;
  font-family:'Segoe UI';
  background-image:url("/images/patient.png");
  background-size:cover;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
  backdrop-filter:blur(5px);
}

.box {
  background:rgba(255,255,255,0.7);
  padding:25px;
  border-radius:20px;
  width:380px;
  text-align:center;
}

input {
  width:90%;
  padding:12px;
  margin-bottom:12px;
  border-radius:30px;
  border:1px solid #ccc;
}

button {
  width:100%;
  padding:12px;
  border:none;
  border-radius:30px;
  background:linear-gradient(90deg,#6a11cb,#2575fc);
  color:white;
  font-weight:bold;
}

.back {
  position:absolute;
  top:20px;
  left:20px;
  background:#6a11cb;
  color:white;
  padding:8px 15px;
  border-radius:20px;
  text-decoration:none;
}
</style>
</head>

<body>

<a href="{{ route('home') }}" class="back">← Accueil</a>

<form class="box" method="POST" action="{{ route('register.patient.post') }}">
@csrf

<h2>Inscription Patient</h2>

@if(session('error'))
<div style="color:red">{{ session('error') }}</div>
@endif

@if(session('success'))
<div style="color:green">{{ session('success') }}</div>
@endif

@if ($errors->any())
<div style="color:red; background:white; padding:10px; border-radius:10px; margin-bottom:10px;">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<input type="text" name="nom" placeholder="Nom" required>
<input type="text" name="prenom" placeholder="Prénom" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="cin" placeholder="CIN" required>
<input type="text" name="telephone" placeholder="Téléphone" required>
<input type="password" name="password" placeholder="Mot de passe" required>
<input type="password" name="password_confirmation" placeholder="Confirmer mot de passe" required>

<button type="submit">S'inscrire</button>

<div style="margin-top:10px;">
Déjà inscrit ? <a href="{{ route('login.patient') }}">Login</a>
</div>

</form>

</body>
</html>