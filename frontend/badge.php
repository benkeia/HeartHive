<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6">Mes récompenses</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="badge-container bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                <img src="assets/icons/badges/Medallions.png" alt="Badge" class="w-64 h-64 mb-4">
                <p class="text-xl font-bold mb-2">Nouveau Bourdon</p>
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-green-500 h-4 rounded-full" style="width: 50%;"></div>
                </div>
                <p class="text-sm text-gray-500">1/2</p>
            </div>


        </div>
    </div>


    <div class="relative w-24 h-24">
        <svg width="89" height="88" viewBox="0 0 89 88" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M37.8727 2.90396C41.4882 -0.862469 47.5118 -0.862468 51.1273 2.90396L51.6834 3.48327C53.9345 5.82838 57.2574 6.80918 60.4212 6.06237L61.186 5.88183C66.2462 4.68735 71.2884 7.92669 72.3055 13.0255L72.4979 13.9902C73.1283 17.1503 75.3683 19.7491 78.4009 20.8386L79.2966 21.1604C84.1525 22.905 86.6196 28.3056 84.7591 33.1182L84.3569 34.1588C83.2034 37.1426 83.6852 40.5116 85.6288 43.0525L86.301 43.9313C89.4291 48.0206 88.5861 53.8819 84.4324 56.924L83.5891 57.5417C81.0012 59.437 79.5894 62.5449 79.8644 65.7408L79.9562 66.8068C80.4002 71.9664 76.4939 76.4732 71.3236 76.7663L70.4699 76.8147C67.2367 76.9981 64.3389 78.8702 62.8427 81.7422L62.4342 82.5264C60.0248 87.1514 54.253 88.8456 49.7261 86.2565L49.0608 85.876C46.235 84.2598 42.765 84.2598 39.9392 85.876L39.2739 86.2565C34.747 88.8456 28.9752 87.1514 26.5658 82.5264L26.1573 81.7422C24.6611 78.8702 21.7633 76.9981 18.5301 76.8147L17.6764 76.7663C12.5061 76.4732 8.59976 71.9664 9.04383 66.8068L9.13557 65.7408C9.41063 62.5449 7.99878 59.437 5.41092 57.5417L4.56755 56.924C0.413878 53.8819 -0.429071 48.0206 2.69899 43.9313L3.37117 43.0525C5.31477 40.5116 5.79661 37.1426 4.64314 34.1588L4.2409 33.1182C2.38044 28.3056 4.84749 22.905 9.70339 21.1604L10.5991 20.8386C13.6317 19.7491 15.8717 17.1503 16.5021 13.9902L16.6945 13.0255C17.7116 7.92669 22.7538 4.68736 27.814 5.88183L28.5788 6.06237C31.7426 6.80919 35.0655 5.82838 37.3166 3.48327L37.8727 2.90396Z" fill="url(#paint0_linear_452_1095)" />
            <path d="M33.0316 26.1128C37.0467 25.788 40.9544 24.6317 44.5 22.7193C48.0456 24.6318 51.9533 25.788 55.9684 26.1128C57.9349 29.7199 60.6455 32.8722 63.9167 35.3568C63.6137 39.5192 64.2105 43.7018 65.6653 47.6133C63.1922 50.9445 61.4586 54.7699 60.5841 58.826C56.7904 60.2799 53.3398 62.5145 50.4611 65.3821C46.5309 64.5596 42.4691 64.5596 38.5389 65.3821C35.6602 62.5145 32.2096 60.2799 28.4159 58.826C27.5414 54.7699 25.8078 50.9445 23.3347 47.6133C24.7895 43.7018 25.3863 39.5192 25.0833 35.3568C28.3545 32.8722 31.0651 29.7199 33.0316 26.1128Z" stroke="#929292" stroke-opacity="0.1" stroke-width="39.7973" />
            <defs>
                <linearGradient id="paint0_linear_452_1095" x1="13.6806" y1="4.89632" x2="65.7876" y2="87.0284" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#D2E0ED" />
                    <stop offset="0.109389" stop-color="#BFC8E7" />
                    <stop offset="0.463563" stop-color="#CBCDFC" />
                    <stop offset="0.739611" stop-color="#C3ADCB" />
                    <stop offset="1" stop-color="#9699B5" />
                </linearGradient>
            </defs>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-full h-full">
                <circle class="text-gray-300" stroke-width="8
" stroke="currentColor" fill="transparent" r="27" cx="44" cy="44" />
                <circle id="progress-circle" class="text-green-500" stroke-width="7" stroke-dasharray="169.56" stroke-dashoffset="169.56" stroke-linecap="round" stroke="currentColor" fill="transparent" r="27" cx="44" cy="44" transform="rotate(-90 44 44)" />
            </svg>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const totalSteps = 50; // Total number of steps
                    const completedSteps = 10; // Number of completed steps
                    const progressCircle = document.getElementById('progress-circle');
                    const circumference = 2 * Math.PI * 27; // 2 * Math.PI * radius

                    function setProgress(percent) {
                        const offset = circumference - (percent / 100) * circumference;
                        progressCircle.style.transition = 'stroke-dashoffset 1s ease-in-out';
                        progressCircle.style.strokeDashoffset = offset;
                    }

                    const progressPercent = (completedSteps / totalSteps) * 100;
                    setTimeout(() => setProgress(progressPercent), 100); // Delay to ensure the transition is visible
                });
            </script>
        </div>
    </div>


</body>

</html>