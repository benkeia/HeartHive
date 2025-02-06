<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="css/signup.css">
</head>

<body>
    <div class="signup-container">

        <form action="../backend/signup.php">
            <div class="signup-header">
                <h1>S'inscrire</h1>
            </div>


            <div class="pagination">

                <div class="number">
                    1
                </div>
                <div class="bar"></div>
                <div class="number">
                    2
                </div>
                <div class="bar"></div>
                <div class="number">
                    3
                </div>
            </div>
            <div class="steps">
                <div class="step">
                    <h2>Bienvenue !</h2>
                    <p>Avec HeartHive, trouvez du sens, faites des rencontres et engagez-vous dans des missions solidaires près de chez vous ! </p>

                    <label for="surname">Nom</label>
                    <input type="text" id="surname" name="surname" required>

                    <label for="name">Prénom</label>
                    <input type="text" id="name" name="name" required>

                    <label for="birth">Date de naissance</label>
                    <input type="date" id="birth" name="birth" required>

                    <label for="city">Ville de résidence</label>
                    <input type="text" id="city" name="city" required>





                </div>
                <div class="step">


                    <label for="profile_picture">Photo de profil</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>

                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>

                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>




                </div>
                <div class="step"></div>


            </div>

            <div class="signup-footer">
                <button class="previous" disabled>Précédent</button>
                <button class="next" disabled>Suivant</button>
            </div>






        </form>

    </div>
</body>

</html>