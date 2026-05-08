<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Choix du Rôle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url("/images/Background.png");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(4px);
      animation: backgroundMove 20s infinite alternate linear;
    }

    @keyframes backgroundMove {
      0% { background-position: center; }
      100% { background-position: top; }
    }

    .title {
      font-size: 50px;
      font-family: 'Arial', sans-serif;
      font-weight: bold;
      color: #ffffff;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 2px;
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
      margin-bottom: 40px;
      animation: fadeIn 1.5s ease;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 70px;
      animation: slideUp 1s ease forwards;
    }

    .role-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      position: relative;
    }

    .role-card {
      width: 220px;
      height: 220px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
      background-color: white;
      transition: transform 0.5s ease, box-shadow 0.5s ease;
      position: relative;
    }

    .role-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .role-card:hover img {
      transform: scale(1.1);
    }

    .role-card:hover {
      box-shadow: 0 12px 30px rgba(0,0,0,0.6);
    }

    .role-label {
      margin-top: 18px;
      font-size: 24px;
      font-weight: bold;
      color: #ffffff;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.8);
      animation: fadeIn 2s ease;
    }

    .role-button {
      margin-top: 10px;
      padding: 12px 28px;
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      transition: transform 0.4s, box-shadow 0.4s;
    }

    .role-button:hover {
      transform: translateY(-4px) scale(1.05);
      box-shadow: 0 0 20px #6a11cb, 0 0 40px #2575fc;
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(-20px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      0% { opacity: 0; transform: translateY(60px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .role-card {
        width: 180px;
        height: 180px;
      }

      .role-label {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <div class="title">Bienvenue sur la plateforme</div>

  <div class="container">

    <div class="role-item">
      <div class="role-card">
        <img src="/images/patient.png" alt="Patient">
      </div>
      <div class="role-label">Patient</div>
      <a href="{{ route('login.patient') }}" class="role-button">Accéder</a>
    </div>

    <div class="role-item">
      <div class="role-card">
        <img src="/images/biologiste.png" alt="Biologiste">
      </div>
      <div class="role-label">Biologiste</div>
      <a href="{{ route('login.bio') }}" class="role-button">Accéder</a>
    </div>

    <div class="role-item">
      <div class="role-card">
        <img src="/images/admin.png" alt="Administrateur">
      </div>
      <div class="role-label">Administrateur</div>
      <a href="{{ route('login.admin') }}" class="role-button">Accéder</a>
    </div>

  </div>

</body>
</html>