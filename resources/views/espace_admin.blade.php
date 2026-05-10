<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:#f4f7fb;
    font-family:'Segoe UI',sans-serif;
}
.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:270px;
    height:100vh;
    background:linear-gradient(180deg,#1e3a8a,#6d28d9);
    color:white;
    padding:25px;
}
.sidebar button,.sidebar a{
    width:100%;
    display:block;
    color:white;
    text-decoration:none;
    border:0;
    padding:13px 15px;
    border-radius:14px;
    margin-bottom:12px;
    background:rgba(255,255,255,.15);
    text-align:left;
}
.sidebar button:hover,.sidebar a:hover{
    background:rgba(255,255,255,.28);
}
.main{
    margin-left:270px;
    padding:30px;
}
.card-custom{
    border:0;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}
.section{display:none}
.section.active{display:block}
.table th{
    background:#1e3a8a;
    color:white;
}
</style>
</head>

<body>

<div class="sidebar">
    <h3 class="fw-bold mb-4">🧪 Admin</h3>

    <button onclick="showSection('dashboard')">
        <i class="bi bi-speedometer2"></i> Dashboard
    </button>

    <button onclick="showSection('addBio')">
        <i class="bi bi-person-plus"></i> Ajouter biologiste
    </button>

    <button onclick="showSection('rendezvous')">
        <i class="bi bi-calendar-check"></i> Rendez-vous
    </button>

    <a href="{{ route('logout') }}" class="bg-danger mt-5">
        <i class="bi bi-box-arrow-right"></i> Déconnexion
    </a>
</div>

<div class="main">

@if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
@endif

<!-- DASHBOARD -->
<section id="dashboard" class="section active">

    <div class="card card-custom p-4 mb-4">
        <h1 class="fw-bold">Tableau de bord administrateur</h1>
        <p class="text-muted mb-0">Statistiques et gestion des utilisateurs.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-4">
                <p class="text-muted mb-1">Patients</p>
                <h1 class="fw-bold text-primary">{{ $patientsCount }}</h1>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4">
                <p class="text-muted mb-1">Biologistes</p>
                <h1 class="fw-bold text-success">{{ $biosCount }}</h1>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4">
                <p class="text-muted mb-1">Total utilisateurs</p>
                <h1 class="fw-bold text-warning">{{ $patientsCount + $biosCount }}</h1>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h3 class="fw-bold mb-3">📊 Statistiques utilisateurs</h3>
        <canvas id="usersChart" height="90"></canvas>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h3 class="fw-bold mb-4">👥 Liste des patients</h3>

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th class="text-center">Choix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->id }}</td>
                    <td>{{ $patient->nom }}</td>
                    <td>{{ $patient->prenom }}</td>
                    <td>{{ $patient->email }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.delete.user', ['role'=>'patient', 'id'=>$patient->id]) }}"
                           class="btn btn-danger btn-sm rounded-4"
                           onclick="return confirm('Voulez-vous vraiment supprimer ce patient ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card card-custom p-4 mb-4">
        <h3 class="fw-bold mb-4">🧬 Liste des biologistes</h3>

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th class="text-center">Choix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($biologistes as $bio)
                <tr>
                    <td>{{ $bio->id }}</td>
                    <td>{{ $bio->nom }}</td>
                    <td>{{ $bio->prenom }}</td>
                    <td>{{ $bio->email }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.delete.user', ['role'=>'biologiste', 'id'=>$bio->id]) }}"
                           class="btn btn-danger btn-sm rounded-4"
                           onclick="return confirm('Voulez-vous vraiment supprimer ce biologiste ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</section>

<!-- AJOUT BIO -->
<section id="addBio" class="section">
    <div class="card card-custom p-4">
        <h3 class="fw-bold mb-4">➕ Ajouter un biologiste</h3>

        <form method="POST" action="{{ route('admin.add.bio') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="nom" class="form-control form-control-lg rounded-4" placeholder="Nom" required>
                </div>

                <div class="col-md-6">
                    <input type="text" name="prenom" class="form-control form-control-lg rounded-4" placeholder="Prénom" required>
                </div>

                <div class="col-md-6">
                    <input type="email" name="email" class="form-control form-control-lg rounded-4" placeholder="Email" required>
                </div>

                <div class="col-md-6">
                    <input type="password" name="password" class="form-control form-control-lg rounded-4" placeholder="Mot de passe" required>
                </div>
            </div>

            <button class="btn btn-primary btn-lg rounded-4 mt-4">
                Ajouter biologiste
            </button>
        </form>
    </div>
</section>

<!-- RENDEZ-VOUS -->
<section id="rendezvous" class="section">
    <div class="card card-custom p-4">
        <h3 class="fw-bold mb-4">📅 Gestion des rendez-vous</h3>

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th class="text-center">Choix</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rendezvous as $rdv)
                <tr>
                    <td>{{ $rdv->nom }} {{ $rdv->prenom }}</td>
                    <td>{{ $rdv->date_rdv }}</td>
                    <td>{{ $rdv->heure_rdv }}</td>
                    <td>{{ $rdv->motif }}</td>
                    <td>
                        @if($rdv->statut == 'accepté')
                            <span class="badge bg-success">Accepté</span>
                        @elseif($rdv->statut == 'refusé')
                            <span class="badge bg-danger">Refusé</span>
                        @else
                            <span class="badge bg-warning text-dark">En attente</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.rdv.statut', $rdv->id) }}" class="d-flex gap-2">
                            @csrf

                            <select name="statut" class="form-select form-select-sm rounded-4">
                                <option value="en_attente">En attente</option>
                                <option value="accepté">Accepté</option>
                                <option value="refusé">Refusé</option>
                            </select>

                            <button class="btn btn-primary btn-sm rounded-4"
                                    onclick="return confirm('Modifier le statut de ce rendez-vous ?');">
                                Modifier
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Aucun rendez-vous.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

</div>

<script>
function showSection(id){
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });

    document.getElementById(id).classList.add('active');
}

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