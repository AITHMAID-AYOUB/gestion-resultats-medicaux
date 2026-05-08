<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion Biologiste</title>

<style>
body {
  margin:0;
  font-family:'Segoe UI';
  background-image:url("/images/biologiste.png");
  background-size:cover;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
  backdrop-filter:blur(5px);
}
.login-box {
  background:rgba(255,255,255,0.7);
  padding:40px;
  border-radius:20px;
  width:350px;
  text-align:center;
}
input {
  width:90%;
  padding:12px;
  margin-bottom:15px;
  border-radius:30px;
  border:1px solid #ccc;
}
button {
  width:100%;
  padding:12px;
  border-radius:30px;
  border:none;
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

<form class="login-box" method="POST" action="{{ route('login.bio.post') }}">
@csrf

<h2>Bienvenue Biologiste</h2>

@if(session('error'))
<div style="color:red">{{ session('error') }}</div>
@endif

<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Mot de passe" required>

<button type="submit">Se connecter</button>

</form>

</body>
</html>