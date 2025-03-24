<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management Table</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
</head>

<body class="bg-gray-50 font-sans">

    <?php include 'include/header.php'; ?>
    <div class="container mx-auto px-4 py-8" x-data="usersTable()">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-2xl font-semibold text-gray-800">Gestion des Utilisateurs</h1>

            <div class="flex items-center gap-2">
                <div class="relative">
                    <button class="p-2 bg-white border border-gray-300 rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>

                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchTerm" placeholder="Rechercher..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500" @input="filterUsers()">
                </div>
            </div>
        </div>

        <!-- Actions en masse -->
        <div class="mb-4 flex items-center gap-2" x-show="hasSelectedUsers()">
            <span class="text-sm text-gray-600" x-text="selectedCount() + ' utilisateur(s) sélectionné(s)'"></span>
            <button @click="bulkAction('Actif')" class="px-3 py-1 bg-green-100 text-green-700 rounded-md text-sm font-medium hover:bg-green-200">
                Accepter
            </button>
            <button @click="bulkAction('Inactif')" class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-sm font-medium hover:bg-red-200">
                Refuser
            </button>
            <button @click="bulkAction('En attente')" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-md text-sm font-medium hover:bg-purple-200">
                En attente
            </button>
        </div>

        <!-- Notifications -->
        <div x-show="notification.show" class="mb-4 p-3 rounded-lg" :class="notification.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
            <span x-text="notification.message"></span>
            <button @click="notification.show = false" class="ml-2 text-gray-500 hover:text-gray-700">
                &times;
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="pl-4 py-3 w-12">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @click="toggleSelectAll()">
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('id')">
                                <div class="flex items-center">
                                    #
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="sortColumn === 'id' && sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        <path x-show="sortColumn === 'id' && sortDirection === 'desc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        <path x-show="sortColumn !== 'id'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('name')">
                                <div class="flex items-center">
                                    Nom
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="sortColumn === 'name' && sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        <path x-show="sortColumn === 'name' && sortDirection === 'desc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        <path x-show="sortColumn !== 'name'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('date')">
                                <div class="flex items-center">
                                    Date d'inscription
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="sortColumn === 'date' && sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        <path x-show="sortColumn === 'date' && sortDirection === 'desc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        <path x-show="sortColumn !== 'date'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('status')">
                                <div class="flex items-center">
                                    Statut
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="sortColumn === 'status' && sortDirection === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        <path x-show="sortColumn === 'status' && sortDirection === 'desc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        <path x-show="sortColumn !== 'status'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Message
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr class="hover:bg-gray-50">
                                <td class="pl-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" x-model="user.selected">
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="user.id"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                    <div class="text-sm text-gray-500" x-text="user.email"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.date"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-purple-100 text-purple-800': user.status === 'En attente',
                                            'bg-green-100 text-green-800': user.status === 'Actif',
                                            'bg-red-100 text-red-800': user.status === 'Inactif',
                                            'bg-yellow-100 text-yellow-800': user.status === 'En pause'
                                        }"
                                        x-text="user.status">
                                    </span>
                                    <span x-show="user.previousStatus" class="ml-2 text-xs text-gray-500">
                                        (était: <span x-text="user.previousStatus"></span>)
                                    </span>
                                </td>
                                <!-- Colonne de messages -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="openMessageModal(user)" class="p-2 bg-blue-100 rounded-lg hover:bg-blue-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                    <!-- Pour les utilisateurs en attente: boutons accepter et refuser -->
                                    <template x-if="user.status === 'En attente'">
                                        <div class="flex space-x-2">
                                            <!-- Bouton accepter -->
                                            <button class="p-2 bg-green-100 rounded-lg hover:bg-green-200"
                                                @click="changeStatus(user, 'Actif')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <!-- Bouton refuser -->
                                            <button class="p-2 bg-red-100 rounded-lg hover:bg-red-200"
                                                @click="changeStatus(user, 'Inactif')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Pour les utilisateurs acceptés ou refusés: bouton retour en attente -->
                                    <template x-if="user.status === 'Actif' || user.status === 'Inactif'">
                                        <button class="p-2 bg-purple-100 rounded-lg hover:bg-purple-200"
                                            @click="changeStatus(user, 'En attente')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </template>

                                    <!-- Bouton annuler la dernière action -->
                                    <button x-show="user.previousStatus"
                                        class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200"
                                        @click="undoStatusChange(user)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Affichage de <span class="font-medium" x-text="filteredUsers.length"></span> utilisateurs sur <span class="font-medium" x-text="users.length"></span> au total
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Précédent</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                3
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Suivant</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de message -->
    <div x-show="messageModal.show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.away="messageModal.show = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" x-text="'Message à ' + messageModal.userName"></h3>
                <button @click="messageModal.show = false" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label for="messageSubject" class="block text-sm font-medium text-gray-700">Sujet</label>
                <input type="text" id="messageSubject" x-model="messageModal.subject" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="messageContent" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea id="messageContent" x-model="messageModal.content" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>
            <div class="flex justify-end">
                <button @click="messageModal.show = false" class="mr-2 px-4 py-2 bg-gray-100 rounded-md text-gray-600 hover:bg-gray-200">
                    Annuler
                </button>
                <button @click="sendMessage()" class="px-4 py-2 bg-blue-600 rounded-md text-white hover:bg-blue-700">
                    Envoyer
                </button>
            </div>
        </div>
    </div>

    <script>
        function usersTable() {
            return {
                users: [{
                        id: 1,
                        name: 'Nom Prénom',
                        email: 'email@exemple.fr',
                        date: '03/09/2024',
                        status: 'En attente',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 2,
                        name: 'Dupont Marie',
                        email: 'marie@exemple.fr',
                        date: '15/08/2024',
                        status: 'Actif',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 3,
                        name: 'Martin Jean',
                        email: 'jean@exemple.fr',
                        date: '27/07/2024',
                        status: 'Inactif',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 4,
                        name: 'Bernard Sophie',
                        email: 'sophie@exemple.fr',
                        date: '10/09/2024',
                        status: 'En attente',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 5,
                        name: 'Petit Thomas',
                        email: 'thomas@exemple.fr',
                        date: '05/09/2024',
                        status: 'Actif',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 6,
                        name: 'Robert Camille',
                        email: 'camille@exemple.fr',
                        date: '22/08/2024',
                        status: 'En pause',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 7,
                        name: 'Richard Léa',
                        email: 'lea@exemple.fr',
                        date: '18/09/2024',
                        status: 'Actif',
                        previousStatus: null,
                        selected: false
                    },
                    {
                        id: 8,
                        name: 'Durand Lucas',
                        email: 'lucas@exemple.fr',
                        date: '01/09/2024',
                        status: 'Inactif',
                        previousStatus: null,
                        selected: false
                    }
                ],
                filteredUsers: [],
                searchTerm: '',
                sortColumn: 'id',
                sortDirection: 'asc',
                selectAll: false,
                notification: {
                    show: false,
                    message: '',
                    type: 'success'
                },
                messageModal: {
                    show: false,
                    userId: null,
                    userName: '',
                    subject: '',
                    content: ''
                },

                init() {
                    this.filteredUsers = [...this.users];
                    this.sortUsers();
                },

                toggleSelectAll() {
                    this.selectAll = !this.selectAll;
                    this.filteredUsers.forEach(user => user.selected = this.selectAll);
                },

                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                    this.sortUsers();
                },

                sortUsers() {
                    const direction = this.sortDirection === 'asc' ? 1 : -1;
                    this.filteredUsers.sort((a, b) => {
                        if (a[this.sortColumn] < b[this.sortColumn]) return -1 * direction;
                        if (a[this.sortColumn] > b[this.sortColumn]) return 1 * direction;
                        return 0;
                    });
                },

                filterUsers() {
                    const term = this.searchTerm.toLowerCase();
                    this.filteredUsers = this.users.filter(user =>
                        user.id.toString().includes(term) ||
                        user.name.toLowerCase().includes(term) ||
                        user.email.toLowerCase().includes(term) ||
                        user.date.toLowerCase().includes(term) ||
                        user.status.toLowerCase().includes(term)
                    );
                    this.sortUsers();
                },

                hasSelectedUsers() {
                    return this.users.some(user => user.selected);
                },

                selectedCount() {
                    return this.users.filter(user => user.selected).length;
                },

                changeStatus(user, newStatus) {
                    // Ne rien faire si déjà dans cet état
                    if (user.status === newStatus) {
                        return;
                    }

                    // Enregistrer l'état précédent pour permettre l'annulation
                    user.previousStatus = user.status;
                    user.status = newStatus;

                    let message = '';
                    if (newStatus === 'Actif') {
                        message = 'La candidature a été acceptée';
                    } else if (newStatus === 'Inactif') {
                        message = 'La candidature a été refusée';
                    } else if (newStatus === 'En attente') {
                        message = 'La candidature a été remise en attente';
                    }

                    // Afficher la notification
                    this.showNotification(message, 'success');

                    // Dans un environnement réel, appel à l'API pour sauvegarder
                    // saveUserStatusToServer(user.id, newStatus);
                },

                undoStatusChange(user) {
                    if (user.previousStatus) {
                        const tempStatus = user.status;
                        user.status = user.previousStatus;
                        user.previousStatus = null;

                        this.showNotification('Modification annulée', 'success');

                        // Dans un environnement réel, appel à l'API pour sauvegarder
                        // saveUserStatusToServer(user.id, user.status);
                    }
                },

                bulkAction(newStatus) {
                    const selectedUsers = this.users.filter(user => user.selected);
                    let count = 0;

                    selectedUsers.forEach(user => {
                        if (user.status !== newStatus) {
                            user.previousStatus = user.status;
                            user.status = newStatus;
                            count++;
                        }
                    });

                    if (count > 0) {
                        this.showNotification(`${count} utilisateur(s) modifié(s) avec succès`, 'success');
                        // Dans un environnement réel, appel à l'API pour sauvegarder
                        // bulkSaveUserStatusToServer(selectedUsers);
                    }
                },

                showNotification(message, type = 'success') {
                    this.notification.message = message;
                    this.notification.type = type;
                    this.notification.show = true;

                    // Masquer après 3 secondes
                    setTimeout(() => {
                        this.notification.show = false;
                    }, 3000);
                },

                openMessageModal(user) {
                    this.messageModal.show = true;
                    this.messageModal.userId = user.id;
                    this.messageModal.userName = user.name;
                    this.messageModal.subject = '';
                    this.messageModal.content = '';
                },

                sendMessage() {
                    // Simuler l'envoi d'un message
                    if (this.messageModal.subject.trim() === '' || this.messageModal.content.trim() === '') {
                        this.showNotification('Veuillez remplir tous les champs du message', 'error');
                        return;
                    }

                    // Dans un environnement réel, appel à l'API pour envoyer le message
                    // sendMessageToServer(this.messageModal.userId, this.messageModal.subject, this.messageModal.content);

                    this.showNotification('Message envoyé avec succès', 'success');
                    this.messageModal.show = false;
                }
            }
        }
    </script>
</body>

</html>