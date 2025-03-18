<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">
    <title>HeartHive</title>
</head>

<body>

    <aside>

        <h2>Filtres</h2>

        <div id="filters">
            <form id="filterForm">
                <div>
                    <label for="city">Ville:</label>
                    <input type="text" id="city-search" name="city">
                    <div id="suggestions" class="suggestions"></div>
                </div>
                <div>
                    <label for="distance">Distance (km):</label>
                    <input type="range" id="radius-range" name="distance" min="0" max="8" value="0" step="1">
                    <span id="nb-km">0 km</span>
                </div>
                <div>
                    <label for="sort">Trier par:</label>
                    <select id="sort" name="sort">
                        <option value="closest">Le plus proche</option>
                        <option value="popular">Le plus populaire</option>
                    </select>
                </div>
                <div>
                    <label for="categories">Catégories:</label>
                    <div id="categories">
                        <label><input type="checkbox" name="categories[]" value="category1"> Catégorie 1</label>
                        <label><input type="checkbox" name="categories[]" value="category2"> Catégorie 2</label>
                        <label><input type="checkbox" name="categories[]" value="category3"> Catégorie 3</label>
                    </div>
                </div>
                <button type="button" class="btn" onclick="applyFilters()">Appliquer</button>
            </form>
        </div>

        <script>
            function applyFilters() {
                var formData = new FormData(document.getElementById('filterForm'));
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'filter.php', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.querySelector('main').innerHTML = xhr.responseText;
                    }
                };
                xhr.send(formData);
            }

            // Mise à jour du texte de la distance
            document.getElementById('radius-range').addEventListener('input', function() {
                const radiusValues = [0, 1, 5, 10, 20, 30, 50, 100, 200];
                const index = parseInt(this.value, 10);
                document.getElementById('nb-km').textContent = `${radiusValues[index]} km`;
            });

            // Autocomplétion de la ville
            document.getElementById('city-search').addEventListener('input', async function() {
                const searchTerm = this.value.trim();
                const suggestions = document.getElementById('suggestions');

                if (searchTerm.length > 2) {
                    try {
                        const response = await fetch(`https://geo.api.gouv.fr/communes?nom=${searchTerm}&fields=nom,centre,code&boost=population&limit=5`);
                        const data = await response.json();
                        showSuggestions(data);
                    } catch (error) {
                        console.error("Erreur lors de la récupération des données:", error);
                        hideSuggestions();
                    }
                } else {
                    hideSuggestions();
                }
            });

            function showSuggestions(communes) {
                const suggestions = document.getElementById('suggestions');
                suggestions.innerHTML = communes.map(commune => `
                    <div class="suggestion" data-code="${commune.code}" data-lat="${commune.centre.coordinates[1]}" data-lon="${commune.centre.coordinates[0]}">
                        ${commune.nom.trim()}
                    </div>
                `).join('');

                suggestions.classList.add('active');

                document.querySelectorAll('.suggestion').forEach(item => {
                    item.addEventListener('click', () => {
                        document.getElementById('city-search').value = item.textContent.trim();
                        hideSuggestions();
                    });
                });
            }

            function hideSuggestions() {
                const suggestions = document.getElementById('suggestions');
                suggestions.classList.remove('active');
                suggestions.innerHTML = "";
            }

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#city-search') && !e.target.closest('#suggestions')) {
                    hideSuggestions();
                }
            });
        </script>

    </aside>

    <main>

    </main>

</body>

</html>