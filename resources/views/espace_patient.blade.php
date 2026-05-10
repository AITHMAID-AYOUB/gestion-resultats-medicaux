<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Patient</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f4f7fb;
    font-family:'Segoe UI',sans-serif;
}
.sidebar{
    min-height:100vh;
    background:linear-gradient(180deg,#2563eb,#7c3aed);
    color:white;
    padding:25px;
}
.sidebar button,.sidebar a{
    width:100%;
    margin-bottom:12px;
    border:0;
    border-radius:14px;
    padding:13px;
    color:white;
    background:rgba(255,255,255,.15);
    text-align:left;
    text-decoration:none;
}
.sidebar button:hover,.sidebar a:hover{
    background:rgba(255,255,255,.25);
}
.card-custom{
    border:0;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}
.section{display:none}
.section.active{display:block}
</style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 sidebar">
            <h3 class="fw-bold mb-4"><i class="bi bi-person-heart"></i> Patient</h3>

            <button onclick="showSection('accueil')">
                <i class="bi bi-house"></i> Accueil
            </button>

            <button onclick="showSection('reserver')">
                <i class="bi bi-calendar-plus"></i> Réserver RDV
            </button>

            <button onclick="showSection('historique')">
                <i class="bi bi-file-medical"></i> Historique analyses
            </button>

            <button onclick="showSection('mesrdv')">
                <i class="bi bi-calendar-check"></i> Mes RDV
            </button>

            <a href="{{ route('logout') }}" class="d-block bg-danger mt-5">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </div>

        <div class="col-md-9 col-lg-10 p-4">

            @if(session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            <section id="accueil" class="section active">
                <div class="card card-custom p-4 mb-4">
                    <h2 class="fw-bold">Bienvenue {{ session('patient_nom') }} 👋</h2>
                    <p class="text-muted mb-0">Depuis cet espace, vous pouvez réserver un rendez-vous et consulter vos résultats d’analyses.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <p class="text-muted">Résultats disponibles</p>
                            <h1 class="fw-bold text-primary">{{ $resultats->count() }}</h1>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <p class="text-muted">Rendez-vous</p>
                            <h1 class="fw-bold text-success">{{ $rendezvous->count() }}</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section id="reserver" class="section">
                <div class="card card-custom p-4">
                    <h3 class="fw-bold mb-4"><i class="bi bi-calendar-plus text-primary"></i> Réserver un rendez-vous</h3>

                    <form method="POST" action="{{ route('patient.reserver.rdv') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date du rendez-vous</label>
                               <input type="date" name="date_rdv" id="date_rdv" class="form-control form-control-lg rounded-4" min="{{ date('Y-m-d') }}"
                                max="{{ date('Y-m-d', strtotime('+20 days')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Heure</label>
                                <select name="heure_rdv" id="heure_rdv" class="form-control form-control-lg rounded-4" required>
                                  <option value="">Choisir une date d'abord</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Motif</label>
                                <textarea name="motif" class="form-control rounded-4" rows="4" placeholder="Ex: contrôle, analyse sanguine..."></textarea>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg rounded-4 mt-4 px-5">
                            <i class="bi bi-send"></i> Réserver
                        </button>
                    </form>
                </div>
            </section>

            <section id="historique" class="section">
                <div class="card card-custom p-4">
                    <h3 class="fw-bold mb-4"><i class="bi bi-file-earmark-medical text-danger"></i> Historique des analyses</h3>

                    @if($resultats->isEmpty())
                        <div class="alert alert-info rounded-4">Aucun résultat pour le moment.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Date</th>
                                        <th>Commentaire</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resultats as $res)
                                        <tr>
                                            <td>{{ $res->date_resultat ?? $res->created_at ?? '' }}</td>
                                            <td>{{ $res->commentaire ?? 'Aucun commentaire' }}</td>
                                            <td>
                                                <a href="{{ asset($res->fichier_pdf) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-4">
                                                  Voir PDF
                                                </a>

                                                <a href="{{ asset($res->fichier_pdf) }}" 
                                                  download
                                                  class="btn btn-sm btn-success rounded-4">
                                                  Télécharger
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            <section id="mesrdv" class="section">
                <div class="card card-custom p-4">
                    <h3 class="fw-bold mb-4"><i class="bi bi-calendar-check text-success"></i> Mes rendez-vous</h3>

                    @forelse($rendezvous as $rdv)
                        <div class="border rounded-4 p-3 mb-3 bg-light">
                            <h5 class="fw-bold mb-1">{{ $rdv->date_rdv }} à {{ $rdv->heure_rdv }}</h5>
                            <p class="mb-1 text-muted">{{ $rdv->motif ?? 'Aucun motif' }}</p>

                            @if($rdv->statut == 'accepté')
                                <span class="badge bg-success">Accepté</span>
                            @elseif($rdv->statut == 'refusé')
                                <span class="badge bg-danger">Refusé</span>
                            @else
                                <span class="badge bg-warning text-dark">En attente</span>
                            @endif
                            <a href="{{ route('patient.rdv.annuler', $rdv->id) }}"
                                onclick="return confirm('Voulez-vous vraiment annuler ce rendez-vous ?')"
                                class="btn btn-danger btn-sm"> Annuler
                            </a>
                        </div>
                    @empty
                        <div class="alert alert-info rounded-4">Aucun rendez-vous réservé.</div>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</div>

<script>
function showSection(id){
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
}
</script>
<script>
document.getElementById('date_rdv').addEventListener('change', function () {

    let date = this.value;
    let heureSelect = document.getElementById('heure_rdv');

    heureSelect.innerHTML = '<option>Chargement...</option>';

    fetch('/patient/available-slots?date=' + date)
        .then(response => response.json())
        .then(slots => {

            heureSelect.innerHTML = '';

            if (slots.length === 0) {
                heureSelect.innerHTML = '<option>Aucun créneau disponible</option>';
                return;
            }

            heureSelect.innerHTML = '<option value="">Choisir une heure</option>';

            slots.forEach(slot => {
                heureSelect.innerHTML += `
                    <option value="${slot}">
                        ${slot}
                    </option>
                `;
            });

        });

});
</script>
</body>
</html>