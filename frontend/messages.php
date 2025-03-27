<?php
session_start();
include('../backend/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Requête pour récupérer TOUTES les conversations (envoyées ET reçues)
$query = "SELECT 
            other_user.user_id,
            other_user.user_firstname,
            other_user.user_name,
            other_user.user_profile_picture,
            latest_msgs.message_content as last_message,
            latest_msgs.created_at,
            (SELECT COUNT(*) FROM messages 
             WHERE sender_id = other_user.user_id 
             AND receiver_id = ? 
             AND is_read = 0) as unread_count
          FROM
            (SELECT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id
                    WHEN receiver_id = ? THEN sender_id
                END AS other_user_id,
                MAX(message_id) as latest_message_id
             FROM messages
             WHERE sender_id = ? OR receiver_id = ?
             GROUP BY other_user_id) AS conv
          JOIN user AS other_user ON other_user.user_id = conv.other_user_id
          JOIN messages AS latest_msgs ON latest_msgs.message_id = conv.latest_message_id
          ORDER BY latest_msgs.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Ajouter un code de débogage après l'exécution de la requête
echo "<!-- NOUVELLE requête: " . count($conversations) . " conversations trouvées -->";

// Ajouter après la ligne $conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Vérifier TOUS les messages pour cet utilisateur
$debug_messages_query = "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC LIMIT 10";
$debug_stmt = $conn->prepare($debug_messages_query);
$debug_stmt->bind_param("ii", $user_id, $user_id);
$debug_stmt->execute();
$debug_messages = $debug_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<!-- Messages existants pour l'utilisateur " . $user_id . ": " . count($debug_messages) . " -->";
foreach ($debug_messages as $msg) {
    echo "<!-- Message " . $msg['message_id'] . " - De: " . $msg['sender_id'] . " À: " . $msg['receiver_id'] . " -->";
}

// Ajoutez du code de débogage temporaire
echo "<!-- Debug conversations: " . count($conversations) . " trouvées -->";
foreach ($conversations as $index => $c) {
    echo "<!-- Conversation " . $index . ": avec user " . $c['user_id'] . " -->";
}

// Si un utilisateur est sélectionné, récupérer les messages
$selected_user = null;
$messages = [];

if (isset($_GET['user']) && is_numeric($_GET['user'])) {
    $selected_user_id = intval($_GET['user']);

    // Récupérer les informations de l'utilisateur sélectionné
    $user_query = "SELECT user_id, user_name, user_profile_picture FROM user WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $selected_user_id);
    $user_stmt->execute();
    $selected_user = $user_stmt->get_result()->fetch_assoc();

    if ($selected_user) {
        // Récupérer les messages entre les deux utilisateurs
        $messages_query = "SELECT 
                            m.*,
                            CASE WHEN m.sender_id = ? THEN 1 ELSE 0 END as is_sent
                           FROM messages m
                           WHERE (m.sender_id = ? AND m.receiver_id = ?)
                           OR (m.sender_id = ? AND m.receiver_id = ?)
                           ORDER BY m.created_at ASC";
        $messages_stmt = $conn->prepare($messages_query);
        $messages_stmt->bind_param("iiiii", $user_id, $user_id, $selected_user_id, $selected_user_id, $user_id);
        $messages_stmt->execute();
        $messages = $messages_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Marquer les messages comme lus
        $update_query = "UPDATE messages SET is_read = 1 
                         WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $selected_user_id, $user_id);
        $update_stmt->execute();
    }
}

// Ajouter ce code après avoir récupéré les messages d'une conversation

