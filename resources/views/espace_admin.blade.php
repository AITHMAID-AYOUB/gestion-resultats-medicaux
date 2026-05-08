<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Admin</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f7fb;
}
.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:250px;
    height:100vh;
    background:linear-gradient(180deg,#1e3a8a,#6d28d9);
    color:white;
    padding:25px;
}
.sidebar h2{
    margin-bottom:40px;
}
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    margin:18px 0;
    padding:12px;
    border-radius:10px;
    background:rgba(255,255,255,0.12);
}
.main{
    margin-left:250px;
    padding:30px;
}
.header{
    background:white;
    padding:20px;
    border-radius:16px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    margin-bottom:25px;
}
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:25px;
}
.card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}
.card h3{
    color:#6b7280;
    margin:0;
}
.card p{
    font-size:32px;
    font-weight:bold;
    color:#2563eb;
    margin:10px 0 0;
}
.box{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    margin-bottom:25px;
}
input,select{
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    margin:5px;
}
button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:#2563eb;
    color:white;
    font-weight:bold;
}
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#1e3a8a;
    color:white;
    padding:13px;
}
td{
    padding:13px;
    border-bottom:1px solid #eee;
}
.delete{
    background:#dc2626;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
}
.success{
    color:green;
    font-weight:bold;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>🧪 Admin</h2>
    <a href="{{ route('espace.admin') }}">📊 Dashboard</a>
    <a href="#addBio">➕ Ajouter biologiste</a>
    <a href="{{ route('logout') }}">🚪 Déconnexion</a>
</div>

<div class="main">

    <div class="header">
        <h1>Tableau de bord administrateur</h1>
        <p>Gestion des patients, biologistes et statistiques.</p>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="cards">
        <div class="card">
            <h3>Patients</h3>
            <p>{{ $patientsCount }}</p>
        </div>
        <div class="card">
            <h3>Biologistes</h3>
            <p>{{ $biosCount }}</p>
        </div>
        <div class="card">
            <h3>Total utilisateurs</h3>
            <p>{{ $patientsCount + $biosCount }}</p>
        </div>
    </div>

    <div class="box">
        <h2>📈 Statistiques utilisateurs</h2>
        <canvas id="usersChart" height="100"></canvas>
    </div>

    <div class="box" id="addBio">
        <h2>➕ Ajouter un biologiste</h2>

        <form method="POST" action="{{ route('admin.add.bio') }}">
            @csrf
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Ajouter</button>
        </form>
    </div>

    <div class="box">
        <h2>👥 Liste des utilisateurs</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->nom }}</td>
                    <td>{{ $user->prenom }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <a class="delete"
                           href="{{ route('admin.delete.user', ['role'=>$user->role, 'id'=>$user->id]) }}"
                           onclick="return confirm('Supprimer cet utilisateur ?')">
                           Supprimer
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<script>
new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: ['Patients', 'Biologistes'],
        datasets: [{
            label: 'Nombre',
            data: [{{ $patientsCount }}, {{ $biosCount }}]
        }]
    }
});
</script>

</body>
</html>