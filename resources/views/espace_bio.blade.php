<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Espace Biologiste</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-gradient-to-b from-indigo-700 to-purple-800 text-white p-6">
        <h1 class="text-2xl font-bold mb-10">🔬 BioSpace</h1>

        <button onclick="showSection('upload')" class="menu-btn w-full text-left p-3 rounded-xl bg-white/20 mb-3">
            📤 Importer résultat
        </button>

        <button onclick="showSection('historique')" class="menu-btn w-full text-left p-3 rounded-xl hover:bg-white/20 mb-3">
            📚 Historique
        </button>

        <button onclick="showSection('rendezvous')" class="menu-btn w-full text-left p-3 rounded-xl hover:bg-white/20 mb-3">
            📅 Rendez-vous
        </button>

        <a href="{{ route('logout') }}" class="block mt-20 bg-red-500 text-center py-3 rounded-xl font-bold">
            Déconnexion
        </a>
    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-8">

        <div class="mb-8">
            <h2 class="text-4xl font-bold text-slate-800">Espace Biologiste</h2>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-5 shadow">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-5 shadow">
                {{ session('error') }}
            </div>
        @endif

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-3xl shadow">
                <p class="text-slate-500">Patients</p>
                <h3 class="text-3xl font-bold text-indigo-700">{{ $patients->count() }}</h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow">
                <p class="text-slate-500">Résultats</p>
                <h3 class="text-3xl font-bold text-purple-700">{{ isset($resultats) ? $resultats->count() : 0 }}</h3>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow">
                <p class="text-slate-500">Rendez-vous</p>
                <h3 class="text-3xl font-bold text-blue-700">{{ isset($rendezvous) ? $rendezvous->count() : 0 }}</h3>
            </div>
        </div>

        <!-- UPLOAD SECTION -->
        <section id="upload" class="section bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-indigo-700 mb-6">📄 Importer Résultat PDF</h2>

            <form method="POST" action="{{ route('bio.upload.resultat') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="font-semibold">Patient :</label>
                    <select name="patient_id" required class="w-full p-3 border rounded-xl mt-2">
                        <option value="">-- Choisir un patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->nom }} {{ $patient->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-semibold">Fichier PDF :</label>
                    <input type="file" name="fichier_pdf" accept="application/pdf" required class="w-full p-3 border rounded-xl mt-2">
                </div>

                <div>
                    <label class="font-semibold">Commentaire :</label>
                    <textarea name="commentaire" rows="3" class="w-full p-3 border rounded-xl mt-2"></textarea>
                </div>

                <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-bold hover:scale-[1.01] transition">
                    📤 Importer le résultat
                </button>
            </form>
        </section>

        <!-- HISTORIQUE SECTION -->
        <section id="historique" class="section hidden bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-purple-700 mb-6">📚 Historique des résultats</h2>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-3 text-left">Patient</th>
                            <th class="p-3 text-left">Commentaire</th>
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultats ?? [] as $resultat)
                            <tr class="border-b hover:bg-slate-50">
                                <td class="p-3">
                                    {{ $resultat->patient->nom ?? '' }} {{ $resultat->patient->prenom ?? '' }}
                                </td>
                                <td class="p-3">{{ $resultat->commentaire }}</td>
                                <td class="p-3">{{ $resultat->created_at->format('d/m/Y') }}</td>
                                <td class="p-3">
                                    <a href="{{ asset('storage/'.$resultat->fichier_pdf) }}" target="_blank" class="text-indigo-600 font-bold">
                                        Voir PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-5 text-center text-slate-500">
                                    Aucun résultat pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- RENDEZ-VOUS SECTION -->
        <section id="rendezvous" class="section hidden bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-blue-700 mb-6">📅 Liste des rendez-vous</h2>

            <div class="grid gap-4">
                @forelse($rendezvous ?? [] as $rdv)
                    <div class="p-5 rounded-2xl border hover:shadow-md transition">
                        <h3 class="font-bold text-lg text-slate-800">
                            {{ $rdv->patient->nom ?? '' }} {{ $rdv->patient->prenom ?? '' }}
                        </h3>
                        <p class="text-slate-500">📅 {{ $rdv->date_rdv ?? $rdv->date ?? '' }}</p>
                        <p class="text-slate-600 mt-2">{{ $rdv->motif ?? 'Aucun motif' }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-500 p-5">
                        Aucun rendez-vous disponible.
                    </p>
                @endforelse
            </div>
        </section>

    </main>
</div>

<script>
function showSection(id) {
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');
    });

    document.getElementById(id).classList.remove('hidden');

    document.querySelectorAll('.menu-btn').forEach(btn => {
        btn.classList.remove('bg-white/20');
    });

    event.target.classList.add('bg-white/20');
}
</script>

</body>
</html>