if (isset($_GET['user']) && is_numeric($_GET['user'])) {
    $receiver_id = (int)$_GET['user'];

    // Marquer les messages de cette conversation comme lus
    $markRead = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $markRead->bind_param("ii", $receiver_id, $_SESSION['user_id']);
    $markRead->execute();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | HeartHive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-pink': '#ffb3e4',
                        'custom-pink-dark': '#ffd6e2',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <?php include('include/header.php'); ?>

    <div class="container mx-auto px-4 py-8 mt-20">
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="flex h-[800px]">
                <!-- Sidebar des conversations -->
                <div class="w-96 border-r border-gray-200">
                    <!-- En-tête du sidebar -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h1 class="text-xl font-semibold">Messages</h1>
                            <button class="text-custom-pink hover:text-pink-600">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <!-- Barre de recherche -->
                        <div class="mt-4">
                            <div class="relative">
                                <input type="text"
                                    placeholder="Rechercher dans les messages"
                                    class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-custom-pink">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des conversations -->
                    <div class="overflow-y-auto h-[calc(100%-73px)]">
                        <?php if (empty($conversations)): ?>
                            <div class="p-4 text-center text-gray-500">
                                Aucune conversation trouvée.
                            </div>
                        <?php else: ?>
                            <?php foreach ($conversations as $conv):
                                $is_active = isset($_GET['user']) && $_GET['user'] == $conv['user_id'];
                            ?>
                                <a href="?user=<?php echo $conv['user_id']; ?>"
                                    class="block p-4 hover:bg-gray-50 cursor-pointer <?php echo $is_active ? 'bg-custom-pink-dark' : ''; ?>">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <img src="<?php echo $conv['user_profile_picture'] ? $conv['user_profile_picture'] : 'assets/img/default-avatar.png'; ?>"
                                                alt="Profile" class="w-12 h-12 rounded-full object-cover">
                                            <?php if ($conv['unread_count'] > 0): ?>
                                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                                                    <?php echo $conv['unread_count']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-baseline">
                                                <h3 class="text-sm font-semibold text-gray-900">
                                                    <?php echo htmlspecialchars($conv['user_firstname'] . ' ' . $conv['user_name']); ?>
                                                </h3>
                                                <span class="text-xs text-gray-500">
                                                    <?php echo date('H:i', strtotime($conv['created_at'])); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm <?php echo $conv['unread_count'] > 0 ? 'font-semibold text-gray-900' : 'text-gray-500'; ?> truncate">
                                                <?php echo htmlspecialchars($conv['last_message']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Zone de conversation -->
                <div class="flex-1 flex flex-col">
                    <?php if ($selected_user): ?>
                        <!-- En-tête de la conversation -->
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="<?php echo $selected_user['user_profile_picture'] ? $selected_user['user_profile_picture'] : 'assets/img/default-avatar.png'; ?>"
                                        class="w-10 h-10 rounded-full">
                                    <div>
                                        <h2 class="text-sm font-semibold"><?php echo htmlspecialchars($selected_user['user_name']); ?></h2>
                                        <p class="text-xs text-green-500">En ligne</p>
                                    </div>
                                </div>
                                <button class="text-gray-500 hover:text-gray-600">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div id="messageContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                            <?php foreach ($messages as $msg): ?>
                                <?php if ($msg['is_sent']): ?>
                                    <!-- Message envoyé -->
                                    <div class="flex flex-row-reverse items-end space-x-reverse space-x-2">
                                        <div class="max-w-[70%] bg-custom-pink text-white rounded-2xl rounded-br-none p-3">
                                            <p class="text-sm"><?php echo htmlspecialchars($msg['message_content']); ?></p>
                                        </div>
                                        <span class="text-xs text-gray-500"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                    </div>
                                <?php else: ?>
                                    <!-- Message reçu -->
                                    <div class="flex items-end space-x-2">
                                        <img src="<?php echo $selected_user['user_profile_picture'] ? $selected_user['user_profile_picture'] : 'assets/img/default-avatar.png'; ?>" class="w-8 h-8 rounded-full">
                                        <div class="max-w-[70%] bg-gray-100 rounded-2xl rounded-bl-none p-3">
                                            <p class="text-sm"><?php echo htmlspecialchars($msg['message_content']); ?></p>
                                        </div>
                                        <span class="text-xs text-gray-500"><?php echo date('H:i', strtotime($msg['created_at'])); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Zone de saisie -->
                        <div class="p-4 border-t border-gray-200">
                            <form id="messageForm" class="flex items-center space-x-2">
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_user['user_id']; ?>">
                                <button type="button" class="text-gray-500 hover:text-custom-pink transition-colors">
                                    <i class="far fa-image text-xl"></i>
                                </button>
                                <input type="text"
                                    id="messageInput"
                                    name="message"
                                    placeholder="Écrivez votre message..."
                                    class="flex-1 px-4 py-2 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-custom-pink">
                                <button type="submit" class="text-custom-pink hover:text-pink-600 transition-colors">
                                    <i class="fas fa-paper-plane text-xl"></i>
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Aucune conversation sélectionnée -->
                        <div class="flex-1 flex flex-col items-center justify-center">
                            <div class="text-center">
                                <div class="text-gray-400 mb-4">
                                    <i class="fas fa-comment-dots text-6xl"></i>
                                </div>
                                <h2 class="text-xl font-semibold mb-2">Vos messages</h2>
                                <p class="text-gray-500 mb-4">Sélectionnez une conversation pour commencer à discuter</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll to bottom of messages
            const messagesContainer = document.getElementById('messageContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            // Gestion du formulaire d'envoi de message
            const messageForm = document.getElementById('messageForm');
            if (messageForm) {
                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const messageInput = document.getElementById('messageInput');
                    const message = messageInput.value.trim();
                    const receiverId = messageForm.querySelector('input[name="receiver_id"]').value;

                    if (message !== '') {
                        // Préparer les données pour l'envoi
                        const formData = new FormData();
                        formData.append('receiver_id', receiverId);
                        formData.append('message', message);

                        // Créer un message temporaire
                        const tempMessage = document.createElement('div');
                        tempMessage.className = 'flex flex-row-reverse items-end space-x-reverse space-x-2';
                        tempMessage.innerHTML = `
                        <div class="max-w-[70%] bg-custom-pink text-white rounded-2xl rounded-br-none p-3">
                            <p class="text-sm">${escapeHtml(message)}</p>
                        </div>
                        <span class="text-xs text-gray-500">envoi...</span>
                    `;

                        messagesContainer.appendChild(tempMessage);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;

                        // Vider l'input
                        messageInput.value = '';

                        // Envoyer le message au serveur
                        fetch('../backend/send_message.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Mettre à jour l'heure du message temporaire
                                    const timeElement = tempMessage.querySelector('span');
                                    if (timeElement) {
                                        timeElement.textContent = data.data.formatted_time;
                                    }
                                } else {
                                    console.error('Erreur:', data.message);
                                    // Retirer le message temporaire en cas d'erreur
                                    messagesContainer.removeChild(tempMessage);
                                }
                            })
                            .catch(error => {
                                console.error('Erreur:', error);
                                // Retirer le message temporaire en cas d'erreur
                                messagesContainer.removeChild(tempMessage);
                            });
                    }
                });
            }

            // Fonction pour échapper les caractères HTML
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
    <script>
        // Fonction pour vérifier les nouveaux messages
        let lastCheckTimestamp = <?php echo time(); ?>;

        function checkNewMessages() {
            // Ajouter un timestamp aléatoire pour éviter le cache
            const cacheBuster = Math.floor(Math.random() * 10000);

            fetch(`../backend/check_new_conversations.php?last_check=${lastCheckTimestamp}&_=${cacheBuster}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur réseau: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    // Vérifier si le texte est vide
                    if (!text || text.trim() === '') {
                        console.error('Réponse vide du serveur');
                        return;
                    }

                    // Vérifier si le texte retourné est du JSON valide
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            lastCheckTimestamp = data.current_timestamp;

                            if (data.conversations && data.conversations.length > 0) {
                                updateConversationsList(data.conversations);
                            }
                        } else {
                            console.error('Erreur du serveur:', data.message || 'Erreur inconnue');
                        }
                    } catch (e) {
                        console.error('Réponse non JSON:', text.substring(0, 100) + '...');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des messages:', error);
                    // Attendre plus longtemps avant la prochaine tentative en cas d'erreur
                    // Pour éviter de surcharger le serveur en cas de problème
                });
        }

        function updateConversationsList(newConversations) {
            const conversationsContainer = document.querySelector('.overflow-y-auto.h-\\[calc\\(100\\%-73px\\)]');

            if (!conversationsContainer) return;

            // Vérifier si le conteneur affiche "Aucune conversation trouvée"
            const noConversationsElement = conversationsContainer.querySelector('.p-4.text-center.text-gray-500');

            if (noConversationsElement) {
                // S'il n'y a pas de conversations, remplacer par les nouvelles
                conversationsContainer.innerHTML = '';
            }

            newConversations.forEach(conv => {
                // Vérifier si cette conversation existe déjà
                const existingConv = document.querySelector(`a[href="?user=${conv.user_id}"]`);

                if (existingConv) {
                    // Mettre à jour la conversation existante
                    const messagePreview = existingConv.querySelector('p');
                    const timeElement = existingConv.querySelector('.text-xs.text-gray-500');
                    const unreadBadge = existingConv.querySelector('.absolute.-top-1.-right-1');

                    if (messagePreview) messagePreview.textContent = conv.last_message;
                    if (timeElement) timeElement.textContent = conv.formatted_time;

                    // Mettre à jour le badge de messages non lus
                    if (conv.unread_count > 0) {
                        if (unreadBadge) {
                            unreadBadge.textContent = conv.unread_count;
                        } else {
                            const imgContainer = existingConv.querySelector('.relative');
                            if (imgContainer) {
                                const badge = document.createElement('span');
                                badge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full';
                                badge.textContent = conv.unread_count;
                                imgContainer.appendChild(badge);
                            }
                        }
                    }

                    // Déplacer la conversation en haut de la liste
                    conversationsContainer.insertBefore(existingConv, conversationsContainer.firstChild);
                } else {
                    // Créer un nouvel élément pour cette conversation
                    const newConvElement = document.createElement('a');
                    newConvElement.href = `?user=${conv.user_id}`;
                    newConvElement.className = 'block p-4 hover:bg-gray-50 cursor-pointer';

                    newConvElement.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img src="${conv.user_profile_picture}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                            ${conv.unread_count > 0 ? `<span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                                ${conv.unread_count}
                            </span>` : ''}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    ${conv.user_firstname} ${conv.user_name}
                                </h3>
                                <span class="text-xs text-gray-500">
                                    ${conv.formatted_time}
                                </span>
                            </div>
                            <p class="text-sm ${conv.unread_count > 0 ? 'font-semibold text-gray-900' : 'text-gray-500'} truncate">
                                ${conv.last_message}
                            </p>
                        </div>
                    </div>
                `;

                    // Ajouter au début de la liste
                    conversationsContainer.insertBefore(newConvElement, conversationsContainer.firstChild);
                }
            });

            // Jouer un son de notification
            playNotificationSound();
        }

        function playNotificationSound() {
            // Créer un élément audio pour jouer le son de notification
            const audio = new Audio('../assets/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Notification audio failed to play:', e));
        }

        // Vérifier les nouveaux messages toutes les 10 secondes
        document.addEventListener('DOMContentLoaded', function() {
            // On attend 10 secondes avant la première vérification
            setTimeout(() => {
                checkNewMessages();
                // Ensuite, vérifier régulièrement
                setInterval(checkNewMessages, 10000);
            }, 10000);
        });
    </script>
    <script>
        // Remplacer l'initialisation existante à la fin du fichier

        // Vérifier les nouveaux messages
        document.addEventListener('DOMContentLoaded', function() {
            // Première vérification après 5 secondes pour laisser le temps à la page de se charger
            setTimeout(() => {
                checkNewMessages();
                // Puis vérifier régulièrement toutes les 20 secondes
                // (intervalle plus long pour réduire la charge serveur)
                setInterval(checkNewMessages, 20000);
            }, 5000);
        });
    </script>
</body>

</html>